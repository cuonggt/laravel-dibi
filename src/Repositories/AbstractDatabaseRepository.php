<?php

namespace Cuonggt\Dibi\Repositories;

use Cuonggt\Dibi\Contracts\DatabaseRepository;
use Cuonggt\Dibi\InformationSchema;
use Illuminate\Http\Request;

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
        $statement = $this->statementType($sqlQuery);

        return [
            'query' => $sqlQuery,
            'statement' => $statement,
            'result' => $this->db->{$statement}($sqlQuery),
        ];
    }

    /**
     * Split a script into the individual statements it contains.
     *
     * Semicolons only separate statements outside string literals, quoted
     * identifiers and comments, so this scans the script rather than splitting
     * on every semicolon. Blank statements are dropped, which also takes care
     * of a trailing semicolon.
     *
     * Quoting styles from all supported drivers are recognised, since the same
     * script may be pasted against any of them: single and double quotes,
     * MySQL backticks and SQL Server square brackets. A doubled closing
     * character escapes itself, and inside the two string quotes a backslash
     * escapes the character after it, as MySQL allows.
     *
     * Statement bodies that contain semicolons, such as a trigger or procedure
     * declared with BEGIN ... END, are still split at those semicolons; that
     * needs the client to track a delimiter, which Dibi does not.
     *
     * @param  string  $sqlQuery
     * @return array<string>
     */
    public function splitSqlQuery($sqlQuery)
    {
        $closedBy = ['\'' => '\'', '"' => '"', '`' => '`', '[' => ']'];

        $statements = [];
        $buffer = '';
        $length = strlen($sqlQuery);
        $position = 0;

        while ($position < $length) {
            $character = $sqlQuery[$position];
            $pair = substr($sqlQuery, $position, 2);

            if ($pair === '--' || $character === '#') {
                $position = $this->consumeUntil($sqlQuery, $position, "\n", true, $buffer);

                continue;
            }

            if ($pair === '/*') {
                $position = $this->consumeUntil($sqlQuery, $position + 2, '*/', true, $buffer, $pair);

                continue;
            }

            if (isset($closedBy[$character])) {
                $position = $this->consumeQuoted($sqlQuery, $position, $closedBy[$character], $buffer);

                continue;
            }

            if ($character === ';') {
                $statements[] = $buffer;
                $buffer = '';
                $position++;

                continue;
            }

            $buffer .= $character;
            $position++;
        }

        $statements[] = $buffer;

        return array_values(array_filter($statements, function ($statement) {
            return trim($statement) !== '';
        }));
    }

    /**
     * Copy the script into the buffer up to and including the given marker.
     *
     * @return int
     */
    protected function consumeUntil($sqlQuery, $position, $marker, $inclusive, &$buffer, $prefix = '')
    {
        $end = strpos($sqlQuery, $marker, $position);

        $end = $end === false
            ? strlen($sqlQuery)
            : $end + ($inclusive ? strlen($marker) : 0);

        $buffer .= $prefix.substr($sqlQuery, $position, $end - $position);

        return $end;
    }

    /**
     * Copy a quoted run into the buffer, honouring the driver's escaping.
     *
     * @return int
     */
    protected function consumeQuoted($sqlQuery, $position, $closing, &$buffer)
    {
        $escapesWithBackslash = $closing === '\'' || $closing === '"';
        $length = strlen($sqlQuery);

        $buffer .= $sqlQuery[$position];
        $position++;

        while ($position < $length) {
            $character = $sqlQuery[$position];

            if ($escapesWithBackslash && $character === '\\' && $position + 1 < $length) {
                $buffer .= substr($sqlQuery, $position, 2);
                $position += 2;

                continue;
            }

            if ($character === $closing) {
                if (($sqlQuery[$position + 1] ?? null) === $closing) {
                    $buffer .= substr($sqlQuery, $position, 2);
                    $position += 2;

                    continue;
                }

                $buffer .= $character;

                return $position + 1;
            }

            $buffer .= $character;
            $position++;
        }

        return $position;
    }

    /**
     * Determine which connection method should run the given query.
     *
     * Only `select` returns rows, so misreading the leading keyword means a
     * query appears to succeed while returning nothing. The keyword is matched
     * as a word rather than split on a single space, so any whitespace may
     * follow it, and comments before it are skipped.
     *
     * A common table expression is treated as a select. `WITH ... SELECT` is by
     * far the usual form, and the rarer `WITH ... UPDATE` still performs its
     * work when run through select(), merely returning no rows — whereas
     * running `WITH ... SELECT` as a bare statement discards the rows entirely.
     *
     * @param  string  $sqlQuery
     * @return string
     */
    protected function statementType($sqlQuery)
    {
        preg_match('/^\s*([a-z]+)/i', $this->stripLeadingComments($sqlQuery), $matches);

        $keyword = strtolower($matches[1] ?? '');

        if ($keyword === 'with') {
            return 'select';
        }

        return in_array($keyword, ['select', 'insert', 'update', 'delete']) ? $keyword : 'statement';
    }

    /**
     * Strip the whitespace and comments that precede a query's first keyword.
     *
     * @param  string  $sqlQuery
     * @return string
     */
    protected function stripLeadingComments($sqlQuery)
    {
        return preg_replace('{^(?:\s++|--[^\n]*+(?:\n|$)|/\*.*?\*/)++}s', '', $sqlQuery);
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
