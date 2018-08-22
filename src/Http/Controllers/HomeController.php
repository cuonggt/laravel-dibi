<?php

namespace Cuonggt\Dibi\Http\Controllers;

use Dibi;

class HomeController extends Controller
{
    /**
     * Single page application catch-all route.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('dibi::app');
    }
}
