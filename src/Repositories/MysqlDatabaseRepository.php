<?php

namespace Cuonggt\Dibi\Repositories;

use Cuonggt\Dibi\Table;
use Cuonggt\Dibi\TableColumn;
use Cuonggt\Dibi\TableIndex;
use Illuminate\Support\Arr;

class MysqlDatabaseRepository extends AbstractDatabaseRepository
{
    /**
     * {@inheritdoc}
     */
    protected function rawTables()
    {
        return $this->db->select('SELECT * FROM information_schema.tables WHERE table_schema = ?', [$this->getName()]);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapRawTableToObject($rawTable)
    {
        return (new Table)->setRaw((array) $rawTable)->map([
            'name' => $rawTable->TABLE_NAME,
            'type' => $rawTable->TABLE_TYPE,
            'engine' => $rawTable->ENGINE,
            'version' => $rawTable->VERSION,
            'rowFormat' => $rawTable->ROW_FORMAT,
            'rows' => $rawTable->TABLE_ROWS,
            'avgRowLength' => $rawTable->AVG_ROW_LENGTH,
            'dataLength' => $rawTable->DATA_LENGTH,
            'maxDataLength' => $rawTable->MAX_DATA_LENGTH,
            'indexLength' => $rawTable->INDEX_LENGTH,
            'dataFree' => $rawTable->DATA_FREE,
            'autoIncrement' => $rawTable->AUTO_INCREMENT,
            'createTime' => $rawTable->CREATE_TIME,
            'updateTime' => $rawTable->UPDATE_TIME,
            'checkTime' => $rawTable->CHECK_TIME,
            'encoding' => Arr::first(explode('_', $rawTable->TABLE_COLLATION)),
            'collation' => $rawTable->TABLE_COLLATION,
            'checksum' => $rawTable->CHECKSUM,
            'createOptions' => $rawTable->CREATE_OPTIONS,
            'comment' => $rawTable->TABLE_COMMENT,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function rawTableByName($tableName)
    {
        return Arr::first(
            $this->db->select(
                'SELECT * FROM information_schema.tables WHERE table_schema = ? AND table_name = ? LIMIT 1',
                [$this->getName(), $tableName]
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function rawColumns($tableName)
    {
        return $this->db->select('SHOW FULL COLUMNS FROM '.$tableName);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapRawColumnToObject($rawColumn)
    {
        return (new TableColumn)->setRaw((array) $rawColumn)->map([
            'column_name' => $rawColumn->Field,
            'data_type' => $rawColumn->Type,
            'collation' => $rawColumn->Collation,
            'is_nullable' => $rawColumn->{'Null'} == 'YES',
            'key' => $rawColumn->Key,
            'column_default' => $rawColumn->Default,
            'extra' => $rawColumn->Extra,
            'comment' => $rawColumn->Comment,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function rawIndexes($tableName)
    {
        return $this->db->select(
            'SELECT * FROM information_schema.statistics WHERE table_schema = ? AND table_name = ?',
            [$this->getName(), $tableName]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function mapRawIndexToObject($rawIndex)
    {
        return (new TableIndex)->setRaw((array) $rawIndex)->map([
            'index_name' => $rawIndex->INDEX_NAME,
            'index_algorithm' => $rawIndex->INDEX_TYPE,
            'is_unique' => ! $rawIndex->NON_UNIQUE,
            'column_name' => $rawIndex->COLUMN_NAME,
        ]);
    }
}
