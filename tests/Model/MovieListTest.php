<?php

namespace App\Tests\Model;

use App\Model\Movie;
use App\Model\MovieList;
use PHPUnit\Framework\TestCase;

class MovieListTest extends TestCase
{
    public function testGetMoviesMustReturnOrderedMovieList()
    {
        $movie1 = $this->getMockBuilder(Movie::class)->disableOriginalConstructor()->getMock();
        $movie2 = $this->getMockBuilder(Movie::class)->disableOriginalConstructor()->getMock();
        $movie3 = $this->getMockBuilder(Movie::class)->disableOriginalConstructor()->getMock();

        $movie1->name  = 'A Movie';
        $movie2->name  = 'C Movie';
        $movie3->name  = 'B Movie';

        $movies = [$movie1, $movie2, $movie3];
        $list = new MovieList();
        $list->addMultipleMovies($movies);

        $orderedMovies = $list->getMovies();

        static::assertCount(3, $orderedMovies);
        static::assertEquals('A Movie', $orderedMovies[0]->name);
        static::assertEquals('B Movie', $orderedMovies[1]->name);
        static::assertEquals('C Movie', $orderedMovies[2]->name);
    }
}
