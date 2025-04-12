<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\System;

class FrontendController extends Controller
{
    protected $language;
    protected $systemRepository;
    protected $system;

    public function __construct()
    {

        $this->setLanguage();
        $this->setSystem();
    }

    public function setLanguage()
    {
        $this->language = 1;
    }

    public function setSystem()
    {
        $this->system = convert_array(System::all(), 'keyword', 'content');
    }
}
