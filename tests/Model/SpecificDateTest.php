<?php

namespace App\Tests\Model;

use App\Model\SpecificDate;
use PHPUnit\Framework\TestCase;

class SpecificDateTest extends TestCase
{
    public function testSpecificDateToStringMustReturnFormattedDate()
    {
        $expectedDate = '2019-01-01';
        $specificDate = SpecificDate::fromString($expectedDate);
        $stringDate = (string) $specificDate;

        static::assertEquals($expectedDate, $stringDate);
    }
}
