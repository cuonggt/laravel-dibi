<?php

namespace Cuonggt\Dibi\Tests\Repositories;

use Cuonggt\Dibi\Repositories\MysqlDatabaseRepository;
use Cuonggt\Dibi\Table;
use Cuonggt\Dibi\TableColumn;
use Cuonggt\Dibi\TableIndex;
use Cuonggt\Dibi\Tests\Fakes\FakeConnection;
use PHPUnit\Framework\TestCase;

class MysqlDatabaseRepositoryTest extends TestCase
{
    /**
     * A row as MySQL's information_schema.tables returns it, with the
     * upper-case column names the repository reads.
     *
     * @return object
     */
    protected function rawTable(array $overrides = [])
    {
        return (object) array_merge([
            'TABLE_CATALOG' => 'def',
            'TABLE_SCHEMA' => 'dibi_testing',
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
            'TABLE_CATALOG' => 'def',
            'TABLE_SCHEMA' => 'dibi_testing',
            'TABLE_NAME' => 'users',
            'COLUMN_NAME' => 'email',
            'ORDINAL_POSITION' => 2,
            'COLUMN_DEFAULT' => null,
            'IS_NULLABLE' => 'YES',
            'DATA_TYPE' => 'varchar',
            'CHARACTER_MAXIMUM_LENGTH' => 255,
            'CHARACTER_OCTET_LENGTH' => 1020,
            'NUMERIC_PRECISION' => null,
            'NUMERIC_SCALE' => null,
            'DATETIME_PRECISION' => null,
            'CHARACTER_SET_NAME' => 'utf8mb4',
            'COLLATION_NAME' => 'utf8mb4_unicode_ci',
        ], $overrides);
    }

    /**
     * @return object
     */
    protected function rawIndex(array $overrides = [])
    {
        return (object) array_merge([
            'TABLE_CATALOG' => 'def',
            'TABLE_SCHEMA' => 'dibi_testing',
            'TABLE_NAME' => 'users',
            'NON_UNIQUE' => 0,
            'INDEX_SCHEMA' => 'dibi_testing',
            'INDEX_NAME' => 'PRIMARY',
            'SEQ_IN_INDEX' => 1,
            'COLUMN_NAME' => 'id',
            'INDEX_TYPE' => 'BTREE',
        ], $overrides);
    }

    /**
     * @return array{0: MysqlDatabaseRepository, 1: FakeConnection}
     */
    protected function repository(array $rows = [])
    {
        $connection = new FakeConnection($rows);

        return [new MysqlDatabaseRepository($connection), $connection];
    }

    public function test_it_reports_the_database_name_from_the_connection(): void
    {
        [$repository] = $this->repository();

        $this->assertSame('dibi_testing', $repository->getName());
    }

    public function test_it_reads_tables_from_information_schema_scoped_to_the_database(): void
    {
        [$repository, $connection] = $this->repository([
            'information_schema.tables' => [$this->rawTable()],
            'information_schema.columns' => [$this->rawColumn()],
        ]);

        $repository->tables();

        $query = $connection->queryMatching('information_schema.tables');

        $this->assertNotNull($query, 'The repository should query information_schema.tables.');
        $this->assertStringContainsString('table_schema = ?', $query['query']);
        $this->assertStringContainsString('ORDER BY table_name ASC', $query['query']);
        $this->assertSame(['dibi_testing'], $query['bindings']);
    }

    public function test_it_reads_columns_from_information_schema_scoped_to_the_database(): void
    {
        [$repository, $connection] = $this->repository([
            'information_schema.columns' => [$this->rawColumn()],
        ]);

        $repository->columns();

        $query = $connection->queryMatching('information_schema.columns');

        $this->assertNotNull($query, 'The repository should query information_schema.columns.');
        $this->assertStringContainsString('table_schema = ?', $query['query']);
        $this->assertStringContainsString('ordinal_position ASC', $query['query']);
        $this->assertSame(['dibi_testing'], $query['bindings']);
    }

