<?php

namespace Cuonggt\Dibi\Http\Controllers;

use Cuonggt\Dibi\Contracts\DatabaseRepository;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(DatabaseRepository $database)
    {
        $this->database = $database;
    }
}
