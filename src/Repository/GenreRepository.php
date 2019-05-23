<?php

namespace App\Repository;

use App\Helper\MovieDbApiResponseParserTrait;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GenreRepository
{
    use MovieDbApiResponseParserTrait;

    /** @var array */
    private $genres;

    public function __construct(ClientInterface $httpClient, ParameterBagInterface $parameterBag)
    {
        $this->httpClient = $httpClient;
        $this->genres = [];
        $this->parameterPag = $parameterBag;
    }

    public function getGenreById(int $id): string
    {
        if (array_key_exists($id, $this->genres)) {
            return $this->genres[$id];
        }

        if (!empty($this->genres)) {
            throw new \DomainException('Unknown genre');
        }

        $url = $this->assembleApiUrl('/genre/movie/list');
        $responseData = $this->fetchResponseData($url);

        if (!is_array($responseData['genres']) || empty($responseData['genres'])) {
            throw new \DomainException('No genres found. API Error', 404);
        }

        foreach ($responseData['genres'] as $genre) {
            $this->genres[$genre['id']] = $genre['name'];
        }

        return $this->getGenreById($id);
    }
}
