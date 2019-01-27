<?php
namespace bets;

/**
 * Bet on whether the winning number will be low
 */
class Low extends Bet
{
    public function getName(): string
    {
        return 'Low';
    }
    
    public function getDescription(): string
    {
        return 'Od 1 do 18.';
    }

    public function getPayout(): int
    {
        return 1;
    }

    public function getResourcePath(): string
    {
        return '/bets/low';
    }

    public function validate($number): bool
    {
        return (int) $number >= 1 && (int) $number <= 18;
    }
}
