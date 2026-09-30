<?php

namespace App\Http\Controllers\Application;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
Class ApplicationController extends Controller
{
    public function index(){
        return view('pages.allApplication');
    }
}
