<?php

namespace App\Repository;

use App\Helper\MovieDbApiResponseParserTrait;
use App\Helper\MovieFactory;
use App\Model\Movie;
use App\Model\MovieList;
use App\Model\UpcomingMovieList;
use GuzzleHttp\ClientInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MoviesRepository
{
    use MovieDbApiResponseParserTrait;

    /**
     * @var MovieFactory
     */
    private $movieFactory;

    /** @var CacheItemPoolInterface */
    private $cachingSystem;

    public function __construct(ClientInterface $httpClient, MovieFactory $movieFactory, ParameterBagInterface $parameterBag, CacheItemPoolInterface $cachingSystem)
    {
        $this->httpClient = $httpClient;
        $this->parameterPag = $parameterBag;
        $this->movieFactory = $movieFactory;
        $this->cachingSystem = $cachingSystem;
    }

    public function retrieveUpcomingMovieList(): UpcomingMovieList
    {
        $cachedMovies = $this->cachingSystem->getItem('movies.upcoming');

        if ($cachedMovies->isHit()) {
            return $cachedMovies->get();
        }

        $movieList = new UpcomingMovieList();
        $this->retrievePaginatedMovies(
            $movieList,
            '/movie/upcoming',
            ['region' => 'US'],
            function (array $responseData) use ($movieList) {
                $movieList
                    ->setStartDate($responseData['dates']['minimum'])
                    ->setEndDate($responseData['dates']['maximum']);
            }
        );

        $cachedMovies->expiresAt(new \DateTimeImmutable('tomorrow'));
        $cachedMovies->set($movieList);
        $this->cachingSystem->save($cachedMovies);

        return $movieList;
    }

    public function retrieveMovieDetails(int $movieId): Movie
    {
        $responseData = $this->fetchResponseData('/movie/' . $movieId);

        $movie = $this->movieFactory->createFromApiResultArray($responseData);
        return $movie;
    }

    public function retrieveMovieListByQuery(string $query): MovieList
    {
        $hashedQuery = md5($query);
        $cachedMovies = $this->cachingSystem->getItem("movies.query.$hashedQuery");
        if ($cachedMovies->isHit()) {
            return $cachedMovies->get();
        }

        $movieList = new MovieList();
        $this->retrievePaginatedMovies($movieList, '/search/movie', [
            'region' => 'US',
            'query' => $query
        ]);

        $cachedMovies->set($movieList);
        $cachedMovies->expiresAfter(new \DateInterval('P1W'));
        $this->cachingSystem->save($cachedMovies);

        return $movieList;
    }

    private function retrievePaginatedMovies(
        MovieList $movieList,
        string $path,
        array $queryParams,
        callable $callback = null
    ): void {
        $page = 1;

        do {
            $data = array_merge(['page' => $page], $queryParams);
            $responseData = $this->fetchResponseData($path, $data);

            if (!is_null($callback)) {
                $callback($responseData);
            }

            $pageMovies = array_map([$this->movieFactory, 'createFromApiResultArray'], $responseData['results']);
            $movieList->addMultipleMovies($pageMovies);
        } while ($page++ < $responseData['total_pages']);
    }
}
