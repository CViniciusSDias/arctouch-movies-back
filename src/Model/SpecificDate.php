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

    public function __toString()
    {
        return $this->dateTime->format('Y-m-d');
    }
}
