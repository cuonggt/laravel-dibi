<?php

namespace Cuonggt\Dibi\Repositories;

use Cuonggt\Dibi\Contracts\DatabaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

abstract class AbstractDatabaseRepository implements DatabaseRepository
{
    /**
     * The database instance.
     *
     * @var \Illuminate\Database\Connection
     */
    public $db;

    /**
     * The database name.
     *
     * @var string
     */
    public $name;

    /**
     * Create a new database repository.
     *
     * @param  string   $name
     * @return void
     */
    public function __construct($name)
    {
        $this->db = DB::connection(config('dibi.db_connection'));
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function tables()
    {
        return collect($this->rawTables())->map(function ($rawTable) {
            return $this->mapRawTableToObject($rawTable);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function table($tableName)
    {
        return $this->mapRawTableToObject($this->rawTableByName($tableName));
    }

    /**
     * {@inheritdoc}
     */
    public function columns($table)
    {
        return collect($this->rawColumns($table))->map(function ($rawColumn) {
            return $this->mapRawColumnToObject($rawColumn);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function indexes($table)
    {
        return collect($this->rawIndexes($table))->map(function ($rawIndex) {
            return $this->mapRawIndexToObject($rawIndex);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function rows($table, Request $request)
    {
        $query = $this->buildSelectQuery($table, $request);

        $total = $query->count();

        $query->when(! empty($request->sort_key), function ($q) use ($request) {
            return $q->orderBy(
                $request->sort_key,
                $request->input('sort_dir', 'asc')
            );
        });

        return [
            'total' => $total,
            'data' => $query->offset($request->input('offset', 0))->limit($request->input('limit', 50))->get(),
        ];
    }

    /**
     * Build select query.
     *
     * @param  string  $table
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Query\Builder
     */
    protected function buildSelectQuery($table, Request $request)
    {
        $query = DB::table($table);

        foreach ($request->input('filters', []) as $filter) {
            if ($filter['field'] == '__raw__' && ! empty($filter['value'])) {
                $query->whereRaw($filter['value']);
            } elseif ($filter['field'] == '__any__') {
                foreach ($this->columns($table)->pluck('column_name')->all() as $column) {
                    $query->tap(function ($query) use ($column, $filter) {
                        return $this->buildSubWhereClause($query, $column, $filter['operator'], $filter['value'], 'or');
                    });
                }
            } else {
                $query->tap(function ($query) use ($filter) {
                    return $this->buildSubWhereClause($query, $filter['field'], $filter['operator'], $filter['value']);
                });
            }
        }

        return $query;
    }

    public function buildSubWhereClause($query, $column, $operator = '=', $value = '', $boolean = 'and')
    {
        if (in_array($operator, ['IN', 'NOT IN'])) {
            return $query->whereIn($column, array_filter(explode(',', $value)), $boolean, $operator == 'NOT IN');
        }

        if (in_array($operator, ['IS NULL', 'IS NOT NULL'])) {
            return $query->whereNull($column, $boolean, $operator == 'IS NOT NULL');
        }

        if (in_array($operator, ['BETWEEN', 'NOT BETWEEN'])) {
            return $query->whereBetween($column, explode(' AND ', strtoupper($value)), $boolean, $operator == 'NOT BETWEEN');
        }

        if (in_array($operator, ['LIKE', 'NOT LIKE'])) {
            return $query->where($column, $operator, '%'.$value.'%', $boolean);
        }

        return $query->where($column, $operator, $value, $boolean);
    }

    /**
     * Get list of raw tables.
     *
     * @return array
     */
    abstract protected function rawTables();

    /**
     * Get the raw table for the given name.
     *
     * @param  string  $table
     * @return object
     */
    abstract protected function rawTableByName($table);

    /**
     * Map the raw table object to a Dibi Table instance.
     *
     * @param  object  $rawTable
     * @return \Cuonggt\Dibi\Table
     */
    abstract protected function mapRawTableToObject($rawTable);

    /**
     * Get list of raw columns for the given table.
     *
     * @param  string  $table
     * @return array
     */
    abstract protected function rawColumns($table);

    /**
     * Map the raw column object to a Dibi TableColumn instance.
     *
     * @param  object  $rawColumn
     * @return \Cuonggt\Dibi\TableColumn
     */
    abstract protected function mapRawColumnToObject($rawColumn);

    /**
     * Get list of raw indexes for the given table.
     *
     * @param  string  $table
     * @return array
     */
    abstract protected function rawIndexes($table);

    /**
     * Map the raw index object to a Dibi TableIndex instance.
     *
     * @param  object  $index
     * @return \Cuonggt\Dibi\TableIndex
     */
    abstract protected function mapRawIndexToObject($index);
}
