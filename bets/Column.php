<?php
namespace bets;

/**
 * Bet from which of the three columns will the winning number be
 */
class Column extends ConsecutiveNumbersBet
{
    /**
     * @var int
     */
    private $startingNumber;
    
    public function __construct(int $startingNumber)
    {
        $this->startingNumber = $startingNumber;
        for ($number = $startingNumber; $number <= 36; $number += 3) {
            $this->consecutiveNumbers[] = $number;
        }
    }
    
    public function getName(): string
    {
        return 'Column';
    }

    public function getPayout(): int
    {
        return 2;
    }

    public function getResourcePath(): string
    {
        return '/bets/column/' . $this->$startingNumber;
    }
    
    public static function getAllBetsCombination()
    {
        return array_map(function ($number) {
            return new static($number);
        }, range(1, 3));
    }
}
