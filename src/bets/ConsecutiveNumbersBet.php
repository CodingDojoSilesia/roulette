<?php
namespace bets;

/**
 * Bet on consecutive numbers
 */
abstract class ConsecutiveNumbersBet extends Bet
{
    protected $consecutiveNumbers;

    public function validate($number): bool
    {
        return in_array((int) $number, $this->consecutiveNumbers, true);
    }
}
