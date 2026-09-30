<?php

namespace App\Http\Controllers\Web\Application;

use App\Http\Controllers\Controller;
Class ApplicationController extends Controller
{
    public function index(){
        return view('pages.all-application');
    }
}
