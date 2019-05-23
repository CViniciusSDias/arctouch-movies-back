<?php

namespace App\Model;

class UpcomingMovieList implements \JsonSerializable
{
    protected $movies;
    /** @var SpecificDate */
    private $startDate;
    /** @var SpecificDate */
    private $endDate;

    public function __construct()
    {
        $this->movies = [];
    }

    public function addMultipleMovies(array $moviesArray)
    {
        foreach ($moviesArray as $movie) {
            $this->addMovie($movie);
        }
    }

    /**
     * @return Movie[]
     */
    public function getMovies(): array
    {
        usort($this->movies, function (Movie $a, Movie $b) {
            return strcmp($a->name, $b->name);
        });

        return $this->movies;
    }

    public function setStartDate(string $startDate): self
    {
        $this->startDate = SpecificDate::fromString($startDate);
        return $this;
    }

    public function setEndDate(string $endDate): self
    {
        $this->endDate = SpecificDate::fromString($endDate);
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'dates' => [
                'start' => (string) $this->startDate,
                'end' => (string) $this->endDate,
            ],
            'movies' => $this->getMovies(),
        ];
    }

    protected function addMovie(Movie $movie)
    {
        $this->movies[] = $movie;
    }
}
