<?php
namespace bets;

/**
 * Bet on two vertically/horizontally adjacent numbers (e.g. 14-17 or 8-9)
 */
class KaputSplit extends Split
{
    public function getPayout(): int
    {
        return 11;
    }
}
