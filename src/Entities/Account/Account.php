<?php

namespace Entities\Account;

use Decimal\Decimal;
use Entities\Account\Interface\AccountInterface;

class Account implements AccountInterface
{
    /**
     * Deposit money into account.
     *
     * @param Decimal $decimal
     * @return bool
     */
    public function deposit(Decimal $decimal) : bool {
        //@todo general account implementation of depositing money.
        return true;
    }
}