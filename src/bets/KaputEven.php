<?php
namespace bets;

/**
 * Bet on whether the winning number will be even
 */
class KaputEven extends Even
{
    public function validate($number): bool
    {
        return (int) $number % 2 === 0;
    }
}
