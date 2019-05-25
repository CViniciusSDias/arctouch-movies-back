<?php

namespace App\Tests\Repository;

use App\Helper\MovieFactory;
use App\Model\Movie;
use App\Model\MovieList;
use App\Model\UpcomingMoviesList;
use App\Repository\MoviesRepository;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MoviesRepositoryTest extends TestCase
{
    private $movieMock;
    /** @var MockObject */
    private $movieFactoryMock;
    /** @var MockObject */
    private $cacheMock;

    protected function setUp(): void
    {
        $this->movieMock = $this
            ->getMockBuilder(Movie::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->movieFactoryMock = $this
            ->getMockBuilder(MovieFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cacheMock = $this->createMock(CacheItemPoolInterface::class);
    }

    public function testRetrieveUpcomingMovies()
    {
        $url = 'https://api.themoviedb.org/3/movie/upcoming?api_key=1f54bd990f1cdfb230adb312546d765d&language=en-US&page=1&region=US';

        $responseMock = $this->getResponseMock(__DIR__ . '/../data/sample-upcoming-movies-response.txt');
        $httpClientMock = $this->getHttpClientMock($url, $responseMock);
        $this->movieFactoryMock->expects($this->exactly(2))
            ->method('createFromApiResultArray')
            ->willReturn($this->movieMock);
        $parameterBagMock = $this->getParameterBagMock();
        $cacheMock = $this->getCacheMock('movies.upcoming');

        $repository = new MoviesRepository($httpClientMock, $this->movieFactoryMock, $parameterBagMock, $cacheMock);

        $upcomingMovieList = $repository->retrieveUpcomingMoviesList();

        static::assertInstanceOf(UpcomingMoviesList::class, $upcomingMovieList);
        static::assertCount(2, $upcomingMovieList->getMovies());
    }

    public function testRetrieveMovieDetails()
    {
        $url = 'https://api.themoviedb.org/3/movie/123456?api_key=1f54bd990f1cdfb230adb312546d765d&language=en-US';

        $responseMock = $this->getResponseMock(__DIR__ . '/../data/sample-movie-detail-response.txt');
        $httpClientMock = $this->getHttpClientMock($url, $responseMock);
        $this->movieFactoryMock->expects($this->once())
            ->method('createFromApiResultArray')
            ->willReturn($this->movieMock);
        $parameterBagMock = $this->getParameterBagMock();

        $repository = new MoviesRepository($httpClientMock, $this->movieFactoryMock, $parameterBagMock, $this->cacheMock);
        $movie = $repository->retrieveMovieDetails(123456);

        static::assertInstanceOf(Movie::class, $movie);
    }

    public function testRetrieveMovieListByQuery()
    {
        $query = 'Query';
        $url = 'https://api.themoviedb.org/3/search/movie?api_key=1f54bd990f1cdfb230adb312546d765d&language=en-US&page=1&region=US&query=' . $query;

        $responseMock = $this->getResponseMock(__DIR__ . '/../data/sample-movies-by-query-response.txt');
        $httpClientMock = $this->getHttpClientMock($url, $responseMock);
        $this->movieFactoryMock->expects($this->once())
            ->method('createFromApiResultArray')
            ->willReturn($this->movieMock);
        $parameterBagMock = $this->getParameterBagMock();
        $cacheKey = 'movies.query.' . md5($query);
        $cacheMock = $this->getCacheMock($cacheKey);

        $repository = new MoviesRepository($httpClientMock, $this->movieFactoryMock, $parameterBagMock, $cacheMock);
        $movieList = $repository->retrieveMovieListByQuery($query);

        static::assertInstanceOf(MovieList::class, $movieList);
        static::assertCount(1, $movieList->getMovies());
    }

    private function getResponseMock(string $dataFilePath)
    {
        $streamMock = $this->createMock(StreamInterface::class);
        $streamMock->expects($this->once())
            ->method('__toString')
            ->willReturn(file_get_contents($dataFilePath));

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->expects($this->once())
            ->method('getBody')
            ->willReturn($streamMock);
        $responseMock->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        return $responseMock;
    }

    private function getHttpClientMock(string $url, MockObject $responseMock)
    {
        $httpClientMock = $this->createMock(ClientInterface::class);
        $httpClientMock->expects($this->once())
            ->method('request')
            ->with('GET', $url)
            ->willReturn($responseMock);

        return $httpClientMock;
    }

    private function getParameterBagMock()
    {
        $parameterBagMock = $this->createMock(ParameterBagInterface::class);
        $parameterBagMock->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(
                [$this->equalTo('api_url')],
                [$this->equalTo('api_key')],
                )->willReturnOnConsecutiveCalls('https://api.themoviedb.org/3', '1f54bd990f1cdfb230adb312546d765d');

        return $parameterBagMock;
    }

    private function getCacheMock(string $cacheKey)
    {
        $itemMock = $this->createMock(CacheItemInterface::class);
        $itemMock->expects($this->once())
            ->method('isHit')
            ->willReturn(false);

        $this->cacheMock->expects($this->once())
            ->method('getItem')
            ->with($this->equalTo($cacheKey))
            ->willReturn($itemMock);

        return $this->cacheMock;
    }
}
