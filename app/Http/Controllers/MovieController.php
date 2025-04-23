<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Category;
use App\Services\TmdbService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;

class MovieController extends Controller
{
    public function index(TmdbService $tmdbService)
    {
        try {
            $apiMovies = $tmdbService->getPopularMovies();
        } catch (\Exception $e) {
            $apiMovies = [];
            logger()->error('TMDB API Error: ' . $e->getMessage());
        }

        $query = Movie::latest();
        if (request('search')) {
            $query->where('judul', 'like', '%' . request('search') . '%')
                  ->orWhere('sinopsis', 'like', '%' . request('search') . '%');
        }

        $localMovies = $query->paginate(6)->withQueryString();

        return view('homepage', [
            'movies' => $localMovies,
            'apiMovies' => $apiMovies
        ]);
    }

    public function detail(Movie $movie)
    {
        return view('detail', compact('movie'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('input', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->validateMovie($request);

        $fileName = $this->handleUploadFoto($request);

        Movie::create([
            'id' => $request->id,
            'judul' => $request->judul,
            'category_id' => $request->category_id,
            'sinopsis' => $request->sinopsis,
            'tahun' => $request->tahun,
            'pemain' => $request->pemain,
            'foto_sampul' => $fileName,
        ]);

        return redirect('/')->with('success', 'Data berhasil disimpan');
    }

    public function data()
    {
        $movies = Movie::latest()->paginate(10);
        return view('data-movies', compact('movies'));
    }

    public function form_edit(Movie $movie)
    {
        $categories = Category::all();
        return view('form-edit', compact('movie', 'categories'));
    }

    public function update(Request $request, Movie $movie)
    {
        $this->validateMovie($request, $movie->id);

        $fileName = $this->handleUploadFoto($request, $movie->foto_sampul);

        $movie->update([
            'judul' => $request->judul,
            'sinopsis' => $request->sinopsis,
            'category_id' => $request->category_id,
            'tahun' => $request->tahun,
            'pemain' => $request->pemain,
            'foto_sampul' => $fileName,
        ]);

        return redirect('/movies/data')->with('success', 'Data berhasil diperbarui');
    }

    public function delete(Movie $movie)
    {
        if (File::exists(public_path('images/' . $movie->foto_sampul))) {
            File::delete(public_path('images/' . $movie->foto_sampul));
        }

        $movie->delete();

        return redirect('/movies/data')->with('success', 'Data berhasil dihapus');
    }

    private function validateMovie(Request $request, $id = null)
    {
        $rules = [
            'judul' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'sinopsis' => 'required|string',
            'tahun' => 'required|integer',
            'pemain' => 'required|string',
        ];

        if (!$id) {
            $rules['id'] = ['required', 'string', 'max:255', Rule::unique('movies', 'id')];
            $rules['foto_sampul'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        } else {
            $rules['id'] = ['required', 'string', 'max:255', Rule::unique('movies', 'id')->ignore($id)];
            $rules['foto_sampul'] = 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }

        $request->validate($rules);
    }

    private function handleUploadFoto(Request $request, $oldFile = null)
    {
        if ($request->hasFile('foto_sampul')) {
            $randomName = Str::uuid()->toString();
            $ext = $request->file('foto_sampul')->getClientOriginalExtension();
            $fileName = $randomName . '.' . $ext;

            $request->file('foto_sampul')->move(public_path('images'), $fileName);

            if ($oldFile && File::exists(public_path('images/' . $oldFile))) {
                File::delete(public_path('images/' . $oldFile));
            }

            return $fileName;
        }

        return $oldFile;
    }
}