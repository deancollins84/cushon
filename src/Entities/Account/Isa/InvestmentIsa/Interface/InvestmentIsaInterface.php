<?php

namespace Entities\Account\Isa\InvestmentIsa\Interface;

use Decimal\Decimal;
use Entities\Fund\Fund;

Interface InvestmentIsaInterface
{
    public function invest(Fund $fund, Decimal $deposit) : bool;
}