<?php

namespace Entities\Account\Interface;

use Decimal\Decimal;

Interface AccountInterface
{
    /**
     * Deposit money into account.
     *
     * @param Decimal $decimal
     * @return bool
     */
    public function deposit(Decimal $decimal): bool;
}