<?php
namespace bets;

/**
 * Bet on whether the winning number will be even
 */
class Even extends Bet
{
    public function getName(): string
    {
        return 'Even';
    }

    public function getPayout(): int
    {
        return 1;
    }

    public function getResourcePath(): string
    {
        return '/bets/even';
    }

    public function validate($number): bool
    {
        return (int) $number !== 0 && (int) $number % 2 === 0;
    }
}
