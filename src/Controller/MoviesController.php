<?php

namespace App\Controller;

use App\Repository\MoviesRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

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

    public function upcomingMovies()
    {
        $movieList = $this->moviesRepository->retrieveUpcomingMovieList();

        return new JsonResponse($movieList);
    }
}
