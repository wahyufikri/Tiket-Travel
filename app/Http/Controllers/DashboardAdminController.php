<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardAdminController extends Controller
{
    public function Dashboard()
    {
        $income = [1000, 2000, 1500, 3000, 2500, 4000, 3500];
        $outcome = [500, 1000, 750, 1500, 1250, 2000, 1750];
        $labels = ['January', 'February', 'March', 'April', 'May', 'June', 'July'];
        return view('dashboard.Welcome',compact('income', 'outcome', 'labels'));
    }
}
