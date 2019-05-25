<?php

namespace App\Controller;

use App\Repository\MoviesRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
        $movieList = $this->moviesRepository->retrieveUpcomingMoviesList();

        return new JsonResponse($movieList, 200, ['Access-Control-Allow-Origin' => '*']);
    }

    public function movieDetails(int $movieId): Response
    {
        $movie = $this->moviesRepository->retrieveMovieDetails($movieId);

        return new JsonResponse($movie, 200, ['Access-Control-Allow-Origin' => '*']);
    }

    public function byQuery(Request $request): Response
    {
        $query = filter_var($request->query->get('q'), FILTER_SANITIZE_STRING);
        if (!$query) {
            throw new \Exception('Required parameter "q" missing');
        }

        $movieList = $this->moviesRepository->retrieveMovieListByQuery($query);

        return new JsonResponse($movieList, 200, ['Access-Control-Allow-Origin' => '*']);
    }
}
