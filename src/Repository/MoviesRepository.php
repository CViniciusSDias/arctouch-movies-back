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

    private function parseMovie(array $result)
    {
        $imagePath = $result['poster_path'] ?? $result['backdrop_path'];
        $genres = array_map([$this->genreRepository, 'getGenreById'], $result['genre_ids']);
        $releaseDate = new SpecificDate(new \DateTime($result['release_date']));

        return new Movie($result['id'], $result['title'], $imagePath, $genres, $releaseDate);
    }
}
