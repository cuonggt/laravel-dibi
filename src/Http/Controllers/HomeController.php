<?php

namespace Cuonggt\Dibi\Http\Controllers;

use Cuonggt\Dibi\Dibi;

class HomeController extends Controller
{
    /**
     * Single page application catch-all route.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('dibi::app', [
            'tables' => $this->database->tables(),
            'dibiScriptVariables' => Dibi::scriptVariables(),
            'assetsAreCurrent' => Dibi::assetsAreCurrent(),
        ]);
    }
}
