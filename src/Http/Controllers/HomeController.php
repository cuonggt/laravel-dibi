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
        $informationSchema = Dibi::databaseRepository()->informationSchema();
        // dd($informationSchema->tables[0]->columns);
        return view('dibi::app', [
            'dibiScriptVariables' => Dibi::scriptVariables([
                'database' => Dibi::databaseRepository()->getName(),
                'informationSchema' => $informationSchema,
            ]),
        ]);
    }
}
