<?php
namespace bets;

/**
 * Bet on three consecutive numbers in a horizontal line (e.g. 7-8-9)
 */
class Street extends ConsecutiveNumbersBet
{
    /**
     * @var int
     */
    private $startingNumber;
    
    public function __construct(int $startingNumber)
    {
        $this->startingNumber = $startingNumber;
        $this->consecutiveNumbers = [$this->startingNumber, $this->startingNumber + 1, $this->startingNumber + 2];
    }
    
    public function getName(): string
    {
        return 'Street';
    }

    public function getPayout(): int
    {
        return 11;
    }

    public function getResourcePath(): string
    {
        return '/bets/street/' . implode('-', $this->consecutiveNumbers);
    }
    
    public static function getAllBetsCombination()
    {
        $bets = array_map(function ($multiplier) {
            return new static($multiplier * 3 + 1);
        }, range(0, 11));
        // special cases with zero
        $bets[12] = new static(0);
        $bets[13] = new static(1);
        $bets[13]->consecutiveNumbers[0] = 1;
        return $bets;
    }
}
