<?php

namespace App\Repository;

use App\Helper\MovieDbApiResponseParserTrait;
use App\Helper\MovieFactory;
use App\Model\Movie;
use App\Model\SpecificDate;
use App\Model\UpcomingMovieList;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MoviesRepository
{
    use MovieDbApiResponseParserTrait;

    /**
     * @var MovieFactory
     */
    private $movieFactory;

    public function __construct(ClientInterface $httpClient, MovieFactory $movieFactory, ParameterBagInterface $parameterBag)
    {
        $this->httpClient = $httpClient;
        $this->parameterPag = $parameterBag;
        $this->movieFactory = $movieFactory;
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

            $pageMovies = array_map([$this->movieFactory, 'createFromApiResultArray'], $responseData['results']);
            $movieList->addMultipleMovies($pageMovies);
        } while ($page++ < $responseData['total_pages']);

        return $movieList;
    }

    public function retrieveMovieDetails(int $movieId): Movie
    {
        $url = $this->assembleApiUrl('/movie/' . $movieId);
        $responseData = $this->fetchResponseData($url);

        $movie = $this->movieFactory->createFromApiResultArray($responseData);
        return $movie;
    }
}
