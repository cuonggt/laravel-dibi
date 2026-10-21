<?php

namespace Cuonggt\Dibi\Tests\Repositories;

use Cuonggt\Dibi\Repositories\SqlsrvDatabaseRepository;
use Cuonggt\Dibi\TableIndex;
use Cuonggt\Dibi\Tests\Fakes\FakeConnection;
use PHPUnit\Framework\TestCase;

class SqlsrvDatabaseRepositoryTest extends TestCase
{
    /**
     * A row as the sys catalog query returns it, aliased to the shared shape.
     *
     * @return object
     */
    protected function rawIndex(array $overrides = [])
    {
        return (object) array_merge([
            'TABLE_CATALOG' => 'dibi_testing',
            'TABLE_SCHEMA' => 'dbo',
            'TABLE_NAME' => 'users',
            'NON_UNIQUE' => 0,
            'INDEX_SCHEMA' => 'dbo',
            'INDEX_NAME' => 'PK_users',
            'SEQ_IN_INDEX' => 1,
            'COLUMN_NAME' => 'id',
            'INDEX_TYPE' => 'CLUSTERED',
        ], $overrides);
    }

    /**
     * @return object
     */
    protected function rawTable(array $overrides = [])
    {
        return (object) array_merge([
            'TABLE_CATALOG' => 'dibi_testing',
            'TABLE_SCHEMA' => 'dbo',
            'TABLE_NAME' => 'users',
            'TABLE_TYPE' => 'BASE TABLE',
        ], $overrides);
    }

    /**
     * @return object
     */
    protected function rawColumn(array $overrides = [])
    {
        return (object) array_merge([
            'TABLE_CATALOG' => 'dibi_testing',
            'TABLE_SCHEMA' => 'dbo',
            'TABLE_NAME' => 'users',
            'COLUMN_NAME' => 'id',
            'ORDINAL_POSITION' => 1,
            'COLUMN_DEFAULT' => null,
            'IS_NULLABLE' => 'NO',
            'DATA_TYPE' => 'int',
            'CHARACTER_MAXIMUM_LENGTH' => null,
            'CHARACTER_OCTET_LENGTH' => null,
            'NUMERIC_PRECISION' => 10,
            'NUMERIC_SCALE' => 0,
            'DATETIME_PRECISION' => null,
            'CHARACTER_SET_NAME' => null,
            'COLLATION_NAME' => null,
        ], $overrides);
    }

    /**
     * @return array{0: SqlsrvDatabaseRepository, 1: FakeConnection}
     */
    protected function repository(array $rows = [])
    {
        $connection = new FakeConnection($rows);

        return [new SqlsrvDatabaseRepository($connection), $connection];
    }

    public function test_it_reads_indexes_from_the_sys_catalog_views(): void
    {
        [$repository, $connection] = $this->repository([
            'sys.indexes' => [$this->rawIndex()],
        ]);

        $repository->indexes();

        $query = $connection->queryMatching('sys.indexes');

        $this->assertNotNull($query, 'The repository should query sys.indexes.');
        $this->assertStringContainsString('sys.index_columns', $query['query']);
        $this->assertStringContainsString('sys.columns', $query['query']);
        $this->assertStringContainsString('sys.schemas', $query['query']);
    }

    public function test_it_excludes_heaps_which_have_no_index_name(): void
    {
        [$repository, $connection] = $this->repository([
            'sys.indexes' => [$this->rawIndex()],
        ]);

        $repository->indexes();

        $this->assertStringContainsString(
            'i.name IS NOT NULL',
            $connection->queryMatching('sys.indexes')['query'],
            'Heaps carry a null index name and should not be reported as indexes.'
        );
    }

    public function test_the_index_query_takes_no_binding_because_sys_views_are_database_scoped(): void
    {
        [$repository, $connection] = $this->repository([
            'sys.indexes' => [$this->rawIndex()],
        ]);

        $repository->indexes();

        $this->assertSame([], $connection->queryMatching('sys.indexes')['bindings']);
    }

    public function test_it_maps_a_raw_index_row_onto_an_index_object(): void
    {
        [$repository] = $this->repository([
            'sys.indexes' => [$this->rawIndex([
                'INDEX_NAME' => 'IX_users_email',
                'SEQ_IN_INDEX' => 2,
                'COLUMN_NAME' => 'email',
                'INDEX_TYPE' => 'NONCLUSTERED',
            ])],
        ]);

        $index = $repository->indexes()->first();

        $this->assertInstanceOf(TableIndex::class, $index);
        $this->assertSame('dibi_testing', $index->tableCatalog);
        $this->assertSame('dbo', $index->tableSchema);
        $this->assertSame('dbo', $index->indexSchema);
        $this->assertSame('IX_users_email', $index->indexName);
        $this->assertSame(2, $index->seqInIndex);
        $this->assertSame('email', $index->columnName);
        $this->assertSame('NONCLUSTERED', $index->indexType);
    }

    public function test_it_qualifies_the_table_name_with_its_schema(): void
    {
        [$repository] = $this->repository([
            'sys.indexes' => [$this->rawIndex(['TABLE_SCHEMA' => 'sales', 'TABLE_NAME' => 'orders'])],
        ]);

        $this->assertSame('sales.orders', $repository->indexes()->first()->tableName);
    }

    public function test_it_converts_non_unique_to_a_boolean(): void
    {
        [$repository] = $this->repository([
            'sys.indexes' => [
                $this->rawIndex(['INDEX_NAME' => 'PK_users', 'NON_UNIQUE' => 0]),
                $this->rawIndex(['INDEX_NAME' => 'IX_users_name', 'NON_UNIQUE' => 1]),
            ],
        ]);

        $indexes = $repository->indexes()->keyBy('indexName');

        $this->assertFalse($indexes['PK_users']->nonUnique);
        $this->assertTrue($indexes['IX_users_name']->nonUnique);
    }

