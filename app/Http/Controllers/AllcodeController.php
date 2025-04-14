<?php

namespace App\Http\Controllers;
use App\Models\Allcode;
use Illuminate\Http\Request;

class AllcodeController extends Controller
{
    // function getAllRole($type)
    // {
    //     $allrole = Allcode::where('type', $type)->get();
    //     //return response()->json($allrole);
    //     return $allrole;
    // }
    function getAllCode(Request $request)
    {
        $type = $request->query('type');
        $allrole = Allcode::where('type', $type)->get();
        //return response()->json($allrole);
        return $allrole;
    }
}