<?php

namespace Entities\Account\Isa;

use Decimal\Decimal;
use Entities\Account\Account;
use Entities\Account\AccountDecorator;
use Entities\Account\Isa\Interface\IsaInterface;

class Isa extends AccountDecorator implements IsaInterface
{
    public function __construct(
        protected Account $account,
        protected Decimal $annualAllowance = new Decimal(20000))
    {}

    /**
     * Deposit money into normal ISA account.
     *
     * @param Decimal $decimal
     * @return bool
     */
    public function deposit(Decimal $decimal): bool {
        //@todo general ISA account implementation of depositing money.
        return true;
    }

    /**
     * Get annual allowance.
     *
     * @return Decimal
     */
    public function getAnnualAllowance() : Decimal {
        return $this->annualAllowance;
    }

    /**
     * Get remaining annual allowance.
     *
     * @return Decimal
     */
    public function getRemainingAnnualAllowance(): Decimal
    {
        //@todo general ISA account implementation of getting the remaining annual allowance.
        return new Decimal(0);
    }
}