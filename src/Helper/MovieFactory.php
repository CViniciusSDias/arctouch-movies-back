<?php

namespace App\Helper;

use App\Model\Movie;
use App\Model\SpecificDate;
use App\Repository\GenreRepository;

class MovieFactory
{
    /**
     * @var GenreRepository
     */
    private $genreRepository;

    public function __construct(GenreRepository $genreRepository)
    {
        $this->genreRepository = $genreRepository;
    }

    public function createFromApiResultArray(array $result): Movie
    {
        $imagePath = $result['poster_path'] ?? ($result['backdrop_path'] ?? null);
        $genres = $this->retrieveGenreNames($result);
        $releaseDate = SpecificDate::fromString($result['release_date']);

        return new Movie($result['id'], $result['title'], $imagePath, $genres, $releaseDate, $result['overview']);
    }

    private function retrieveGenreNames(array $result): array
    {
        if (array_key_exists('genre_ids', $result)) {
            return array_map([$this->genreRepository, 'getGenreById'], $result['genre_ids']);
        }

        return array_column($result['genres'], 'name');
    }
}
