<?php
namespace bets;

abstract class Bet
{
    /**
     * Returns a full path of a resource (means bets)
     */
    abstract public function getResourcePath(): string;
    
    /**
     * Gets a name of the bet.
     */
    abstract public function getName(): string;
    
    /**
     * Gets a description of the bet.
     */
    public function getDescription(): string
    {
        return '';
    }
    
    /**
     * Returns the payout, which is a multiplier of the nof chips. Remember that both chips and payout are passed to 
     * a player.
     */
    abstract public function getPayout(): int;
    
    /**
     * Validates the number due to the bet rules.
     */
    abstract public function validate($number): bool;
}