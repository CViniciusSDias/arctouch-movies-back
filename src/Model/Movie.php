<?php

namespace App\Model;

/**
 * @property string $name
 * @property string $imagePath
 * @property string $genres
 * @property \DateTime $releaseDate
 */
class Movie implements \JsonSerializable
{
    /** @var string */
    private $name;
    /** @var string */
    private $imagePath;
    /** @var string[] */
    private $genres;
    /** @var \DateTime */
    private $releaseDate;

    public function __construct(string $name, ?string $imagePath, array $genres, \DateTime $releaseDate)
    {
        $this->name = $name;
        $this->imagePath = $imagePath;
        $this->genres = $genres;
        $this->releaseDate = $releaseDate;
    }

    public function __get(string $propertyName)
    {
        $methodName = 'get' . ucfirst($propertyName);
        if (method_exists($this, $methodName)) {
            return $this->$methodName();
        }

        return $this->$propertyName;
    }

    public function jsonSerialize(): array
    {
        $returnData = get_object_vars($this);
        $returnData['releaseDate'] = $this->releaseDate->format('Y-m-d');

        return $returnData;
    }
}
