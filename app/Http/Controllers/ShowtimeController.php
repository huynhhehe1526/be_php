<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Showtime;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use PhpParser\Node\Expr\FuncCall;

use function Clue\StreamFilter\fun;

class ShowtimeController extends Controller
{

    public function showDetailShowtimeById(Request $req)
    {
        if (!$req->id) {
            return [
                'error' => 1,
                'message' => 'Missing params required'
            ];
        } else {
            //Tìm thông tin chi tiết cho lịch chiếu
            $detail = Showtime::where('id', $req->id)
                ->with([
                    'movie' => function ($query) {
                        $query->select('id', 'title', 'director', 'genreId');
                        $query->with([
                            'associate_genre' => function ($query) {
                                $query->select('id', 'nameGenre', 'valueVi', 'valueEn');
                            },
                        ]);
                    },
                    'location' => function ($query) {
                        $query->select('id', 'valueVi', 'valueEn');
                    },
                ])
                ->first();
            if ($detail) {
                return [
                    'error' => 0,
                    'data' => $detail
                ];
            }
            return [
                'message' => 'Not found info in database'
            ];
        }
    }

    public function showAllShowtime()
    {
        // $showtime = Showtime::all();
        $showtime = Showtime::with([
            'movie' => function ($query) {
                $query->select('id', 'title');
            },
            'location' => function ($query) {
                $query->select('id', 'nameLocation', 'valueVi', 'valueEn');
            }
        ])
            ->get();
        return [
            'error' => 0,
            'message' => 'Lấy thông tin lịch chiếu thành công....',
            'data' => $showtime
        ];
    }

    public function showAllShowtimeByMovieId(Request $request)
    {

        $movieId = $request->query('id');

        if (!$movieId) {
            return [
                'error' => 1,
                'message' => 'Missing required params'
            ];
        } else {
            $location = Showtime::whereHas('movie', function ($query) use ($movieId) {
                $query->where('movie_id', $movieId);
            })->with([
                'movie' => function ($query) {
                    $query->select('id', 'title');
                },
                'location' => function ($query) {
                    $query->select('id', 'nameLocation', 'valueVi', 'valueEn');
                }
            ])
                ->get();

            if ($location->isNotEmpty()) {
                return [
                    'error' => 0,
                    'data' => $location
                ];
            } else {
                return [
                    'error' => 2,
                    'message' => 'Dữ liệu không tồn tại trong cơ sở dữ liệu'
                ];
            }
        }
    }

    //Dùng cái này
    // public function showAllShowtimeByMovieId(Request $request)
    // {
    //     $movieId = $request->query('id');

    //     if (!$movieId) {
    //         return [
    //             'error' => 1,
    //             'message' => 'Thiếu thông số yêu cầu'
    //         ];
    //     } else {
    //         $showtimes = Showtime::where('movie_id', $movieId)
    //             ->with([
    //                 'movie' => function ($query) {
    //                     $query->select('id', 'title');
    //                 },
    //                 'location' => function ($query) {
    //                     $query->select('id', 'nameLocation', 'valueVi', 'valueEn');
    //                 }
    //             ])

    //             ->get();

    //         $formattedShowtimes = [];

    //         foreach ($showtimes as $showtime) {
    //             $key = $showtime->date . '-' . $showtime->location_id;

    //             if (!isset($formattedShowtimes[$key])) {
    //                 $formattedShowtimes[$key] = [
    //                     'id' => $showtime->id,
    //                     'date' => $showtime->date,
    //                     'time' => [],
    //                     'movie_id' => $showtime->movie_id,
    //                     'location_id' => $showtime->location_id,
    //                     'title' => $showtime->movie->title,
    //                     'nameLocation' => $showtime->location->nameLocation,
    //                     'valueEn' => $showtime->location->valueEn,
    //                     'valueVi' => $showtime->location->valueVi, 
    //                 ];
    //             }