    public function test_it_reads_indexes_from_information_schema_scoped_to_the_database(): void
    {
        [$repository, $connection] = $this->repository([
            'information_schema.statistics' => [$this->rawIndex()],
        ]);

        $repository->indexes();

        $query = $connection->queryMatching('information_schema.statistics');

        $this->assertNotNull($query, 'The repository should query information_schema.statistics.');
        $this->assertStringContainsString('table_schema = ?', $query['query']);
        $this->assertSame(['dibi_testing'], $query['bindings']);
    }

    public function test_it_maps_a_raw_table_row_onto_a_table_object(): void
    {
        [$repository] = $this->repository([
            'information_schema.tables' => [$this->rawTable(['TABLE_NAME' => 'orders', 'TABLE_TYPE' => 'VIEW'])],
            'information_schema.columns' => [$this->rawColumn(['TABLE_NAME' => 'orders'])],
        ]);

        $tables = $repository->tables();

        $this->assertCount(1, $tables);
        $this->assertInstanceOf(Table::class, $tables[0]);
        $this->assertSame('def', $tables[0]->tableCatalog);
        $this->assertSame('dibi_testing', $tables[0]->tableSchema);
        $this->assertSame('orders', $tables[0]->tableName);
        $this->assertSame('VIEW', $tables[0]->tableType);
    }

    public function test_it_maps_a_raw_column_row_onto_a_column_object(): void
    {
        [$repository] = $this->repository([
            'information_schema.columns' => [$this->rawColumn([
                'COLUMN_DEFAULT' => 'unknown@example.com',
                'NUMERIC_PRECISION' => 10,
                'NUMERIC_SCALE' => 2,
                'DATETIME_PRECISION' => 6,
            ])],
        ]);

        $column = $repository->columns()->first();

        $this->assertInstanceOf(TableColumn::class, $column);
        $this->assertSame('users', $column->tableName);
        $this->assertSame('email', $column->columnName);
        $this->assertSame(2, $column->ordinalPosition);
        $this->assertSame('unknown@example.com', $column->columnDefault);
        $this->assertSame('varchar', $column->dataType);
        $this->assertSame(255, $column->characterMaximumLength);
        $this->assertSame(1020, $column->characterOctetLength);
        $this->assertSame(10, $column->numericPrecision);
        $this->assertSame(2, $column->numericScale);
        $this->assertSame(6, $column->datetimePrecision);
        $this->assertSame('utf8mb4', $column->characterSetName);
        $this->assertSame('utf8mb4_unicode_ci', $column->collationName);
    }

    public function test_it_converts_the_is_nullable_string_to_a_boolean(): void
    {
        [$repository] = $this->repository([
            'information_schema.columns' => [
                $this->rawColumn(['COLUMN_NAME' => 'nullable', 'IS_NULLABLE' => 'YES']),
                $this->rawColumn(['COLUMN_NAME' => 'required', 'IS_NULLABLE' => 'NO']),
            ],
        ]);

        $columns = $repository->columns()->keyBy('columnName');

        $this->assertTrue($columns['nullable']->isNullable);
        $this->assertFalse($columns['required']->isNullable);
    }

    public function test_it_flags_every_mysql_string_data_type(): void
    {
        $stringTypes = [
            'char', 'varchar', 'binary', 'varbinary', 'tinyblob', 'tinytext',
            'text', 'blob', 'mediumtext', 'mediumblob', 'longtext', 'longblob',
            'enum', 'set',
        ];

        $otherTypes = [
            'int', 'bigint', 'tinyint', 'decimal', 'float', 'double',
            'date', 'datetime', 'timestamp', 'time', 'year', 'json',
        ];

        $rows = [];

        foreach (array_merge($stringTypes, $otherTypes) as $type) {
            $rows[] = $this->rawColumn(['COLUMN_NAME' => $type.'_column', 'DATA_TYPE' => $type]);
        }

        [$repository] = $this->repository(['information_schema.columns' => $rows]);

        $columns = $repository->columns()->keyBy('dataType');

        foreach ($stringTypes as $type) {
            $this->assertTrue(
                $columns[$type]->isStringDataType,
                "{$type} should be treated as a string data type."
            );
        }

        foreach ($otherTypes as $type) {
            $this->assertFalse(
                $columns[$type]->isStringDataType,
                "{$type} should not be treated as a string data type."
            );
        }
    }

