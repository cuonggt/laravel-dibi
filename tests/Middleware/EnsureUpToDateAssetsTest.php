<?php

namespace Cuonggt\Dibi\Tests\Middleware;

use Cuonggt\Dibi\Http\Middleware\EnsureUpToDateAssets;
use Cuonggt\Dibi\Tests\FeatureTestCase;
use Illuminate\Http\Request;
use ReflectionObject;
use RuntimeException;

class EnsureUpToDateAssetsTest extends FeatureTestCase
{
    /**
     * A directory standing in for the consumer's public directory.
     *
     * @var string
     */
    protected $publicPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->publicPath = sys_get_temp_dir().'/dibi-assets-'.uniqid();

        mkdir($this->publicPath, 0777, true);

        $this->app->usePublicPath($this->publicPath);
    }

    protected function tearDown(): void
    {
        $this->deleteDirectory($this->publicPath);

        parent::tearDown();
    }

    /**
     * Remove a directory and everything beneath it.
     */
    protected function deleteDirectory($directory)
    {
        if (! is_dir($directory)) {
            return;
        }

        foreach (array_diff(scandir($directory), ['.', '..']) as $entry) {
            $path = $directory.'/'.$entry;

            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }

        rmdir($directory);
    }

    /**
     * The middleware only runs its checks outside the console, and the test
     * suite itself runs in the console, so this flips the cached answer.
     */
    protected function pretendNotRunningInConsole()
    {
        $property = (new ReflectionObject($this->app))->getProperty('isRunningInConsole');

        $property->setAccessible(true);
        $property->setValue($this->app, false);
    }

    /**
     * Write a manifest into the stand-in public directory.
     */
    protected function publishManifest($contents)
    {
        mkdir($this->publicPath.'/vendor/dibi/.vite', 0777, true);

        file_put_contents($this->publicPath.'/vendor/dibi/.vite/manifest.json', $contents);
    }

    /**
     * The manifest this package ships, which published assets must match.
     *
     * @return string
     */
    protected function packageManifest()
    {
        return file_get_contents(__DIR__.'/../../public/.vite/manifest.json');
    }

    /**
     * @return mixed
     */
    protected function handle()
    {
        return (new EnsureUpToDateAssets)->handle(Request::create('/dibi'), function () {
            return 'reached the application';
        });
    }

    public function test_it_skips_the_check_in_the_console(): void
    {
        $this->assertTrue(
            $this->app->runningInConsole(),
            'The suite runs in the console, which is why these checks never fire during tests.'
        );

        $this->assertSame('reached the application', $this->handle());
    }

    public function test_it_rejects_a_request_when_the_assets_were_never_published(): void
    {
        $this->pretendNotRunningInConsole();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The Dibi assets are not published. Please run: `php artisan dibi:install`.');

        $this->handle();
    }

    public function test_it_rejects_a_request_when_the_published_assets_are_stale(): void
    {
        $this->pretendNotRunningInConsole();

        $this->publishManifest('{"resources/js/app.js":{"file":"assets/app-OUTDATED.js"}}');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The published Dibi assets are not up-to-date with the installed version.');

        $this->handle();
    }

    public function test_it_allows_a_request_when_the_published_assets_match(): void
    {
        $this->pretendNotRunningInConsole();

        $this->publishManifest($this->packageManifest());

        $this->assertSame('reached the application', $this->handle());
    }

    public function test_it_compares_the_whole_manifest_not_just_its_presence(): void
    {
        $this->pretendNotRunningInConsole();

        $this->publishManifest($this->packageManifest()."\n");

        $this->expectException(RuntimeException::class);

        $this->handle();
    }

    public function test_the_default_middleware_stack_checks_the_assets(): void
    {
        $this->assertContains(EnsureUpToDateAssets::class, config('dibi.middleware'));
    }
}
