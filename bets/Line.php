<?php
namespace bets;

/**
 * Bet on six consecutive numbers that form two horizontal lines (e.g. 31-32-33-34-35-36)
 */
class Line extends ConsecutiveNumbersBet
{
    /**
     * @var int
     */
    private $startingNumber;
    
    public function __construct(int $startingNumber)
    {
        $this->startingNumber = $startingNumber;
        $this->consecutiveNumbers = [
            $this->startingNumber, $this->startingNumber + 1, $this->startingNumber + 2,
            $this->startingNumber + 3, $this->startingNumber + 4, $this->startingNumber + 5
        ];
    }
    
    public function getName(): string
    {
        return 'Corner';
    }

    public function getPayout(): int
    {
        return 5;
    }

    public function getResourcePath(): string
    {
        return '/bets/line/' . implode('-', $this->consecutiveNumbers);
    }
    
    public static function getAllBetsCombination()
    {
        return array_map(function ($multiplier) {
            return new static($multiplier * 3 + 1);
        }, range(0, 10));
    }
}
