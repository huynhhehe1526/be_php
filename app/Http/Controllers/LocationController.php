<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function getAllLocation()
    {
        $location = Location::all();
        return [
            'error' => 0,
            'data' => $location
        ];
    }
}
