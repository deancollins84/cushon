<?php

namespace Entities\Account\Isa;

use Entities\Account\Interface\AccountInterface;
use Entities\Account\Isa\Interface\IsaInterface;

abstract class IsaDecorator implements IsaInterface, AccountInterface
{
    public function __construct(
        protected Isa $isa
    ){}

}