<?php

namespace App\Model;

class MovieList implements \JsonSerializable
{
    private $movies;

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

    private function addMovie(Movie $movie)
    {
        $this->movies[] = $movie;
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

    public function jsonSerialize(): array
    {
        return $this->getMovies();
    }
}