    public function test_it_maps_a_raw_index_row_onto_an_index_object(): void
    {
        [$repository] = $this->repository([
            'information_schema.statistics' => [$this->rawIndex([
                'INDEX_NAME' => 'users_email_index',
                'SEQ_IN_INDEX' => 2,
                'COLUMN_NAME' => 'email',
                'INDEX_TYPE' => 'HASH',
            ])],
        ]);

        $index = $repository->indexes()->first();

        $this->assertInstanceOf(TableIndex::class, $index);
        $this->assertSame('users', $index->tableName);
        $this->assertSame('users_email_index', $index->indexName);
        $this->assertSame('dibi_testing', $index->indexSchema);
        $this->assertSame(2, $index->seqInIndex);
        $this->assertSame('email', $index->columnName);
        $this->assertSame('HASH', $index->indexType);
    }

    public function test_it_converts_non_unique_to_a_boolean(): void
    {
        [$repository] = $this->repository([
            'information_schema.statistics' => [
                $this->rawIndex(['INDEX_NAME' => 'PRIMARY', 'NON_UNIQUE' => 0]),
                $this->rawIndex(['INDEX_NAME' => 'users_name_index', 'NON_UNIQUE' => 1]),
            ],
        ]);

        $indexes = $repository->indexes()->keyBy('indexName');

        $this->assertFalse($indexes['PRIMARY']->nonUnique);
        $this->assertTrue($indexes['users_name_index']->nonUnique);
    }

    public function test_it_groups_columns_and_indexes_onto_the_table_they_belong_to(): void
    {
        [$repository] = $this->repository([
            'information_schema.tables' => [
                $this->rawTable(['TABLE_NAME' => 'users']),
                $this->rawTable(['TABLE_NAME' => 'orders']),
            ],
            'information_schema.columns' => [
                $this->rawColumn(['TABLE_NAME' => 'users', 'COLUMN_NAME' => 'id']),
                $this->rawColumn(['TABLE_NAME' => 'users', 'COLUMN_NAME' => 'email']),
                $this->rawColumn(['TABLE_NAME' => 'orders', 'COLUMN_NAME' => 'total']),
            ],
            'information_schema.statistics' => [
                $this->rawIndex(['TABLE_NAME' => 'users', 'INDEX_NAME' => 'PRIMARY']),
            ],
        ]);

        $tables = collect($repository->tables())->keyBy('tableName');

        $this->assertCount(2, $tables['users']->columns());
        $this->assertSame(
            ['id', 'email'],
            collect($tables['users']->columns())->pluck('columnName')->all()
        );

        $this->assertCount(1, $tables['orders']->columns());
        $this->assertSame('total', $tables['orders']->columns()[0]->columnName);

        $this->assertCount(1, $tables['users']->indexes());
        $this->assertSame('PRIMARY', $tables['users']->indexes()[0]->indexName);
    }

    public function test_it_leaves_indexes_unset_for_a_table_that_has_none(): void
    {
        [$repository] = $this->repository([
            'information_schema.tables' => [$this->rawTable(['TABLE_NAME' => 'orders'])],
            'information_schema.columns' => [$this->rawColumn(['TABLE_NAME' => 'orders'])],
            'information_schema.statistics' => [$this->rawIndex(['TABLE_NAME' => 'users'])],
        ]);

        $tables = $repository->tables();

        $this->assertNull($tables[0]->indexes());
    }

    public function test_it_keeps_the_raw_row_alongside_the_mapped_object(): void
    {
        [$repository] = $this->repository([
            'information_schema.columns' => [$this->rawColumn(['COLUMN_NAME' => 'email'])],
        ]);

        $raw = $repository->columns()->first()->getRaw();

        $this->assertSame('email', $raw['COLUMN_NAME']);
        $this->assertSame('YES', $raw['IS_NULLABLE']);
    }
}
