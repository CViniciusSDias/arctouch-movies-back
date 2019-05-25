<?php

namespace App\Tests\Helper;

use App\Helper\MovieFactory;
use App\Model\Movie;
use App\Model\SpecificDate;
use App\Repository\GenreRepository;
use PHPUnit\Framework\TestCase;

class MovieFactoryTest extends TestCase
{
    private $movieFactory;
    private $movie;

    protected function setUp(): void
    {
        $genreRepositoryMock = $this
            ->getMockBuilder(GenreRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $genreName = 'Genre Name';
        $genreRepositoryMock->expects($this->once())
            ->method('getGenreById')
            ->with($this->equalTo(1))
            ->willReturn($genreName);
        $movieFacotry = new MovieFactory($genreRepositoryMock);

        $this->movieFactory = $movieFacotry;

        $this->movie = new Movie(
            1,
            'Test title',
            'valid-path',
            ['Genre Name'],
            SpecificDate::fromString('2019-01-01'),
            ''
        );
    }

    public function testCreateFromValidApiResultWithoutPosterMustReturnMovie()
    {
        $movie = $this->movieFactory->createFromApiResultArray([
            'id' => 1,
            'backdrop_path' => 'valid-path',
            'genre_ids' => [1],
            'release_date' => '2019-01-01',
            'title' => 'Test title',
            'overview' => ''
        ]);

        static::assertInstanceOf(Movie::class, $movie);
        static::assertEquals($this->movie, $movie);
    }

    public function testCreateFromValidApiResultWithPosterMustReturnMovie()
    {
        $movie = $this->movieFactory->createFromApiResultArray([
            'id' => 1,
            'poster_path' => 'valid-path',
            'genre_ids' => [1],
            'release_date' => '2019-01-01',
            'title' => 'Test title',
            'overview' => ''
        ]);

        static::assertInstanceOf(Movie::class, $movie);
        static::assertEquals($this->movie, $movie);
    }

    public function testCreateFromValidApiResultWithoutImageMustReturnMovie()
    {
        $movie = $this->movieFactory->createFromApiResultArray([
            'id' => 1,
            'genre_ids' => [1],
            'release_date' => '2019-01-01',
            'title' => 'Test title',
            'overview' => ''
        ]);

        $expectedMovie = $this->movie = new Movie(
            1,
            'Test title',
            null,
            ['Genre Name'],
            SpecificDate::fromString('2019-01-01'),
            ''
        );
        static::assertInstanceOf(Movie::class, $movie);
        static::assertEquals($expectedMovie, $movie);
    }
}
