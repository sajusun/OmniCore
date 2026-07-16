<?php

namespace App\Http\Controllers\Web\Backend;

use App\Models\User;
use App\Models\FoodLog;
use App\Models\FoodScan;
use App\Models\ActivityLog;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // User counts
        $totalUsers = User::count();

        // Subscribed users count
        $totalSubscribedUsers = User::whereHas('activeSubscription')->count();
        $totalFoodScans = FoodScan::count();
        $totalFoodLogs = FoodLog::count();

        // Recent data
        $latestFoodScans = FoodScan::latest()->take(5)->get();
        $latestFoodLogs = FoodLog::latest()->take(5)->get();
        $latestpostUsers = User::latest()->take(5)->get();
        $activityLogs = ActivityLog::latest()->take(5)->get();
        return view('backend.layouts.dashboard', compact(
            'totalUsers',
            'totalSubscribedUsers',
            'totalFoodScans',
            'totalFoodLogs',
            'latestFoodScans',
            'latestFoodLogs',
            'latestpostUsers',
            'activityLogs',
        ));
    }

    public function data()
    {
        return view('backend.layouts.data');
    }
}
