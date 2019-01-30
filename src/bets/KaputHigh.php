<?php
namespace bets;

/**
 * Bet on whether the winning number will be high
 */
class KaputHigh extends High
{
    public function validate($number): bool
    {
        return (int) $number >= 20 && (int) $number <= 36;
    }
}
