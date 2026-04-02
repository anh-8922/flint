<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class TmdbService
{
    private PendingRequest $client;
    private string $imageUrl;

    public function __construct()
    {
        $this->imageUrl = config('services.tmdb.image_url');

        $this->client = Http::baseUrl(config('services.tmdb.base_url'))
            ->withQueryParameters(['api_key' => config('services.tmdb.key'), 'language' => 'en-US'])
            ->acceptJson();
    }

    // ── Movies ────────────────────────────────────────────────────────────────

    public function trending(string $timeWindow = 'week', int $page = 1): array
    {
        return $this->get("/trending/movie/{$timeWindow}", ['page' => $page]);
    }

    public function popular(int $page = 1): array
    {
        return $this->get('/movie/popular', ['page' => $page]);
    }

    public function topRated(int $page = 1): array
    {
        return $this->get('/movie/top_rated', ['page' => $page]);
    }

    public function nowPlaying(int $page = 1): array
    {
        return $this->get('/movie/now_playing', ['page' => $page]);
    }

    public function upcoming(int $page = 1): array
    {
        return $this->get('/movie/upcoming', ['page' => $page]);
    }

    public function movie(int $id, array $append = []): array
    {
        $params = $append ? ['append_to_response' => implode(',', $append)] : [];

        return $this->get("/movie/{$id}", $params);
    }

    public function search(string $query, int $page = 1): array
    {
        return $this->get('/search/movie', ['query' => $query, 'page' => $page]);
    }

    public function recommendations(int $movieId, int $page = 1): array
    {
        return $this->get("/movie/{$movieId}/recommendations", ['page' => $page]);
    }

    // ── Images ────────────────────────────────────────────────────────────────

    public function posterUrl(string $path, string $size = 'w500'): string
    {
        return "{$this->imageUrl}/{$size}{$path}";
    }

    public function backdropUrl(string $path, string $size = 'original'): string
    {
        return "{$this->imageUrl}/{$size}{$path}";
    }

    // ── Internal ──────────────────────────────────────────────────────────────

    private function get(string $endpoint, array $params = []): array
    {
        return $this->client->get($endpoint, $params)->json();
    }
}
