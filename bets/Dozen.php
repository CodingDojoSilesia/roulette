<?php
namespace bets;

/**
 * Bet on one of the three dozen that are found on the layout of the table
 */
class Dozen extends ConsecutiveNumbersBet
{
    /**
     * @var int
     */
    private $startingNumber;
    
    public function __construct(int $startingNumber)
    {
        $this->startingNumber = $startingNumber;
        for ($i = ($startingNumber - 1); $i < ($startingNumber - 1) + 12; ++$i) {
            $this->consecutiveNumbers[] = $i + 1;
        }
    }
    
    public function getName(): string
    {
        return 'Dozen';
    }

    public function getPayout(): int
    {
        return 2;
    }

    public function getResourcePath(): string
    {
        return '/bets/dozen/' . $this->$startingNumber;
    }
    
    public static function getAllBetsCombination()
    {
        return array_map(function ($number) {
            return new static($number);
        }, range(1, 3));
    }
}
