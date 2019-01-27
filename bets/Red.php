<?php
namespace bets;

/**
 * Bet on the colour of the winning number
 */
class Red extends Bet
{
    private $numbers = [1,3,5,7,9,12,14,16,18,21,23,25,27,28,30,32,34,36];
    
    public function getName(): string
    {
        return 'Red';
    }
    
    public function getDescription(): string
    {
        return implode('-', $this->numbers);
    }

    public function getPayout(): int
    {
        return 1;
    }

    public function getResourcePath(): string
    {
        return '/bets/red';
    }

    public function validate($number): bool
    {
        return in_array((int) $number, $this->numbers, true);
    }
}
