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
            'nullable' => $column->Null == 'YES',
            'key' => $column->Key,
            'default' => $column->Default,
            'extra' => $column->Extra,
        ]);
    }
}
