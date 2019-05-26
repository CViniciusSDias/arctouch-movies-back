<?php

namespace App\Repository;

use App\Helper\MovieDbApiResponseParserTrait;
use GuzzleHttp\ClientInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GenreRepository
{
    use MovieDbApiResponseParserTrait;

    /** @var array */
    private $genres;
    /** @var CacheItemPoolInterface */
    private $cachingSystem;

    public function __construct(
        ClientInterface $httpClient,
        ParameterBagInterface $parameterBag,
        CacheItemPoolInterface $cachingSystem
    ) {
        $this->httpClient = $httpClient;
        $this->genres = [];
        $this->parameterPag = $parameterBag;
        $this->cachingSystem = $cachingSystem;
    }

    public function getGenreById(int $id): string
    {
        if (array_key_exists($id, $this->genres)) {
            return $this->genres[$id];
        }

        if (!empty($this->genres)) {
            throw new \DomainException('Unknown genre');
        }

        $this->fetchGenres();

        return $this->getGenreById($id);
    }

    private function fetchGenres(): void
    {
        $cachedGenres = $this->cachingSystem->getItem('genres.all');
        if ($cachedGenres->isHit()) {
            $this->genres = $cachedGenres->get();

            return;
        }

        $responseData = $this->fetchResponseData('/genre/movie/list');

        if (!is_array($responseData['genres']) || empty($responseData['genres'])) {
            throw new \DomainException('No genres found. API Error', 404);
        }

        foreach ($responseData['genres'] as $genre) {
            $this->genres[$genre['id']] = $genre['name'];
        }

        $cachedGenres->set($this->genres);
        $this->cachingSystem->save($cachedGenres);
    }
}
