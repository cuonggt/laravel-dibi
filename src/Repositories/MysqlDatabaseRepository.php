<?php

namespace Cuonggt\Dibi\Repositories;

use Cuonggt\Dibi\Table;
use Cuonggt\Dibi\Column;
use Illuminate\Support\Arr;
use Cuonggt\Dibi\TableIndex;
use Illuminate\Support\Facades\DB;

class MysqlDatabaseRepository extends AbstractDatabaseRepository
{
    /**
     * {@inheritdoc}
     */
    protected function rawTables()
    {
        return DB::select('SELECT * FROM information_schema.tables WHERE table_schema = ?', [$this->name]);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapRawTableToObject($rawTable)
    {
        $encoding = Arr::first(explode('_', $rawTable->TABLE_COLLATION));

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
            'encoding' => $encoding,
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
        $tables = DB::select(
            'SELECT * FROM information_schema.tables WHERE table_schema = ? AND table_name = ? LIMIT 1',
            [$this->name, $tableName]
        );

        return Arr::first($tables);
    }

    /**
     * {@inheritdoc}
     */
    protected function rawColumns($tableName)
    {
        return DB::select('SHOW COLUMNS FROM '.$tableName);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapRawColumnToObject($rawColumn)
    {
        return (new Column)->setRaw((array) $rawColumn)->map([
            'field' => $rawColumn->Field,
            'type' => $rawColumn->Type,
            'nullable' => $rawColumn->{'Null'} == 'YES',
            'key' => $rawColumn->Key,
            'default' => $rawColumn->Default,
            'extra' => $rawColumn->Extra,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function rawIndexes($tableName)
    {
        return DB::select(
            'SELECT * FROM information_schema.statistics WHERE table_schema = ? AND table_name = ?',
            [$this->name, $tableName]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function mapRawIndexToObject($rawIndex)
    {
        return (new TableIndex)->setRaw((array) $rawIndex)->map([
            'nonUnique' => $rawIndex->NON_UNIQUE,
            'keyName' => $rawIndex->INDEX_NAME,
            'seqInIndex' => $rawIndex->SEQ_IN_INDEX,
            'columnName' => $rawIndex->COLUMN_NAME,
            'collation' => $rawIndex->COLLATION,
            'cardinality' => $rawIndex->CARDINALITY,
            'subPart' => $rawIndex->SUB_PART,
            'packed' => $rawIndex->PACKED,
            'comment' => $rawIndex->COMMENT,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getKeyName($table)
    {
        $columns = DB::select(
            'SELECT * FROM information_schema.columns WHERE table_schema = ? AND table_name = ? AND column_key = ?',
            [$this->name, $table, 'PRI']
        );

        $count = count($columns);

        if ($count > 1) {
            return array_map(function ($column) {
                return $column->COLUMN_NAME;
            }, $columns);
        }

        if ($count == 1) {
            return $columns[0]->COLUMN_NAME;
        }

        return false;
    }
}
