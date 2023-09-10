<?php

namespace Entities\Investment;

use Decimal\Decimal;
use Entities\Fund\Fund;

/**
 * Fund invested in.
 *
 * @todo Continually refactor with tests as unfinished in functionality.
 */
class Investment
{
    /**
     * @var array
     */
    protected array $total = [];

    /**
     * @param Fund $fund
     */
    public function __construct(
        protected Fund $fund
    ){}

    /**
     * Get fund invested in.
     *
     * @return Fund
     */
    public function getFund(): Fund {
        return $this->fund;
    }

    /**
     * Add amount to invest.
     *
     * @param Decimal ...$amount
     * @return $this
     */
    public function add(Decimal ... $amount) : self {
        $this->total = [
            ...$this->total,
            ...$amount
        ];

        return $this;
    }

    /**
     * Get total of invested in fund.
     *
     * @return Decimal
     */
    public function getTotal(): Decimal{
        $total = new Decimal(0);

        foreach($this->total as $amount){
            $total = $total->add($amount);
        }

        return $total;
    }

}