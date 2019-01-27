<?php
namespace bets;

/**
 * Bet on whether the winning number will be high
 */
class High extends Bet
{
    public function getName(): string
    {
        return 'High';
    }
    
    public function getDescription(): string
    {
        return 'Od 19 do 36.';
    }

    public function getPayout(): int
    {
        return 1;
    }

    public function getResourcePath(): string
    {
        return '/bets/high';
    }

    public function validate($number): bool
    {
        return (int) $number >= 19 && (int) $number <= 36;
    }
}
