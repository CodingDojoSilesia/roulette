<?php
namespace bets;

/**
 * Bet on four numbers that meet at one corner (e.g. 10-11-13-14)
 */
class Corner extends ConsecutiveNumbersBet
{
    /**
     * @var int
     */
    private $startingNumber;
    
    public function __construct(int $startingNumber)
    {
        $this->startingNumber = $startingNumber;
        $this->consecutiveNumbers = [
            $this->startingNumber, $this->startingNumber + 1, $this->startingNumber + 3, $this->startingNumber + 4
        ];
    }
    
    public function getName(): string
    {
        return 'Corner';
    }

    public function getPayout(): int
    {
        return 8;
    }

    public function getResourcePath(): string
    {
        return '/bets/corner/' . implode('-', $this->consecutiveNumbers);
    }
    
    public static function getAllBetsCombination()
    {
        $bets = [];
        for ($i = 0; $i < 10; ++$i) {
            $bets[] = new static($i * 3 + 1);
            $bets[] = new static($i * 3 + 2);
        }
        // the special case with zero
        $bets[22] = new static(0);
        $bets[22]->consecutiveNumbers = [0, 1, 2, 3];
        return $bets;
    }
}
