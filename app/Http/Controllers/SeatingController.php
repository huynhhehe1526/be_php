<?php

namespace App\Http\Controllers;

use App\Models\Seating;
use Illuminate\Http\Request;

class SeatingController extends Controller
{
    public function GetAllSeat()
    {
        $seat = Seating::with([
            'chair_allcodes' => function ($query) {
                $query->select('keyMap', 'valueVi', 'valueEn');
            },
            'statusSeat_allcodes' => function ($query) {
                $query->select('keyMap', 'valueVi', 'valueEn');
            },
            // 'price_allcodes' => function ($query) {
            //     $query->select('keyMap', 'valueVi', 'valueEn');
            // },
        ])
            ->get();
        if ($seat) {
            return [
                'error' => 0,
                'data' => $seat,
            ];
        }
        return [
            'error' => 1,
            'data' => $seat,
        ];
    }
}