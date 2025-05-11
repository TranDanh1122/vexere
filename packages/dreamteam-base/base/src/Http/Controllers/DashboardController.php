<?php

namespace DreamTeam\Base\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends AdminController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $requests)
    {
        $currentUser = \Auth::guard('admin')->user();
        return view('Core::home', compact('currentUser'));
    }
}
