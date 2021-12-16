<?php

namespace Cuonggt\Dibi\Http\Controllers;

use Cuonggt\Dibi\DatabaseRepositoryFactory;
use Cuonggt\Dibi\Dibi;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->database = DatabaseRepositoryFactory::make(Dibi::databaseConnection());
    }
}
