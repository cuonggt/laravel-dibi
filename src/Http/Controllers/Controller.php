<?php

namespace Cuonggt\Dibi\Http\Controllers;

use Cuonggt\Dibi\Contracts\DatabaseRepository;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * The database instance.
     *
     * @var \Cuonggt\Dibi\Contracts\DatabaseRepository
     */
    public $database;

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
