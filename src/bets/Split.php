<?php
namespace bets;

/**
 * Bet on two vertically/horizontally adjacent numbers (e.g. 14-17 or 8-9)
 */
class Split extends Bet
{
    /**
     * @var int
     */
    private $smallerNumber;
    /**
     * @var int
     */
    private $greaterNumber;
    
    public function __construct(int $smallerNumber, int $greaterNumber)
    {
        $this->smallerNumber = $smallerNumber;
        $this->greaterNumber = $greaterNumber;
    }
    
    public function getName(): string
    {
        return 'Split';
    }

    public function getPayout(): int
    {
        return 17;
    }

    public function getResourcePath(): string
    {
        return '/bets/split/' . $this->smallerNumber . '-' . $this->greaterNumber;
    }

    public function validate($number): bool
    {
        return (int) $number === $this->smallerNumber || (int) $number === $this->greaterNumber;
    }
    
    public static function getAllBetsCombination()
    {
        $bets = [];
        $firstLine = [1, 2, 3];
        for ($i = 0; $i < 36; ++$i) {
            $bets[] = new static($i, $i + 1);
            foreach ($firstLine as $number) {
                if ($i > 0 && 3 * $i + $number <= 36) {
                    $bets[] = new static(3 * ($i - 1) + $number, 3 * $i + $number);
                }
            }
        }
        $bets[] = new static(0, 2);
        $bets[] = new static(0, 3);
        return $bets;
    }
}
