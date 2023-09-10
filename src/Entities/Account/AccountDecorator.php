<?php

namespace Entities\Account;

use Entities\Account\Interface\AccountInterface;

abstract class AccountDecorator implements AccountInterface
{
    public function __construct(
        protected Account $account
    ){}

}