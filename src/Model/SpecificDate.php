<?php

namespace App\Model;

class SpecificDate
{
    /** @var \DateTime */
    private $dateTime;

    public function __construct(\DateTime $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    public static function fromString(string $date)
    {
        return new self(new \DateTime($date));
    }

    public function __toString()
    {
        return $this->dateTime->format('Y-m-d');
    }
}
