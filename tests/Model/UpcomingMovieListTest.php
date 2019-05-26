<?php

namespace App\Tests\Model;

use App\Model\UpcomingMoviesList;
use PHPUnit\Framework\TestCase;

class UpcomingMovieListTest extends TestCase
{
    /** @var UpcomingMoviesList */
    private $list;

    protected function setUp(): void
    {
        $this->list = new UpcomingMoviesList();
    }

    public function testEndDateMustBeAfterStartDate()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('End date must be after start date');

        $this->list->setStartDate('2019-01-01');
        $this->list->setEndDate('2018-01-01');
    }

    public function testStartDateMustBeBeforeEndDate()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Start date must be before end date');

        $this->list->setEndDate('2019-01-01');
        $this->list->setStartDate('2019-01-02');
    }
}
