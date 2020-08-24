<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function index(){
        return view('welcome');
    }

    public function better_release_radar(){
        return view('better_release_radar');
    }

    public function about(){
        return view('about');
    }

    public function contact(){
        return view('contact');
    }
}
