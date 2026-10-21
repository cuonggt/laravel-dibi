<?php

namespace Cuonggt\Dibi\Tests\Controller;

use Cuonggt\Dibi\Tests\FeatureTestCase;
use Illuminate\Support\Facades\DB;

class SqlQueryControllerTest extends FeatureTestCase
{
    /**
     * Run a query through the endpoint and return the decoded results.
     *
     * @return array
     */
    protected function run_query($sqlQuery)
    {
        return $this->post('/dibi/api/sql-query', ['sql_query' => $sqlQuery])
            ->assertOk()
            ->json('results');
    }

    public function test_it_runs_a_select_and_returns_the_rows(): void
    {
        DB::table('package_tests')->insert(['id' => 1, 'name' => 'foo']);

        $results = $this->run_query('SELECT * FROM package_tests');

        $this->assertCount(1, $results);
        $this->assertSame('select', $results[0]['statement']);
        $this->assertSame('SELECT * FROM package_tests', $results[0]['query']);
        $this->assertSame('foo', $results[0]['result'][0]['name']);
    }

    public function test_it_reports_an_empty_result_set_for_a_select_matching_nothing(): void
    {
        $results = $this->run_query('SELECT * FROM package_tests');

        $this->assertSame('select', $results[0]['statement']);
        $this->assertSame([], $results[0]['result']);
    }

    public function test_it_runs_an_insert(): void
    {
        $results = $this->run_query("INSERT INTO package_tests (id, name) VALUES (1, 'bar')");

        $this->assertSame('insert', $results[0]['statement']);
        $this->assertSame('bar', DB::table('package_tests')->find(1)->name);
    }

    public function test_it_runs_an_update(): void
    {
        DB::table('package_tests')->insert(['id' => 1, 'name' => 'foo']);

        $results = $this->run_query("UPDATE package_tests SET name = 'changed' WHERE id = 1");

        $this->assertSame('update', $results[0]['statement']);
        $this->assertSame('changed', DB::table('package_tests')->find(1)->name);
    }

    public function test_it_runs_a_delete(): void
    {
        DB::table('package_tests')->insert(['id' => 1, 'name' => 'foo']);

        $results = $this->run_query('DELETE FROM package_tests WHERE id = 1');

        $this->assertSame('delete', $results[0]['statement']);
        $this->assertSame(0, DB::table('package_tests')->count());
    }

    public function test_it_classifies_anything_else_as_a_bare_statement(): void
    {
        $results = $this->run_query('CREATE TABLE statement_test (id INTEGER)');

        $this->assertSame('statement', $results[0]['statement']);
    }

    public function test_it_classifies_the_statement_regardless_of_keyword_case(): void
    {
        DB::table('package_tests')->insert(['id' => 1, 'name' => 'foo']);

        foreach (['select', 'SELECT', 'SeLeCt'] as $keyword) {
            $results = $this->run_query($keyword.' * FROM package_tests');

            $this->assertSame(
                'select',
                $results[0]['statement'],
                "{$keyword} should be recognised as a select."
            );
        }
    }

    public function test_it_runs_each_statement_separated_by_a_semicolon(): void
    {
        $results = $this->run_query(
            "INSERT INTO package_tests (id, name) VALUES (1, 'one');".
            "INSERT INTO package_tests (id, name) VALUES (2, 'two')"
        );

        $this->assertCount(2, $results);
        $this->assertSame('insert', $results[0]['statement']);
        $this->assertSame('insert', $results[1]['statement']);
        $this->assertSame(2, DB::table('package_tests')->count());
    }

    public function test_it_ignores_an_empty_trailing_statement(): void
    {
        $results = $this->run_query('SELECT * FROM package_tests;');

        $this->assertCount(1, $results);
        $this->assertSame('select', $results[0]['statement']);
    }

    public function test_it_returns_the_query_it_ran_alongside_each_result(): void
    {
        $results = $this->run_query('SELECT 1 AS one');

        $this->assertArrayHasKey('query', $results[0]);
        $this->assertArrayHasKey('statement', $results[0]);
        $this->assertArrayHasKey('result', $results[0]);
    }

