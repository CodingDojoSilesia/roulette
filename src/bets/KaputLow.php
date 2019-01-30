<?php
namespace bets;

/**
 * Bet on whether the winning number will be low
 */
class KaputLow extends Low
{
    public function validate($number): bool
    {
        return (int) $number >= 0 && (int) $number <= 18;
    }
}
