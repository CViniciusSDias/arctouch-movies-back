<?php

namespace App\Model;

use App\Helper\PropertyAccessTrait;

/**
 * @property int $id
 * @property string $name
 * @property string $imagePath
 * @property string $genres
 * @property SpecificDate $releaseDate
 */
class Movie implements \JsonSerializable
{
    use PropertyAccessTrait;

    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var string */
    private $imagePath;
    /** @var string[] */
    private $genres;
    /** @var SpecificDate */
    private $releaseDate;

    public function __construct(int $id, string $name, ?string $imagePath, array $genres, SpecificDate $releaseDate)
    {
        $this->id = $id;
        $this->name = $name;
        $this->imagePath = $imagePath;
        $this->genres = $genres;
        $this->releaseDate = $releaseDate;
    }

    public function jsonSerialize(): array
    {
        $returnData = get_object_vars($this);
        $returnData['releaseDate'] = (string) $returnData['releaseDate'];

        return $returnData;
    }
}
