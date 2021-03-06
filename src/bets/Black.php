<?php
namespace bets;

/**
 * Bet on the colour of the winning number
 */
class Black extends Bet
{
    protected $numbers = [2,4,6,8,10,11,13,15,17,19,20,22,24,26,29,31,33,35];
    
    public function getName(): string
    {
        return 'Black';
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
        return '/bets/black';
    }

    public function validate($number): bool
    {
        return in_array((int) $number, $this->numbers, true);
    }
}
