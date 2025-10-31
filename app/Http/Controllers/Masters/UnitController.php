<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        return view('masters.units.index');
    }
}