    //             $formattedShowtimes[$key]['time'][] = $showtime->time;
    //         }
    //         $result = [];
    //         foreach ($formattedShowtimes as $key => $item) {
    //             $times = implode(', ', array_reverse($item['time']));
    //             $explodedTimes = explode(', ', $times);
    //             $timeData = [];
    //             $timeData['time'] = $explodedTimes;
    //             // for ($i = 0; $i < count($explodedTimes); $i++) {
    //             //     $timeData['time ' . $i] = $explodedTimes[$i];
    //             // }

    //             $result[] = [
    //                 'id' => $item['id'],
    //                 'date' => $item['date'],
    //                 'time' => $timeData,
    //                 'movie_id' => $item['movie_id'],
    //                 'location_id' => $item['location_id'],
    //                 'title' => $item['title'],
    //                 'nameLocation' => $item['nameLocation'],
    //                 'valueEn' => $item['valueEn'],
    //                 'valueVi' => $item['valueVi'],
    //             ];
    //         }
    //         array_walk($result, function (&$value, $key) {
    //             asort($value['time']);
    //         });
    //         return [
    //             'error' => 0,
    //             'data' => $result
    //         ];
    //     }
    //     return [
    //         'error' => 2,
    //         'message' => 'Dữ liệu không tồn tại trong cơ sở dữ liệu'
    //     ];
    // }



    // public function showAllShowtimeByMovieId(Request $request)
    // {
    //     $movieId = $request->query('id');
    //     $showtimes = Showtime::where('movie_id', $movieId)
    //         ->with([
    //             'movie' => function ($query) {
    //                 $query->select('id', 'title');
    //             },
    //             'location' => function ($query) {
    //                 $query->select('id', 'nameLocation', 'valueVi', 'valueEn');
    //             }
    //         ])
    //         ->select('movie_id', 'date', 'location_id', 'time')
    //         ->distinct('show_date', 'location_id')
    //         ->get();

    //     if ($showtimes->isNotEmpty()) {
    //         return [
    //             'error' => 0,
    //             'data' => $showtimes
    //         ];
    //     } else {
    //         return [
    //             'error' => 2,
    //             'message' => 'Dữ liệu không tồn tại trong cơ sở dữ liệu'
    //         ];
    //     }
    // }

    // public function showAllShowtimeByMovieId(Request $request)
    // {
    //     $movieId = $request->query('id');

    //     if (!$movieId) {
    //         return [
    //             'error' => 1,
    //             'message' => 'Thiếu thông số yêu cầu'
    //         ];
    //     } else {
    //         $showtimes = Showtime::where('movie_id', $movieId)
    //             ->with([
    //                 'movie' => function ($query) {
    //                     $query->select('id', 'title');
    //                 },
    //                 'location' => function ($query) {
    //                     $query->select('id', 'nameLocation', 'valueVi', 'valueEn');
    //                 }
    //             ])
    //             ->select('date', 'location_id', 'time') // Chọn các cột date, location_id, time
    //             ->distinct('date', 'location_id') // Chỉ lấy các dòng duy nhất dựa trên date và location_id
    //             ->get();

    //         $formattedShowtimes = $showtimes->groupBy('date', 'location_id')->map(function ($items) {
    //             $timeData = collect($items->pluck('time')->first())->sortByDesc('time')->map(function ($time) {
    //                 return ['time' => $time];
    //             });
    //             $location = $items->first()->location;
    //             return [
    //                 'date' => $items->first()->date,
    //                 'time' => $timeData,
    //                 'movie_id' => $items->first()->movie_id,
    //                 'location_id' => $items->first()->location_id,
    //                 'nameLocation' => $location->nameLocation,
    //                 'valueEn' => $location->valueEn,
    //                 'valueVi' => $location->valueVi,
    //             ];
    //         });

    //         return [
    //             'error' => 0,
    //             'data' => $formattedShowtimes->values()->all()
    //         ];
    //     }

    //     return [
    //         'error' => 2,
    //         'message' => 'Dữ liệu không tồn tại trong cơ sở dữ liệu'
    //     ];
    // }

