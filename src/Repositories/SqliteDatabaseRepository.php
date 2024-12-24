<?php

namespace Cuonggt\Dibi\Repositories;

use Cuonggt\Dibi\Table;
use Cuonggt\Dibi\TableColumn;
use Cuonggt\Dibi\TableIndex;

class SqliteDatabaseRepository extends AbstractDatabaseRepository
{
    /**
     * @return array
     */
    protected function stringDataTypes()
    {
        return [
            'TEXT',
            'CHAR',
            'VARCHAR',
            'BLOB',
            // Add other string types as needed
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function rawTables()
    {
        // Get all tables from the sqlite_master table
        return $this->db->select(
            'SELECT name AS table_name FROM sqlite_master WHERE type = "table" ORDER BY name ASC'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function mapRawTableToObject($rawTable)
    {
        return (new Table)->setRaw((array) $rawTable)->map([
            'tableName' => $rawTable->table_name,
            'tableType' => 'table', // SQLite only supports tables in this context
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function rawColumns()
    {
        // Get columns for all tables
        $tables = $this->rawTables();
        $columns = [];

        foreach ($tables as $table) {
            $tableName = $table->table_name;
            $columnsData = $this->db->select("PRAGMA table_info($tableName)");

            foreach ($columnsData as $column) {
                $columns[] = (object) [
                    'table_name' => $tableName,
                    'column_name' => $column->name,
                    'data_type' => $column->type,
                    'is_nullable' => $column->notnull == 0,
                    'column_default' => $column->dflt_value,
                    'ordinal_position' => $column->cid,
                ];
            }
        }

        return $columns;
    }

    /**
     * {@inheritdoc}
     */
    protected function mapRawColumnToObject($rawColumn)
    {
        return (new TableColumn)->setRaw((array) $rawColumn)->map([
            'tableName' => $rawColumn->table_name,
            'columnName' => $rawColumn->column_name,
            'ordinalPosition' => $rawColumn->ordinal_position,
            'columnDefault' => $rawColumn->column_default,
            'isNullable' => $rawColumn->is_nullable,
            'dataType' => $rawColumn->data_type,
            'isStringDataType' => in_array(strtoupper($rawColumn->data_type), $this->stringDataTypes()),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function rawIndexes()
    {
        // Get indexes for all tables
        $tables = $this->rawTables();
        $indexes = [];

        foreach ($tables as $table) {
            $tableName = $table->table_name;
            $indexData = $this->db->select("PRAGMA index_list($tableName)");

            foreach ($indexData as $index) {
                $indexDetails = $this->db->select("PRAGMA index_info({$index->name})");
                foreach ($indexDetails as $indexDetail) {
                    $indexes[] = (object) [
                        'table_name' => $tableName,
                        'index_name' => $index->name,
                        'column_name' => $indexDetail->name,
                        'non_unique' => !$index->unique,
                    ];
                }
            }
        }

        return $indexes;
    }

    /**
     * {@inheritdoc}
     */
    protected function mapRawIndexToObject($rawIndex)
    {
        return (new TableIndex)->setRaw((array) $rawIndex)->map([
            'tableName' => $rawIndex->table_name,
            'indexName' => $rawIndex->index_name,
            'nonUnique' => $rawIndex->non_unique,
            'columnName' => $rawIndex->column_name,
        ]);
    }
}
