<?php

namespace App\Http\Controllers;

use App\Services\TmdbService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
}
