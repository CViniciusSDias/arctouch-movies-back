<?php

namespace App\Repository;

use App\Helper\ResponseParserTrait;
use App\Model\Movie;
use App\Model\MovieList;
use GuzzleHttp\ClientInterface;

class MoviesRepository
{
    use ResponseParserTrait;

    private const API_ENDPOINT = 'https://api.themoviedb.org/3';
    private const API_KEY = '1f54bd990f1cdfb230adb312546d765d';
    /**
     * @var GenreRepository
     */
    private $genreRepository;

    public function __construct(ClientInterface $httpClient, GenreRepository $genreRepository)
    {
        $this->httpClient = $httpClient;
        $this->genreRepository = $genreRepository;
    }

    public function retrieveUpcomingMovieList(): MovieList
    {
        $page = 1;

        $movieList = new MovieList();
        do {
            $url = $this->assembleApiUrl('/movie/upcoming', [
                'page' => $page,
                'region' => 'US',
            ]);
            $responseData = $this->fetchResponseData($url);

            $pageMovies = array_map([$this, 'parseMovie'], $responseData['results']);
            $movieList->addMultipleMovies($pageMovies);
        } while ($page++ < $responseData['total_pages']);

        return $movieList;
    }

    private function parseMovie(array $result)
    {
        $imagePath = $result['poster_path'] ?? $result['backdrop_path'];
        $genres = array_map([$this->genreRepository, 'getGenreById'], $result['genre_ids']);
        $releaseDate = new \DateTime($result['release_date']);

        return new Movie($result['title'], $imagePath, $genres, $releaseDate);
    }
}
