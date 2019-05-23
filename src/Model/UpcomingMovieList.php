<?php

namespace App\Model;

class UpcomingMovieList extends MovieList implements \JsonSerializable
{
    /** @var SpecificDate */
    private $startDate;
    /** @var SpecificDate */
    private $endDate;

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
        $serializedData = parent::jsonSerialize();
        $serializedData['dates'] = [
            'start' => (string) $this->startDate,
            'end' => (string) $this->endDate,
        ];

        return $serializedData;
    }
}