    // public function showAllShowtimeByMovieId(Request $request)
    // {
    //     $movieId = $request->query('id');
    //     $showtimes = Showtime::select('date', 'location_id')
    //         ->where('movie_id', $movieId)
    //         ->with([
    //             'movie' => function ($query) {
    //                 $query->select('id', 'title');
    //             },
    //             'location' => function ($query) {
    //                 $query->select('id', 'nameLocation', 'valueVi', 'valueEn');
    //             }
    //         ])
    //         ->distinct()
    //         ->groupBy('date', 'location_id')
    //         ->get();

    //     if ($showtimes->isNotEmpty()) {
    //         return [
    //             'error' => 0,
    //             'data' => $showtimes
    //         ];
    //     } else {
    //         return [
    //             'error' => 2,
    //             'message' => 'Dữ liệu không tồn tại trong cơ sở dữ liệu'
    //         ];
    //     }
    // }





    public function createShowtime(Request $request)
    {
        $existingShowtime = Showtime::where('movie_id', $request->movie_id)
            ->where('location_id', $request->location_id)
            ->where('time', $request->time)
            ->where('date', Carbon::createFromFormat('d/m/Y', $request->input('date'))->toDateString())
            ->first();


        if ($existingShowtime) {
            return response()->json([
                'error' => 1,
                'message' => 'The information is exist in the database.'
            ]);
        }


        $showtime = new Showtime();
        $showtime->movie_id = $request->movie_id;
        $showtime->location_id = $request->location_id;
        $showtime->time = $request->time;
        $showtime->date = Carbon::createFromFormat('d/m/Y', $request->input('date'))->toDateString();
        $showtime->save();

        // Trả về thông báo thành công
        return response()->json([
            'error' => 0,
            'message' => 'Create showtime successfull!',
            'data' => $showtime
        ]);
    }

    public function editShowtime(Request $request)
    {
        $showtime = Showtime::find($request->id);

        // Kiểm tra nếu không tìm thấy showtime
        if (!$showtime) {
            return response()->json([
                'error' => 1,
                'message' => 'Showtime not exist in .'
            ]);
        }

        $existingShowtime = Showtime::where('movie_id', '=',  $request->movie_id)
            ->where('location_id', '=', $request->location_id)
            ->where('time', '=', $request->time)
            ->where('date', '=', Carbon::createFromFormat('d/m/Y', $request->input('date'))->toDateString())
            ->first();
        // $existingShowtime = Showtime::where('movie_id', '=', $request->movie_id)
        //     ->where('location_id', '=', $request->location_id)
        //     ->where('time', '=', $request->time)
        //     ->where('date', '=',  Carbon::createFromFormat('d/m/Y', $request->input('date'))->toDateString())
        //     ->where('id', '!=', $request->input('id')) // Không so sánh với chính nó
        //     ->exist();


        if ($existingShowtime !== null) {
            return response()->json([
                'error' => 2,
                'message' => 'Thông tin đã tồn tại trong cơ sở dữ liệu.'
            ]);
        }

        // if ($existingShowtime) {
        //     return response()->json([
        //         'error' => 2,
        //         'message' => 'Thông tin đã tồn tại trong cơ sở dữ liệu.'
        //     ]);
        // }

        // Cập nhật
        $showtime->movie_id = $request->movie_id;
        $showtime->location_id = $request->location_id;
        $showtime->time = $request->time;
        $showtime->date = Carbon::createFromFormat('d/m/Y', $request->input('date'))->toDateString();
        $showtime->save();

        return response()->json([
            'error' => 0,
            'message' => 'Cập nhật thông tin lịch chiếu thành công!',
            'data' => $showtime
        ]);
    }

    //delete
    public function deleteShowtime(Request $request)
    {
        if (!$request->has('id')) {
            return [
                'error' => 1,
                'errMessage' => 'Missing required parameter: id'
            ];
        }

        $foundShowtime = Showtime::find($request->input('id'));

        if ($foundShowtime) {
            $foundShowtime->delete();
            return [
                'error' => 0,
                'errMessage' => 'Delete showtime succeeded!'
            ];
        } else {
            return [
                'error' => 2,
                'errMessage' => 'No showtime found with the given id'
            ];
        }
    }
}
