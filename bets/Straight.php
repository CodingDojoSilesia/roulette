<?php
namespace bets;

/**
 * Bet on a single number
 */
class Straight extends Bet
{
    /**
     * @var int
     */
    private $number;
    
    public function __construct(int $number)
    {
        $this->number = $number;
    }
    
    public function getName(): string
    {
        return 'Straight';
    }

    public function getPayout(): int
    {
        return 35;
    }

    public function getResourcePath(): string
    {
        return '/bets/straight/' . $this->number;
    }

    public function validate($number): bool
    {
        return (int) $number === $this->number;
    }
    
    public static function getAllBetsCombination()
    {
        return array_map(function ($number) {
            return new static($number);
        }, range(0, 36));
    }
}
