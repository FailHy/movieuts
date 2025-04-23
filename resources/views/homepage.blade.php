@extends('layout.template')

@section('title', 'Homepage')

@section('content')

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>    
@endif

<h1>Popular Movies</h1>
<div class="row">
    @foreach ($movies as $movie)
    <div class="col-lg-6">
        <div class="card mb-3" style="max-width: 540px;">
            <div class="row g-0">
                <div class="col-md-4">
                    <img 
                        src="{{ isset($movie['poster_path']) ? 'https://image.tmdb.org/t/p/w500' . $movie['poster_path'] : '/images/' . ($movie['foto_sampul'] ?? 'default.jpg') }}" 
                        class="img-fluid rounded-start" 
                        alt="{{ $movie['title'] ?? $movie['judul'] }}">
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <h5 class="card-title">{{ $movie['title'] ?? $movie['judul'] }}</h5>
                        <p class="card-text">{{ $movie['overview'] ?? $movie['sinopsis'] }}</p>
                        <a href="/movie/{{ $movie['id'] }}" class="btn btn-success">Lihat Selanjutnya</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
