<?php

namespace Cuonggt\Dibi\Http\Controllers;

class TablesController extends Controller
{
    /**
     * Get list of tables.
     *
     * @return \Illuminate\Http\JsonResponse.
     */
    public function index()
    {
        return response()->json($this->database->tables());
    }

    /**
     * Get the table for the given name.
     *
     * @param  string  $name
     * @return \Illuminate\Http\JsonResponse.
     */
    public function show($name)
    {
        return response()->json([
            'info' => $this->database->table($name),
            'columns' => $this->database->columns($name),
            'indexes' => $this->database->indexes($name),
        ]);
    }
}
