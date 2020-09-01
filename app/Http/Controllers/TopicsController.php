<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class TopicsController extends Controller
{
    public function index()
    {
        return view('topics.index');
    }
}
