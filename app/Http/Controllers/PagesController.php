<?php

namespace App\Http\Controllers;

use App\Models\Random;
use App\Services\Randomizer;

class PagesController extends Controller {
    
    protected $viewPath = "pages";


    public function index() {
        return $this->view("index");
    }

}
