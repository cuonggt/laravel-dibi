<?php

namespace Cuonggt\Dibi;

use Illuminate\Support\Facades\DB;

class MysqlDatabase extends AbstractDatabase
{
    protected function getTables()
    {
        return DB::select('select * from information_schema.tables where table_schema = ?', [$this->name]);
    }

    protected function getTableByName($table)
    {
        $tables = DB::select(
            'select * from information_schema.tables where table_schema = ? and table_name = ? limit 1',
            [$this->name, $table]
        );

        return array_first($tables);
    }

    protected function mapTableToObject($table)
    {
        $encoding = array_first(explode('_', $table->TABLE_COLLATION));

        return (new Table)->setRaw((array) $table)->map([
            'name' => $table->TABLE_NAME,
            'type' => $table->TABLE_TYPE,
            'engine' => $table->ENGINE,
            'version' => $table->VERSION,
            'rowFormat' => $table->ROW_FORMAT,
            'rows' => $table->TABLE_ROWS,
            'avgRowLength' => $table->AVG_ROW_LENGTH,
            'dataLength' => $table->DATA_LENGTH,
            'maxDataLength' => $table->MAX_DATA_LENGTH,
            'indexLength' => $table->INDEX_LENGTH,
            'dataFree' => $table->DATA_FREE,
            'autoIncrement' => $table->AUTO_INCREMENT,
            'createTime' => $table->CREATE_TIME,
            'updateTime' => $table->UPDATE_TIME,
            'checkTime' => $table->CHECK_TIME,
            'encoding' => $encoding,
            'collation' => $table->TABLE_COLLATION,
            'checksum' => $table->CHECKSUM,
            'createOptions' => $table->CREATE_OPTIONS,
            'comment' => $table->TABLE_COMMENT,
        ]);
    }

    protected function getColumns($table)
    {
        return DB::select('show columns from '.$table);
    }

    protected function mapColumnToObject($column)
    {
        return (new Column)->setRaw((array) $column)->map([
            'field' => $column->Field,
            'type' => $column->Type,
            'nullable' => $column->{'Null'} == 'YES',
            'key' => $column->Key,
            'default' => $column->Default,
            'extra' => $column->Extra,
        ]);
    }

    protected function getIndexes($table)
    {
        return DB::select(
            'select * from information_schema.statistics where table_schema = ? and table_name = ?',
            [$this->name, $table]
        );
    }

    protected function mapIndexToObject($index)
    {
        return (new TableIndex)->setRaw((array) $index)->map([
            'nonUnique' => $index->NON_UNIQUE,
            'keyName' => $index->INDEX_NAME,
            'seqInIndex' => $index->SEQ_IN_INDEX,
            'columnName' => $index->COLUMN_NAME,
            'collation' => $index->COLLATION,
            'cardinality' => $index->CARDINALITY,
            'subPart' => $index->SUB_PART,
            'packed' => $index->PACKED,
            'comment' => $index->COMMENT,
        ]);
    }

    protected function getKeyName($table)
    {
        $columns = DB::select(
            'select * from information_schema.columns where table_schema = ? and table_name = ? and column_key = ?',
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
