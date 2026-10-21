<?php

namespace Cuonggt\Dibi\Tests\Controller;

use Cuonggt\Dibi\Dibi;
use Cuonggt\Dibi\InformationSchema;
use Cuonggt\Dibi\Tests\FeatureTestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeControllerTest extends FeatureTestCase
{
    /**
     * The variables the controller hands the frontend.
     *
     * @return array
     */
    protected function scriptVariables($uri = '/dibi')
    {
        return $this->get($uri)->assertOk()->viewData('dibiScriptVariables');
    }

    /**
     * The variables as the rendered page actually embeds them.
     *
     * @return array
     */
    protected function embeddedVariables($uri = '/dibi')
    {
        $html = $this->get($uri)->assertOk()->getContent();

        $this->assertMatchesRegularExpression(
            '/window\.Dibi = /',
            $html,
            'The page should assign the frontend variables to window.Dibi.'
        );

        preg_match('/window\.Dibi = (.+?);\s*<\/script>/s', $html, $matches);

        return json_decode($matches[1], true);
    }

    public function test_it_renders_the_single_page_application(): void
    {
        $this->get('/dibi')->assertOk()->assertViewIs('dibi::app');
    }

    public function test_it_serves_the_application_for_any_nested_path(): void
    {
        foreach (['/dibi/tables/package_tests', '/dibi/sql-query', '/dibi/anything/at/all'] as $uri) {
            $this->get($uri)->assertOk()->assertViewIs('dibi::app');
        }
    }

    public function test_it_tells_the_frontend_where_dibi_is_served_from(): void
    {
        $this->assertSame('/dibi', $this->scriptVariables()['path']);
    }

    public function test_it_tells_the_frontend_the_current_database_name(): void
    {
        $this->assertSame(
            DB::connection()->getDatabaseName(),
            $this->scriptVariables()['database']
        );
    }

    public function test_it_tells_the_frontend_the_available_connections(): void
    {
        $variables = $this->scriptVariables();

        $this->assertSame(['sqlite'], $variables['databaseConnections']);
        $this->assertSame('sqlite', $variables['currentDatabaseConnection']);
    }

    public function test_it_honours_a_connection_selected_earlier(): void
    {
        config(['database.connections.sqlite_other' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]]);

        Dibi::registerDatabaseConnections(['sqlite', 'sqlite_other']);

        Cache::put('dibiConnection', 'sqlite_other');

        $variables = $this->scriptVariables();

        $this->assertSame(['sqlite', 'sqlite_other'], $variables['databaseConnections']);
        $this->assertSame('sqlite_other', $variables['currentDatabaseConnection']);
    }

    public function test_it_passes_an_information_schema_to_the_view(): void
    {
        $variables = $this->scriptVariables();

        $this->assertInstanceOf(InformationSchema::class, $variables['informationSchema']);
        $this->assertNotEmpty($variables['informationSchema']->tables);
    }

    public function test_the_rendered_page_embeds_the_variables_as_json(): void
    {
        $embedded = $this->embeddedVariables();

        $this->assertSame('/dibi', $embedded['path']);
        $this->assertSame(DB::connection()->getDatabaseName(), $embedded['database']);
        $this->assertSame(['sqlite'], $embedded['databaseConnections']);
        $this->assertArrayHasKey('informationSchema', $embedded);
    }

    public function test_the_embedded_information_schema_describes_each_table(): void
    {
        DB::statement('CREATE INDEX package_tests_name_index ON package_tests (name)');

        $tables = collect($this->embeddedVariables()['informationSchema']['tables'])
            ->keyBy('tableName');

        $this->assertArrayHasKey('package_tests', $tables);

        $table = $tables['package_tests'];

        $this->assertSame('BASE TABLE', $table['tableType']);
        $this->assertContains(
            'name',
            collect($table['columns'])->pluck('columnName')->all()
        );
        $this->assertContains(
            'package_tests_name_index',
            collect($table['indexes'])->pluck('indexName')->all()
        );
    }

    public function test_the_embedded_columns_carry_the_fields_the_frontend_reads(): void
    {
        $tables = collect($this->embeddedVariables()['informationSchema']['tables'])
            ->keyBy('tableName');

        $column = collect($tables['package_tests']['columns'])
            ->firstWhere('columnName', 'name');

        foreach (['columnName', 'dataType', 'isNullable', 'isStringDataType', 'ordinalPosition'] as $field) {
            $this->assertArrayHasKey($field, $column, "The frontend reads {$field} from each column.");
        }
    }

    public function test_the_embedded_indexes_carry_the_mapped_fields_and_nothing_else(): void
    {
        DB::statement('CREATE INDEX package_tests_name_index ON package_tests (name)');

        $tables = collect($this->embeddedVariables()['informationSchema']['tables'])
            ->keyBy('tableName');

        $index = collect($tables['package_tests']['indexes'])
            ->firstWhere('indexName', 'package_tests_name_index');

        $expected = [
            'tableCatalog', 'tableSchema', 'tableName', 'nonUnique',
            'indexSchema', 'indexName', 'seqInIndex', 'columnName', 'indexType',
        ];

        $this->assertSame($expected, array_keys($index));

        $this->assertArrayNotHasKey(
            'raw',
            $index,
            'The raw row duplicates every mapped field, so it should not be sent to the browser.'
        );
    }

    public function test_it_requires_authorization(): void
    {
        Dibi::auth(function () {
            return false;
        });

        $this->get('/dibi')->assertForbidden();

        Dibi::auth(function () {
            return true;
        });
    }
}
