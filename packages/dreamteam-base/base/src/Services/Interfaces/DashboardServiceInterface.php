<?php

namespace DreamTeam\Base\Services\Interfaces;

use Illuminate\Http\Request;

interface DashboardServiceInterface
{
    public function dashboard(Request $requests, $currentUser);
}
