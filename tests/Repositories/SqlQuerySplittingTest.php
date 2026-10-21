<?php

namespace Cuonggt\Dibi\Tests\Repositories;

use Cuonggt\Dibi\Repositories\MysqlDatabaseRepository;
use Cuonggt\Dibi\Tests\Fakes\FakeConnection;
use PHPUnit\Framework\TestCase;

/**
 * Splitting a script into statements is shared by every driver, so it is
 * exercised here through one of them against a connection that is never used.
 */
class SqlQuerySplittingTest extends TestCase
{
    /**
     * @return MysqlDatabaseRepository
     */
    protected function repository()
    {
        return new MysqlDatabaseRepository(new FakeConnection);
    }

    public function test_it_splits_on_a_semicolon(): void
    {
        $this->assertSame(
            ['SELECT 1', 'SELECT 2'],
            $this->repository()->splitSqlQuery('SELECT 1;SELECT 2')
        );
    }

    public function test_it_returns_a_single_statement_unchanged(): void
    {
        $this->assertSame(
            ['SELECT * FROM users'],
            $this->repository()->splitSqlQuery('SELECT * FROM users')
        );
    }

    public function test_it_drops_blank_statements(): void
    {
        $this->assertSame(
            ['SELECT 1'],
            $this->repository()->splitSqlQuery('SELECT 1;')
        );

        $this->assertSame(
            [' SELECT 1 '],
            $this->repository()->splitSqlQuery(';; SELECT 1 ;  ;')
        );
    }

    public function test_it_keeps_a_semicolon_inside_a_string_literal(): void
    {
        $this->assertSame(
            ["SELECT ';' AS s"],
            $this->repository()->splitSqlQuery("SELECT ';' AS s")
        );
    }

    public function test_it_splits_around_string_literals_containing_semicolons(): void
    {
        $this->assertSame(
            ["SELECT ';' AS a", " SELECT ';;' AS b"],
            $this->repository()->splitSqlQuery("SELECT ';' AS a; SELECT ';;' AS b")
        );
    }

    public function test_it_treats_a_doubled_quote_as_an_escaped_quote(): void
    {
        $this->assertSame(
            ["SELECT 'it''s; here' AS s"],
            $this->repository()->splitSqlQuery("SELECT 'it''s; here' AS s")
        );
    }

    public function test_it_treats_a_backslash_as_an_escape_inside_a_string(): void
    {
        $this->assertSame(
            ["SELECT 'a\\'; b' AS s"],
            $this->repository()->splitSqlQuery("SELECT 'a\\'; b' AS s")
        );
    }

    public function test_it_respects_every_supported_identifier_quote(): void
    {
        $queries = [
            'double quotes' => 'SELECT 1 AS "we;ird"',
            'mysql backticks' => 'SELECT 1 AS `ba;ck`',
            'sql server brackets' => 'SELECT 1 AS [bra;cket]',
        ];

        foreach ($queries as $label => $query) {
            $this->assertSame(
                [$query],
                $this->repository()->splitSqlQuery($query),
                "A semicolon inside {$label} should not split the statement."
            );
        }
    }

    public function test_a_doubled_closing_bracket_does_not_end_an_identifier(): void
    {
        $this->assertSame(
            ['SELECT 1 AS [odd]]; name]'],
            $this->repository()->splitSqlQuery('SELECT 1 AS [odd]]; name]')
        );
    }

    public function test_it_ignores_a_semicolon_in_a_line_comment(): void
    {
        $this->assertSame(
            ["SELECT 1 -- ; not a split\n", 'SELECT 2'],
            $this->repository()->splitSqlQuery("SELECT 1 -- ; not a split\n;SELECT 2")
        );
    }

    public function test_it_ignores_a_semicolon_in_a_hash_comment(): void
    {
        $this->assertSame(
            ["SELECT 1 # ; nope\n", 'SELECT 2'],
            $this->repository()->splitSqlQuery("SELECT 1 # ; nope\n;SELECT 2")
        );
    }

    public function test_it_ignores_a_semicolon_in_a_block_comment(): void
    {
        $this->assertSame(
            ['SELECT /* ; */ 1', ' SELECT 2'],
            $this->repository()->splitSqlQuery('SELECT /* ; */ 1; SELECT 2')
        );
    }

    public function test_it_keeps_an_unterminated_quote_as_one_statement(): void
    {
        $this->assertSame(
            ["SELECT 'oops; still going"],
            $this->repository()->splitSqlQuery("SELECT 'oops; still going")
        );
    }

    public function test_it_keeps_an_unterminated_block_comment_as_one_statement(): void
    {
        $this->assertSame(
            ['SELECT 1 /* oops; still going'],
            $this->repository()->splitSqlQuery('SELECT 1 /* oops; still going')
        );
    }

    public function test_it_returns_nothing_for_an_empty_script(): void
    {
        $this->assertSame([], $this->repository()->splitSqlQuery(''));
        $this->assertSame([], $this->repository()->splitSqlQuery("  \n "));
        $this->assertSame([], $this->repository()->splitSqlQuery(';'));
    }
}
