<?php

namespace Cuonggt\Dibi\Tests\Repositories;

use Cuonggt\Dibi\Repositories\SqliteDatabaseRepository;
use Cuonggt\Dibi\Tests\FeatureTestCase;
use Illuminate\Support\Facades\DB;

/**
 * The suite already runs on SQLite, so this driver is exercised against a real
 * database rather than canned rows.
 */
class SqliteDatabaseRepositoryTest extends FeatureTestCase
{
    /**
     * @return SqliteDatabaseRepository
     */
    protected function repository()
    {
        return new SqliteDatabaseRepository(DB::connection());
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    protected function tablesByName()
    {
        return collect($this->repository()->tables())->keyBy('tableName');
    }

    public function test_it_takes_the_schema_name_from_the_connection(): void
    {
        $this->assertSame(
            DB::connection()->getDatabaseName(),
            $this->repository()->getName()
        );
    }

    public function test_it_lists_tables_sorted_by_name(): void
    {
        DB::statement('CREATE TABLE zebra (id INTEGER)');
        DB::statement('CREATE TABLE alpha (id INTEGER)');

        $names = collect($this->repository()->tables())->pluck('tableName')->all();

        $sorted = $names;
        sort($sorted);

        $this->assertSame($sorted, $names);
        $this->assertContains('alpha', $names);
        $this->assertContains('zebra', $names);
    }

    public function test_it_excludes_sqlite_internal_tables(): void
    {
        DB::statement('CREATE TABLE counted (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT)');
        DB::table('counted')->insert(['name' => 'forces sqlite_sequence to exist']);

        $this->assertNotEmpty(
            DB::select("SELECT name FROM sqlite_master WHERE name = 'sqlite_sequence'"),
            'This test is only meaningful once SQLite has created its internal table.'
        );

        $names = collect($this->repository()->tables())->pluck('tableName')->all();

        $this->assertNotContains('sqlite_sequence', $names);
    }

    public function test_it_reports_a_view_with_its_own_table_type(): void
    {
        DB::statement('CREATE VIEW named_packages AS SELECT name FROM package_tests');

        $tables = $this->tablesByName();

        $this->assertSame('VIEW', $tables['named_packages']->tableType);
        $this->assertSame('BASE TABLE', $tables['package_tests']->tableType);
    }

    public function test_it_numbers_columns_from_one(): void
    {
        DB::statement('CREATE TABLE ordered (first TEXT, second TEXT, third TEXT)');

        $columns = collect($this->repository()->columns())
            ->where('tableName', 'ordered')
            ->keyBy('columnName');

        $this->assertSame(1, $columns['first']->ordinalPosition);
        $this->assertSame(2, $columns['second']->ordinalPosition);
        $this->assertSame(3, $columns['third']->ordinalPosition);
    }

    public function test_it_reads_nullability_and_defaults(): void
    {
        DB::statement("CREATE TABLE constrained (required TEXT NOT NULL, optional TEXT, defaulted TEXT DEFAULT 'fallback')");

        $columns = collect($this->repository()->columns())
            ->where('tableName', 'constrained')
            ->keyBy('columnName');

        $this->assertFalse($columns['required']->isNullable);
        $this->assertTrue($columns['optional']->isNullable);
        $this->assertNull($columns['optional']->columnDefault);
        $this->assertSame("'fallback'", $columns['defaulted']->columnDefault);
    }

    public function test_it_lowercases_the_declared_data_type(): void
    {
        DB::statement('CREATE TABLE shouty (id INTEGER, label VARCHAR(50))');

        $columns = collect($this->repository()->columns())
            ->where('tableName', 'shouty')
            ->keyBy('columnName');

        $this->assertSame('integer', $columns['id']->dataType);
        $this->assertSame('varchar(50)', $columns['label']->dataType);
    }

    public function test_it_defaults_a_column_declared_without_a_type_to_text(): void
    {
        DB::statement('CREATE TABLE untyped (anything)');

        $column = collect($this->repository()->columns())
            ->firstWhere('tableName', 'untyped');

        $this->assertSame('text', $column->dataType);
        $this->assertTrue($column->isStringDataType);
    }

    public function test_it_flags_sqlite_string_data_types(): void
    {
        DB::statement('CREATE TABLE typed (a TEXT, b VARCHAR, c CHAR, d CLOB, e INTEGER, f REAL, g BLOB, h NUMERIC)');

        $columns = collect($this->repository()->columns())
            ->where('tableName', 'typed')
            ->keyBy('columnName');

        foreach (['a', 'b', 'c', 'd'] as $name) {
            $this->assertTrue(
                $columns[$name]->isStringDataType,
                "{$columns[$name]->dataType} should be a string data type."
            );
        }

        foreach (['e', 'f', 'g', 'h'] as $name) {
            $this->assertFalse(
                $columns[$name]->isStringDataType,
                "{$columns[$name]->dataType} should not be a string data type."
            );
        }
    }

    public function test_it_reports_a_primary_key_as_a_primary_index(): void
    {
        DB::statement('CREATE TABLE keyed (code TEXT NOT NULL, name TEXT, PRIMARY KEY (code))');

        $index = collect($this->repository()->indexes())
            ->firstWhere('tableName', 'keyed');

        $this->assertNotNull($index, 'A primary key should be reported as an index.');
        $this->assertSame('PRIMARY', $index->indexType);
        $this->assertFalse($index->nonUnique);
        $this->assertSame('code', $index->columnName);
    }

    public function test_an_integer_primary_key_has_no_index_of_its_own(): void
    {
        DB::statement('CREATE TABLE rowid_keyed (id INTEGER PRIMARY KEY, name TEXT)');

        $this->assertNull(
            collect($this->repository()->indexes())->firstWhere('tableName', 'rowid_keyed'),
            'An INTEGER PRIMARY KEY is an alias for the rowid, so SQLite creates no index for it.'
        );

        $this->assertNull($this->tablesByName()['rowid_keyed']->indexes());
    }

    public function test_it_distinguishes_unique_from_non_unique_indexes(): void
    {
        DB::statement('CREATE TABLE indexed (email TEXT, nickname TEXT)');
        DB::statement('CREATE UNIQUE INDEX indexed_email_unique ON indexed (email)');
        DB::statement('CREATE INDEX indexed_nickname_index ON indexed (nickname)');

        $indexes = collect($this->repository()->indexes())
            ->where('tableName', 'indexed')
            ->keyBy('indexName');

        $this->assertFalse($indexes['indexed_email_unique']->nonUnique);
        $this->assertTrue($indexes['indexed_nickname_index']->nonUnique);
        $this->assertSame('BTREE', $indexes['indexed_nickname_index']->indexType);
    }

    public function test_it_numbers_the_members_of_a_multi_column_index_from_one(): void
    {
        DB::statement('CREATE TABLE composite (a TEXT, b TEXT)');
        DB::statement('CREATE INDEX composite_index ON composite (a, b)');

        $members = collect($this->repository()->indexes())
            ->where('indexName', 'composite_index')
            ->sortBy('seqInIndex')
            ->values();

        $this->assertCount(2, $members);
        $this->assertSame(1, $members[0]->seqInIndex);
        $this->assertSame('a', $members[0]->columnName);
        $this->assertSame(2, $members[1]->seqInIndex);
        $this->assertSame('b', $members[1]->columnName);
    }

    public function test_it_attaches_columns_and_indexes_to_their_own_table(): void
    {
        DB::statement('CREATE TABLE owner (id INTEGER, label TEXT)');
        DB::statement('CREATE INDEX owner_label_index ON owner (label)');
        DB::statement('CREATE TABLE bystander (other TEXT)');

        $tables = $this->tablesByName();

        $this->assertSame(
            ['id', 'label'],
            collect($tables['owner']->columns())->pluck('columnName')->all()
        );
        $this->assertSame('owner_label_index', $tables['owner']->indexes()[0]->indexName);

        $this->assertSame(
            ['other'],
            collect($tables['bystander']->columns())->pluck('columnName')->all()
        );
        $this->assertNull(
            $tables['bystander']->indexes(),
            'A table with no indexes should not borrow another table\'s.'
        );
    }

    public function test_it_describes_a_table_with_no_rows(): void
    {
        DB::statement('CREATE TABLE empty_table (id INTEGER)');

        $tables = $this->tablesByName();

        $this->assertCount(1, $tables['empty_table']->columns());
        $this->assertSame(DB::connection()->getDatabaseName(), $tables['empty_table']->tableSchema);
        $this->assertNull($tables['empty_table']->tableCatalog);
    }
}
