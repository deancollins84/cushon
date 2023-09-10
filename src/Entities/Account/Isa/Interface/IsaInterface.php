<?php

namespace Entities\Account\Isa\Interface;

use Decimal\Decimal;

Interface IsaInterface
{
    /**
     * Get annual allowance.
     *
     * @return Decimal
     */
    public function getAnnualAllowance() : Decimal;

    /**
     * Get remaining annual allowance.
     *
     * @return Decimal
     */
    public function getRemainingAnnualAllowance() : Decimal;
}