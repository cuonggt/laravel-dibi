<?php

namespace Cuonggt\Dibi\Tests\Fakes;

/**
 * A stand-in for a database connection that records the queries it is asked to
 * run and replays canned rows, so repository query construction and row mapping
 * can be tested without a live server.
 */
class FakeConnection
{
    /**
     * Every query run against this connection, with its bindings.
     *
     * @var array<array{query: string, bindings: array}>
     */
    public $queries = [];

    /**
     * Rows to return, keyed by a fragment of the query that selects them.
     *
     * @var array<string, array>
     */
    public $rows = [];

    /**
     * The database name the connection reports.
     *
     * @var string
     */
    public $databaseName;

    public function __construct(array $rows = [], $databaseName = 'dibi_testing')
    {
        $this->rows = $rows;
        $this->databaseName = $databaseName;
    }

    public function getDatabaseName()
    {
        return $this->databaseName;
    }

    public function select($query, $bindings = [])
    {
        $this->queries[] = ['query' => $query, 'bindings' => $bindings];

        foreach ($this->rows as $fragment => $rows) {
            if (strpos($query, $fragment) !== false) {
                return $rows;
            }
        }

        return [];
    }

    /**
     * The single query run against this connection matching the given fragment.
     *
     * @return array{query: string, bindings: array}|null
     */
    public function queryMatching($fragment)
    {
        foreach ($this->queries as $query) {
            if (strpos($query['query'], $fragment) !== false) {
                return $query;
            }
        }

        return null;
    }
}
