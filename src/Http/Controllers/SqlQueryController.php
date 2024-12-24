<?php

namespace Cuonggt\Dibi\Http\Controllers;

use Cuonggt\Dibi\Dibi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;

class SqlQueryController extends Controller
{
    public function run(Request $request)
    {
        // Determine the database connection type
        $connectionType = Config::get('database.default');

        // Split the SQL queries by semicolon and filter out empty queries
        $queries = collect(explode(';', $request->sql_query))->filter();

        // Map each query to its result after converting to SQLite syntax if necessary
        $results = $queries->map(function ($query) use ($connectionType) {
            // Convert the query to SQLite syntax if the connection is SQLite
            if ($connectionType === 'sqlite') {
                $query = $this->convertToSQLite($query);
            }
            // Execute the (converted) query
            return Dibi::databaseRepository()->runSqlQuery($query);
        });

        return [
            'results' => $results,
        ];
    }

    /**
     * Convert SQL query to SQLite compatible syntax.
     *
     * @param string $query
     * @return string
     */
    protected function convertToSQLite($query)
    {
        // Example conversion logic
        // Replace backticks with double quotes for identifiers
        $query = str_replace('`', '"', $query);

        // Convert MySQL specific functions to SQLite equivalents
        $query = $this->convertFunctions($query);

        // Handle specific SQL dialect differences
        // For example, converting LIMIT clauses
        if (preg_match('/LIMIT (\d+)/', $query, $matches)) {
            $limit = $matches[1];
            $query = preg_replace('/LIMIT \d+/', "LIMIT $limit", $query);
        }

        // Remove MySQL specific clauses
        $query = preg_replace('/ENGINE=\w+/', '', $query); // Remove ENGINE clause

        // Additional conversions can be added here

        return trim($query); // Return the trimmed query
    }

    /**
     * Convert MySQL specific functions to SQLite equivalents.
     *
     * @param string $query
     * @return string
     */
    protected function convertFunctions($query)
    {
        // Example conversion for MySQL functions to SQLite
        $query = str_replace('NOW()', 'DATETIME("now")', $query); // Convert NOW() to SQLite equivalent
        $query = str_replace('CURDATE()', 'DATE("now")', $query); // Convert CURDATE() to SQLite equivalent
        // Add more function conversions as needed

        return $query;
    }
}