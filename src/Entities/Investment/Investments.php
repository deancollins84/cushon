<?php

namespace Entities\Investment;

use Decimal\Decimal;
use Entities\Fund\Fund;

/**
 * Collection of investments.
 *
 * @todo This would likely implement iterator interface.
 * @todo Continually refactor with tests as unfinished in functionality.
 */
class Investments
{
    /**
     * @var array
     */
    protected array $investments = [];

    /**
     * Has fund.
     *
     * @param Fund $fund
     * @return bool
     */
    public function hasFund(Fund $fund) : bool {
        foreach ($this->investments as $investment) {
            if ($investment->getFund() === $fund) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get investments.
     *
     * @return array
     */
    public function get(): array {
        return $this->investments;
    }

    /**
     * Add investment.
     *
     */
    public function add(Investment ...$investment) : self {
        $this->investments = [
            ...$this->investments,
            ...$investment
        ];

        return $this;
    }

    /**
     * Get total of invested in all funds.
     *
     * @return Decimal
     */
    public function getTotal(): Decimal{
        $total = new Decimal(0);

        foreach($this->investments as $investment){
            $total = $total->add(
                $investment->getTotal()
            );
        }

        return $total;
    }

}