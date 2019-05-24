<?php

namespace App\Controller;

use App\Repository\MoviesRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class MoviesController
{
    /**
     * @var MoviesRepository
     */
    private $moviesRepository;

    public function __construct(MoviesRepository $moviesRepository)
    {
        $this->moviesRepository = $moviesRepository;
    }

    public function upcomingMovies(): Response
    {
        $movieList = $this->moviesRepository->retrieveUpcomingMovieList();

        return new JsonResponse($movieList, 200, ['Access-Control-Allow-Origin' => '*']);
    }

    public function movieDetails(int $movieId): Response
    {
        $movie = $this->moviesRepository->retrieveMovieDetails($movieId);

        return new JsonResponse($movie, 200, ['Access-Control-Allow-Origin' => '*']);
    }

    public function byQuery(string $query): Response
    {
        $movieList = $this->moviesRepository->retrieveMovieListByQuery($query);

        return new JsonResponse($movieList, 200, ['Access-Control-Allow-Origin' => '*']);
    }
}
