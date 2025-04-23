<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TmdbService;
use Illuminate\Http\Request;

class MovieApiController extends Controller
{
    //
    public function getPopularMovies(TmdbService $tmdbService) {
        $movies = $tmdbService->getPopularMovies();
        return response()->json($movies); // Return data JSON
    }
}
