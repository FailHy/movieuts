<?php

namespace App\Http\Controllers;
use App\Services\TmdbService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    //
    public function homepage(TmdbService $tmdbService)
{
    $movies = $tmdbService->getPopularMovies();
    return view('homepage', ['movies' => $movies['results']]);
}

}
