<?php



namespace Carbon;

use DateTimeInterface;

interface CarbonConverterInterface
{
    public function convertDate(DateTimeInterface $dateTime, bool $negated = false): CarbonInterface;
}
