<?php
namespace bets;

/**
 * Bet on whether the winning number will be odd
 */
class Odd extends Bet
{
    public function getName(): string
    {
        return 'Odd';
    }

    public function getPayout(): int
    {
        return 1;
    }

    public function getResourcePath(): string
    {
        return '/bets/odd';
    }

    public function validate($number): bool
    {
        return (int) $number % 2 === 1;
    }
}
