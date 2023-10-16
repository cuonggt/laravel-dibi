<?php

namespace Cuonggt\Dibi\Repositories;

use Cuonggt\Dibi\Contracts\DatabaseRepository;
use Cuonggt\Dibi\InformationSchema;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

abstract class AbstractDatabaseRepository implements DatabaseRepository
{
    /**
     * The database instance.
     *
     * @var \Illuminate\Database\ConnectionInterface
     */
    public $db;

    /**
     * Create a new database repository.
     *
     * @param  \Illuminate\Database\ConnectionInterface  $db
     * @return void
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->db->getDatabaseName();
    }

    /**
     * {@inheritdoc}
     */
    public function runSqlQuery($sqlQuery)
    {
        $statement = strtolower(Arr::first(explode(' ', $sqlQuery)));

        if (! in_array($statement, ['select', 'insert', 'update', 'delete'])) {
            $statement = 'statement';
        }

        return [
            'query' => $sqlQuery,
            'statement' => $statement,
            'result' => $this->db->{$statement}($sqlQuery),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function informationSchema()
    {
        return new InformationSchema($this->tables());
    }

    /**
     * {@inheritdoc}
     */
    public function tables()
    {
        $columns = $this->columns()->groupBy('tableName');
        $indexes = $this->indexes()->groupBy('tableName');

        return collect($this->rawTables())
            ->map(function ($rawTable) use ($columns, $indexes) {
                $table = $this->mapRawTableToObject($rawTable);

                if ($indexes[$table->tableName] ?? false) {
                    $table->indexes($indexes[$table->tableName]->toArray());
                }

                return $table
                    ->columns($columns[$table->tableName]->toArray());
            })
            ->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function columns()
    {
        return collect($this->rawColumns())->map(function ($rawColumn) {
            return $this->mapRawColumnToObject($rawColumn);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function indexes()
    {
        return collect($this->rawIndexes())->map(function ($rawIndex) {
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
     * @return \Illuminate\Database\Query\Builder
     */
    protected function buildSelectQuery($table, Request $request)
    {
        $query = $this->db->table($table);

        foreach ($request->input('filters', []) as $filter) {
            if ($filter['field'] == '__raw__') {
                if (! empty($filter['value'])) {
                    $query->whereRaw($filter['value']);
                }
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

    /**
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  string  $column
     * @param  string  $operator
     * @param  string  $value
     * @param  string  $boolean
     * @return \Illuminate\Database\Query\Builder
     */
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
     * Map the raw table object to a Dibi Table instance.
     *
     * @param  object  $rawTable
     * @return \Cuonggt\Dibi\Table
     */
    abstract protected function mapRawTableToObject($rawTable);

    /**
     * Get list of raw columns.
     *
     * @return array
     */
    abstract protected function rawColumns();

    /**
     * Map the raw column object to a Dibi TableColumn instance.
     *
     * @param  object  $rawColumn
     * @return \Cuonggt\Dibi\TableColumn
     */
    abstract protected function mapRawColumnToObject($rawColumn);

    /**
     * Get list of raw indexes.
     *
     * @return array
     */
    abstract protected function rawIndexes();

    /**
     * Map the raw index object to a Dibi TableIndex instance.
     *
     * @param  object  $rawIndex
     * @return \Cuonggt\Dibi\TableIndex
     */
    abstract protected function mapRawIndexToObject($rawIndex);
}
