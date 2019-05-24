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
        $specificDate = SpecificDate::fromString($startDate);
        if (!is_null($this->endDate) && $specificDate->getDateTime() >= $this->endDate->getDateTime()) {
            throw new \InvalidArgumentException('Start date must be before end date', 500);
        }

        $this->startDate = $specificDate;
        return $this;
    }

    public function setEndDate(string $endDate): self
    {
        $specificDate = SpecificDate::fromString($endDate);
        if (!is_null($this->startDate) && $specificDate->getDateTime() <= $this->startDate->getDateTime()) {
            throw new \InvalidArgumentException('End date must be after start date', 500);
        }

        $this->endDate = $specificDate;
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
