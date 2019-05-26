<?php

namespace App\Model;

class SpecificDate
{
    /** @var \DateTimeInterface */
    private $dateTime;

    public function __construct(\DateTimeInterface $dateTime)
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

    public function getDateTime(): \DateTimeInterface
    {
        if ($this->dateTime instanceof \DateTimeImmutable) {
            return $this->dateTime;
        }

        return \DateTimeImmutable::createFromMutable($this->dateTime);
    }
}
