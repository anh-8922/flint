<?php

namespace App\Http\Controllers;

use App\Services\TmdbService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TmdbController extends Controller
{
    public function __construct(private TmdbService $tmdb) {}

    public function trending(): JsonResponse
    {
        return response()->json($this->tmdb->trending());
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate(['query' => 'required|string|max:200']);

        return response()->json($this->tmdb->search($request->string('query')));
    }

    public function show(int $id): View
    {
        $movie = $this->tmdb->movieDetail($id);

        // Pick first YouTube trailer
        $trailer = collect($movie['videos']['results'] ?? [])
            ->first(fn($v) => $v['site'] === 'YouTube' && $v['type'] === 'Trailer');

        // Director from crew
        $director = collect($movie['credits']['crew'] ?? [])
            ->first(fn($c) => $c['job'] === 'Director');

        // Top 8 cast
        $cast = array_slice($movie['credits']['cast'] ?? [], 0, 8);

        // Top 8 recommendations
        $recommendations = array_slice($movie['recommendations']['results'] ?? [], 0, 8);

        return view('movie.show', compact('movie', 'trailer', 'director', 'cast', 'recommendations'));
    }
}
