<?php

namespace Cuonggt\Dibi\Http\Controllers;

use Cuonggt\Dibi\Dibi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class SelectConnectionController extends Controller
{
    public function select(Request $request)
    {
        if (! in_array($request->connection, Dibi::databaseConnections())) {
            throw ValidationException::withMessages([
                'connection' => ['Invalid connection.'],
            ]);
        }

        Cache::put('dibiConnection', $request->connection);
    }
}
