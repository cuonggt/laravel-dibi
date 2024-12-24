<?php

namespace Cuonggt\Dibi\Http\Controllers;

use Cuonggt\Dibi\Dibi;
use Illuminate\Routing\Controller;

class HomeController extends Controller
{
    /**
     * Single page application catch-all route.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        return view('dibi::app', [
            'dibiScriptVariables' => Dibi::scriptVariables([
                'databaseConnections' => Dibi::databaseConnections(),
                'currentDatabaseConnection' => Dibi::currentDatabaseConnection(),
                'database' => Dibi::databaseRepository()->getName(),
                'informationSchema' => Dibi::databaseRepository()->informationSchema(),
            ]),
        ]);
    }
}