    public function test_it_recognises_a_select_followed_by_any_whitespace(): void
    {
        DB::table('package_tests')->insert(['id' => 1, 'name' => 'foo']);

        foreach (["SELECT\t* FROM package_tests", "SELECT\n* FROM package_tests"] as $sql) {
            $results = $this->run_query($sql);

            $this->assertSame('select', $results[0]['statement']);
            $this->assertSame('foo', $results[0]['result'][0]['name']);
        }
    }

    public function test_it_recognises_every_statement_in_a_multi_statement_query(): void
    {
        DB::table('package_tests')->insert(['id' => 1, 'name' => 'foo']);

        $results = $this->run_query("SELECT id FROM package_tests;\nSELECT name FROM package_tests");

        $this->assertCount(2, $results);
        $this->assertSame('select', $results[0]['statement']);
        $this->assertSame(
            'select',
            $results[1]['statement'],
            'A statement after the first is preceded by the whitespace left by the split.'
        );
        $this->assertSame('foo', $results[1]['result'][0]['name']);
    }

    public function test_it_treats_a_common_table_expression_as_a_select(): void
    {
        DB::table('package_tests')->insert(['id' => 1, 'name' => 'foo']);

        $results = $this->run_query(
            'WITH recent AS (SELECT * FROM package_tests) SELECT name FROM recent'
        );

        $this->assertSame('select', $results[0]['statement']);
        $this->assertSame('foo', $results[0]['result'][0]['name']);
    }

    public function test_it_skips_comments_before_the_first_keyword(): void
    {
        DB::table('package_tests')->insert(['id' => 1, 'name' => 'foo']);

        $queries = [
            'line comment' => "-- a note\nSELECT * FROM package_tests",
            'block comment' => '/* a note */ SELECT * FROM package_tests',
            'multi-line block comment' => "/* a\n note */\nSELECT * FROM package_tests",
            'several comments' => "-- one\n-- two\nSELECT * FROM package_tests",
        ];

        foreach ($queries as $label => $sql) {
            $results = $this->run_query($sql);

            $this->assertSame('select', $results[0]['statement'], "The {$label} should be skipped.");
            $this->assertSame('foo', $results[0]['result'][0]['name']);
        }
    }

    public function test_it_still_classifies_an_unknown_leading_keyword_as_a_statement(): void
    {
        $results = $this->run_query("-- make a table\nCREATE TABLE comment_test (id INTEGER)");

        $this->assertSame('statement', $results[0]['statement']);
    }

    public function test_it_returns_a_json_array_when_a_leading_fragment_is_empty(): void
    {
        $results = $this->run_query(';SELECT 1 AS one');

        $this->assertCount(1, $results);
        $this->assertSame('select', $results[0]['statement']);
    }

    public function test_it_ignores_whitespace_only_fragments(): void
    {
        $results = $this->run_query("SELECT 1 AS one;\n \n");

        $this->assertCount(1, $results);
        $this->assertSame('select', $results[0]['statement']);
    }

    public function test_it_runs_a_query_whose_string_literal_contains_a_semicolon(): void
    {
        $results = $this->run_query("SELECT ';' AS separator");

        $this->assertCount(1, $results);
        $this->assertSame('select', $results[0]['statement']);
        $this->assertSame(';', $results[0]['result'][0]['separator']);
    }

    public function test_it_separates_statements_whose_literals_contain_semicolons(): void
    {
        $results = $this->run_query("SELECT ';' AS a; SELECT ';;' AS b");

        $this->assertCount(2, $results);
        $this->assertSame(';', $results[0]['result'][0]['a']);
        $this->assertSame(';;', $results[1]['result'][0]['b']);
    }

    public function test_it_requires_authorization(): void
    {
        \Cuonggt\Dibi\Dibi::auth(function () {
            return false;
        });

        $this->post('/dibi/api/sql-query', ['sql_query' => 'SELECT 1'])->assertForbidden();

        \Cuonggt\Dibi\Dibi::auth(function () {
            return true;
        });
    }
}