    public function test_it_inverts_sql_servers_is_unique_flag_in_the_query(): void
    {
        [$repository, $connection] = $this->repository([
            'sys.indexes' => [$this->rawIndex()],
        ]);

        $repository->indexes();

        $this->assertStringContainsString(
            'CASE WHEN i.is_unique = 1 THEN 0 ELSE 1 END AS NON_UNIQUE',
            $connection->queryMatching('sys.indexes')['query'],
            'SQL Server reports uniqueness, while the shared shape expects non-uniqueness.'
        );
    }

    public function test_it_keeps_included_columns_which_sql_server_reports_with_ordinal_zero(): void
    {
        [$repository] = $this->repository([
            'sys.indexes' => [
                $this->rawIndex(['INDEX_NAME' => 'IX_users_included', 'SEQ_IN_INDEX' => 1, 'COLUMN_NAME' => 'name']),
                $this->rawIndex(['INDEX_NAME' => 'IX_users_included', 'SEQ_IN_INDEX' => 0, 'COLUMN_NAME' => 'email']),
            ],
        ]);

        $indexes = $repository->indexes();

        $this->assertCount(2, $indexes);
        $this->assertSame(1, $indexes[0]->seqInIndex);
        $this->assertSame('name', $indexes[0]->columnName);
        $this->assertSame(0, $indexes[1]->seqInIndex);
        $this->assertSame('email', $indexes[1]->columnName);
    }

    public function test_indexes_attach_to_their_schema_qualified_table(): void
    {
        [$repository] = $this->repository([
            'INFORMATION_SCHEMA.TABLES' => [
                $this->rawTable(['TABLE_SCHEMA' => 'dbo', 'TABLE_NAME' => 'users']),
                $this->rawTable(['TABLE_SCHEMA' => 'sales', 'TABLE_NAME' => 'orders']),
            ],
            'INFORMATION_SCHEMA.COLUMNS' => [
                $this->rawColumn(['TABLE_SCHEMA' => 'dbo', 'TABLE_NAME' => 'users']),
                $this->rawColumn(['TABLE_SCHEMA' => 'sales', 'TABLE_NAME' => 'orders']),
            ],
            'sys.indexes' => [
                $this->rawIndex(['TABLE_SCHEMA' => 'dbo', 'TABLE_NAME' => 'users', 'INDEX_NAME' => 'PK_users']),
                $this->rawIndex(['TABLE_SCHEMA' => 'sales', 'TABLE_NAME' => 'orders', 'INDEX_NAME' => 'PK_orders']),
            ],
        ]);

        $tables = collect($repository->tables())->keyBy('tableName');

        $this->assertNotNull(
            $tables['dbo.users']->indexes(),
            'Indexes must use the same SCHEMA.TABLE name as tables, or grouping drops them.'
        );
        $this->assertSame('PK_users', $tables['dbo.users']->indexes()[0]->indexName);
        $this->assertSame('PK_orders', $tables['sales.orders']->indexes()[0]->indexName);
    }

    public function test_it_distinguishes_same_named_tables_in_different_schemas(): void
    {
        [$repository] = $this->repository([
            'INFORMATION_SCHEMA.TABLES' => [
                $this->rawTable(['TABLE_SCHEMA' => 'dbo', 'TABLE_NAME' => 'users']),
                $this->rawTable(['TABLE_SCHEMA' => 'archive', 'TABLE_NAME' => 'users']),
            ],
            'INFORMATION_SCHEMA.COLUMNS' => [
                $this->rawColumn(['TABLE_SCHEMA' => 'dbo', 'TABLE_NAME' => 'users']),
                $this->rawColumn(['TABLE_SCHEMA' => 'archive', 'TABLE_NAME' => 'users']),
            ],
            'sys.indexes' => [
                $this->rawIndex(['TABLE_SCHEMA' => 'dbo', 'TABLE_NAME' => 'users', 'INDEX_NAME' => 'PK_users']),
            ],
        ]);

        $tables = collect($repository->tables())->keyBy('tableName');

        $this->assertSame('PK_users', $tables['dbo.users']->indexes()[0]->indexName);
        $this->assertNull(
            $tables['archive.users']->indexes(),
            'An index on dbo.users must not leak onto archive.users.'
        );
    }

    public function test_it_flags_every_sql_server_string_data_type(): void
    {
        $stringTypes = ['char', 'varchar', 'text', 'nchar', 'nvarchar', 'ntext', 'binary', 'varbinary', 'image'];
        $otherTypes = ['int', 'bigint', 'bit', 'decimal', 'money', 'date', 'datetime2', 'uniqueidentifier'];

        $rows = [];

        foreach (array_merge($stringTypes, $otherTypes) as $type) {
            $rows[] = $this->rawColumn(['COLUMN_NAME' => $type.'_column', 'DATA_TYPE' => $type]);
        }

        [$repository] = $this->repository(['INFORMATION_SCHEMA.COLUMNS' => $rows]);

        $columns = $repository->columns()->keyBy('dataType');

        foreach ($stringTypes as $type) {
            $this->assertTrue($columns[$type]->isStringDataType, "{$type} should be a string data type.");
        }

        foreach ($otherTypes as $type) {
            $this->assertFalse($columns[$type]->isStringDataType, "{$type} should not be a string data type.");
        }
    }
}
