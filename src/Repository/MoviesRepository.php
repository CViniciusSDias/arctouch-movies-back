<?php

namespace App\Repository;

use App\Helper\ResponseParserTrait;
use App\Model\Movie;
use App\Model\SpecificDate;
use App\Model\UpcomingMovieList;
use GuzzleHttp\ClientInterface;

class MoviesRepository
{
    use ResponseParserTrait;

    /** @var GenreRepository */
    private $genreRepository;

    public function __construct(ClientInterface $httpClient, GenreRepository $genreRepository)
    {
        $this->httpClient = $httpClient;
        $this->genreRepository = $genreRepository;
    }

    public function retrieveUpcomingMovieList(): UpcomingMovieList
    {
        $page = 1;

        $movieList = new UpcomingMovieList();
        do {
            $url = $this->assembleApiUrl('/movie/upcoming', [
                'page' => $page,
                'region' => 'US',
            ]);
            $responseData = $this->fetchResponseData($url);
            $movieList
                ->setStartDate($responseData['dates']['maximum'])
                ->setEndDate($responseData['dates']['minimum']);

            $pageMovies = array_map([$this, 'parseMovie'], $responseData['results']);
            $movieList->addMultipleMovies($pageMovies);
        } while ($page++ < $responseData['total_pages']);

        return $movieList;
    }

    public function retrieveMovieDetails(int $movieId): Movie
    {
        $url = $this->assembleApiUrl('/movie/' . $movieId);
        $responseData = $this->fetchResponseData($url);

        $movie = $this->parseMovie($responseData);
        return $movie;
    }

    private function parseMovie(array $result): Movie
    {
        $imagePath = $result['poster_path'] ?? $result['backdrop_path'];
        $genres = array_key_exists('genre_ids', $result)
            ? array_map([$this->genreRepository, 'getGenreById'], $result['genre_ids'])
            : array_map(function (array $genreData) {
                return $genreData['name'];
            }, $result['genres']);
        $releaseDate = new SpecificDate(new \DateTime($result['release_date']));

        return new Movie($result['id'], $result['title'], $imagePath, $genres, $releaseDate, $result['overview']);
    }
}
