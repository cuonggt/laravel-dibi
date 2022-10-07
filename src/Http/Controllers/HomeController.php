<?php

namespace Cuonggt\Dibi\Http\Controllers;

use Cuonggt\Dibi\Dibi;

class HomeController extends Controller
{
    /**
     * Single page application catch-all route.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $tables = $this->database->tables();

        return view('dibi::app', [
            'tables' => $tables->pluck('name')->all(),
            'dibiScriptVariables' => Dibi::scriptVariables([
                'database' => $this->database->getName(),
                'tables' => $tables->toArray(),
            ]),
        ]);
    }
}
