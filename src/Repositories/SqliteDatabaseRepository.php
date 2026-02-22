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
            'text',
            'varchar',
            'char',
            'clob',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function rawTables()
    {
        return $this->db->select(
            "SELECT name, type FROM sqlite_master WHERE type IN ('table', 'view') AND name NOT LIKE 'sqlite_%' ORDER BY name ASC"
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function mapRawTableToObject($rawTable)
    {
        return (new Table)->setRaw((array) $rawTable)->map([
            'tableCatalog' => null,
            'tableSchema' => $this->getName(),
            'tableName' => $rawTable->name,
            'tableType' => $rawTable->type === 'view' ? 'VIEW' : 'BASE TABLE',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function rawColumns()
    {
        $columns = [];

        foreach ($this->rawTables() as $rawTable) {
            $tableColumns = $this->db->select("PRAGMA table_info('{$rawTable->name}')");

            foreach ($tableColumns as $column) {
                $column->table_name = $rawTable->name;
                $columns[] = $column;
            }
        }

        return $columns;
    }

    /**
     * {@inheritdoc}
     */
    protected function mapRawColumnToObject($rawColumn)
    {
        $dataType = strtolower($rawColumn->type ?: 'text');

        return (new TableColumn)->setRaw((array) $rawColumn)->map([
            'tableCatalog' => null,
            'tableSchema' => $this->getName(),
            'tableName' => $rawColumn->table_name,
            'columnName' => $rawColumn->name,
            'ordinalPosition' => $rawColumn->cid + 1,
            'columnDefault' => $rawColumn->dflt_value,
            'isNullable' => ! $rawColumn->notnull,
            'dataType' => $dataType,
            'characterMaximumLength' => null,
            'characterOctetLength' => null,
            'numericPrecision' => null,
            'numericScale' => null,
            'datetimePrecision' => null,
            'characterSetName' => null,
            'collationName' => null,
            'isStringDataType' => in_array($dataType, $this->stringDataTypes()),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function rawIndexes()
    {
        $indexes = [];

        foreach ($this->rawTables() as $rawTable) {
            $tableIndexes = $this->db->select("PRAGMA index_list('{$rawTable->name}')");

            foreach ($tableIndexes as $index) {
                $indexInfo = $this->db->select("PRAGMA index_info('{$index->name}')");

                foreach ($indexInfo as $info) {
                    $indexes[] = (object) [
                        'table_name' => $rawTable->name,
                        'index_name' => $index->name,
                        'non_unique' => ! $index->unique,
                        'column_name' => $info->name,
                        'index_type' => $index->origin === 'pk' ? 'PRIMARY' : 'BTREE',
                        'seq_in_index' => $info->seqno + 1,
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
            'tableCatalog' => null,
            'tableSchema' => $this->getName(),
            'tableName' => $rawIndex->table_name,
            'nonUnique' => (bool) $rawIndex->non_unique,
            'indexSchema' => $this->getName(),
            'indexName' => $rawIndex->index_name,
            'seqInIndex' => $rawIndex->seq_in_index,
            'columnName' => $rawIndex->column_name,
            'indexType' => $rawIndex->index_type,
        ]);
    }
}
