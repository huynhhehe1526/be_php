<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

use function Clue\StreamFilter\fun;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createMovie(Request $req)
    {

        $movie = new Movie;

        $movie->title = $req->input('title');
        $movie->image = $req->file('image')->store('image11');
        $movie->genreId = $req->input('genreId');
        $movie->director = $req->input('director');
        $movie->statusId = $req->input('statusId');
        $movie->preview = $req->input('preview');
        $movie->duration = $req->input('duration');
        $movie->actor = $req->input('actor');
        $movie->premiere_date = Carbon::createFromFormat('d/m/Y', $req->input('premiere_date'))->toDateString();
        $movie->subtitle = $req->input('subtitle');
        $movie->video = $req->input('video');
        $movie->save();
        return $movie;
    }

    public function updateMovie(Request $data)
    {
        try {
            $movie = Movie::find($data->id);
            if ($movie) {

                $updateRequired = false;

                //check image in storage image11

                if ($data->hasFile('image') && $movie->image !== $data->file('image')) {
                    Storage::delete($movie->image);
                    $movie->image = $data->file('image')->store('image11');
                    $updateRequired = true;
                }


                if ($movie->title != $data->input('title')) {
                    $movie->title = $data->input('title');
                    $updateRequired = true;
                }
                if ($movie->genreId != $data->input('genreId')) {
                    $movie->genreId = $data->input('genreId');
                    $updateRequired = true;
                }

                if ($movie->director = $data->input('director')) {
                    $movie->director = $data->input('director');
                    $updateRequired = true;
                }
                if ($movie->statusId = $data->input('statusId')) {
                    $movie->statusId = $data->input('statusId');
                    $updateRequired = true;
                }


                if ($movie->preview = $data->input('preview')) {
                    $movie->preview = $data->input('preview');
                    $updateRequired = true;
                }
                if ($movie->duration = $data->input('duration')) {
                    $movie->duration = $data->input('duration');
                    $updateRequired = true;
                }



                if ($movie->actor = $data->input('actor')) {
                    $movie->actor = $data->input('actor');
                    $updateRequired = true;
                }

                //date
                //$movie->premiere_date = Carbon::createFromFormat('d/m/Y', $req->input('premiere_date'))->toDateString();
                if ($movie->premiere_date) {
                    //$data1 = $data->input('premiere_date');
                    $movie->premiere_date = Carbon::createFromFormat('d/m/Y', $data->input('premiere_date'))->toDateString();
                    // $movie->premiere_date  = $data->input('premiere_date')->startOfDay()->eq(now()->startOfDay())->toDateString();
                    $updateRequired = true;
                }



                if ($movie->subtitle = $data->input('subtitle')) {
                    $movie->subtitle = $data->input('subtitle');
                    $updateRequired = true;
                }

                if ($movie->video != $data->input('video')) {
                    $movie->video = $data->input('video');
                    $updateRequired = true;
                }


                if ($updateRequired) {
                    $movie->save();
                    return [
                        'errCode' => 0,
                        'message' => 'Cập nhật phim thành công!'
                    ];
                } else {
                    return [
                        'errCode' => 3,
                        'message' => 'Dữ liệu không thay đổi, không cần cập nhật'
                    ];
                }
            } else {
                return [
                    'errCode' => 1,
                    'message' => 'Không tìm thấy phim'
                ];
            }
        } catch (\Exception $e) {
            return [
                'errCode' => 500,
                'message' => $e->getMessage()
            ];
        }
    }




    public function listMovie(Request $req)
    {
        $defaultLimit = 10;
        $limit = $req->query('limit', $defaultLimit);

        $movies = Movie::take($limit)->get();
        return response()->json(['error' => 0, 'data' => $movies], 200);
        //return Movie::all();
    }

    public function getAllMovie()
    {

        //c2
        // $movies = Movie::with(['associate_genre',  'status_movie'])->get()->map(function ($movie) {
        //     return [
        //         'title' => $movie->title,
        //         'genreId' => $movie->associate_genre->nameGenre,
        //         'statusId' => $movie->status_movie->valueVi,$movie->status_movie->valueEn,
        //         // 'valueEn' => $movie->status_movie->valueEn
        //     ];
        // });

        //c1
        $movies = Movie::with([
            'associate_genre' => function ($query) {
                $query->select('id', 'nameGenre', 'valueVi', 'valueEn');
            },
            'status_movie' => function ($query) {
                $query->select('keyMap', 'valueVi', 'valueEn');
            }
        ])->get();

        if ($movies) {
            return [
                'error' => 0,
                'data' => $movies
            ];
        }

        return [
            'error' => 1,
            'data' => $movies
        ];
    }



    public function deleteMovie(Request $request)
    {
        if (!$request->has('id')) {
            return [
                'error' => 1,
                'errMessage' => 'Missing required parameter: id'
            ];
        }

        $foundMovie = Movie::find($request->input('id'));

        if ($foundMovie) {
            $foundMovie->delete();
            return [
                'error' => 0,
                'errMessage' => 'Delete movie succeeded!'
            ];
        } else {
            return [
                'error' => 2,
                'errMessage' => 'No movie found with the given id'
            ];
        }
    }

    public function getMoviesByGenre(Request $request)
    {
        $genreId = $request->query('id');
        if ($genreId) {
            $movies = Movie::whereHas('associate_genre', function ($query) use ($genreId) {
                $query->where('id', $genreId);
            })->with(['associate_genre' => function ($query) {
                $query->select('id', 'nameGenre',  'valueVi', 'valueEn');
            }])->get();
            return [
                'error' => 0,
                'data' => $movies
            ];
            //return $movies;
        } else {
            return [
                'error' => 1,
                'errMessage' => 'Missing required parameter'
            ];
        }
    }




    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function hienthi()
    {
        return 'Không quan tâm';
    }

    // public function detailMovie(Request $request)
    // {

    //     $idInput = $request->query('id');
    //     if (!$idInput) {
    //         return [
    //             'error' => 2,
    //             'message' => 'missing required params'
    //         ];
    //     } else {
    //         $movie = Movie::where('id', $idInput)->first();
    //         //$genreIds = Movie::select('genreId')->get();
    //         $genreIds = $movie->pluck('genreId');
    //         $movie = Movie::whereHas('associate_genre', function ($query) use ($genreIds) {
    //             $query->where('id',  $genreIds);
    //         })->with(['associate_genre' => function ($query) {
    //             $query->select('id', 'nameGenre', 'valueVi', 'valueEn');
    //         }])->get('id');



    //         // Xác định tên của "statusId" trong bảng "role"
    //         $movie = Movie::with(['status_movie'])->get();
    //         if ($movie) {
    //             return [
    //                 'error' => 0,
    //                 'data' => $movie
    //             ];
    //         } else {
    //             return [
    //                 'error' => 1,
    //                 'message' => 'Không tìm thấy phim'
    //             ];
    //         }
    //     }
    // }



    //sd cái này
    // public function detailMovie(Request $request)
    // {
    //     $idInput = $request->query('id');
    //     if (!$idInput) {
    //         return (object) [
    //             'error' => 2,
    //             'message' => 'missing required params'
    //         ];
    //     } else {
    //         $movie = Movie::where('id', $idInput)->first();

    //         if ($movie) {
    //             return (object) [
    //                 'error' => 0,
    //                 'data' => $movie
    //             ];
    //         } else {
    //             return (object) [
    //                 'error' => 1,
    //                 'message' => 'Không tìm thấy phim'
    //             ];
    //         }
    //     }
    // }


    public function detailMovie(Request $request)
    {
        $idInput = $request->query('id');
        if (!$idInput) {
            return [
                'error' => 2,
                'message' => 'missing required params'
            ];
        } else {

            //c1
            // $movie = Movie::with('associate_genre', 'status_movie')
            //     ->select('id', 'title', 'genreId', 'statusId')
            //     ->with(['associate_genre' => function ($query) {
            //         $query->select('id', 'nameGenre', 'valueVi', 'valueEn');
            //     }, 'status_movie' => function ($query) {
            //         $query->select('keyMap', 'valueVi', 'valueEn');
            //     }])
            //     ->where('id', $idInput)
            //     ->first();

            //c2

            $movie = Movie::with(['associate_genre' => function ($query) {
                $query->select('id', 'nameGenre', 'valueVi', 'valueEn');
            }, 'status_movie' => function ($query) {
                $query->select('keyMap', 'valueVi', 'valueEn');
            }])
                // ->select('id', 'title', 'genreId', 'statusId')
                ->get()
                ->where('id', $idInput)
                ->first();


            if ($movie) {
                return [
                    'error' => 0,
                    'data' => $movie
                ];
            } else {
                return [
                    'error' => 1,
                    'message' => 'Không tìm thấy phim'
                ];
            }
        }
    }
    public function getMoviesByGenreAndStatus(Request $request)
    {
        $genreId = $request->query('genreId');
        $statusId = $request->query('statusId');

        if ($genreId && $statusId) {
            $movies = Movie::whereHas('associate_genre', function ($query) use ($genreId) {
                $query->where('id', $genreId);
            })->where('statusId', $statusId)
                ->with(['associate_genre' => function ($query) {
                    $query->select('id', 'nameGenre', 'valueVi', 'valueEn');
                }])
                ->get();

            return [
                'error' => 0,
                'data' => $movies
            ];
        } else {
            return [
                'error' => 1,
                'errMessage' => 'Param required'
            ];
        }
    }
    public function getMoviesByStatus(Request $request)
    {
        $statusId = $request->query('statusId');

        if ($statusId) {
            $movies = Movie::where('statusId', $statusId)
                ->with(['associate_genre' => function ($query) {
                    $query->select('id', 'nameGenre', 'valueVi', 'valueEn');
                }])
                ->get();

            return [
                'error' => 0,
                'data' => $movies
            ];
        } else {
            return [
                'error' => 1,
                'errMessage' => 'Param required'
            ];
        }
    }
}