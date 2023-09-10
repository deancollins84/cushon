<?php

namespace Entities\Account\Isa\InvestmentIsa;

use Decimal\Decimal;
use Entities\Account\Isa\InvestmentIsa\Interface\InvestmentIsaInterface;
use Entities\Account\Isa\Isa;
use Entities\Account\Isa\IsaDecorator;
use Entities\Fund\Fund;
use Entities\Investment\Investment;
use Entities\Investment\Investments;
use Exceptions\Account\Isa\AnnualAllowanceException;
use Exceptions\Account\Isa\InvestmentIsa\SingleFundOnlyException;

class InvestmentIsa extends IsaDecorator implements InvestmentIsaInterface
{
    public function __construct(
        Isa $isa,
        protected Investments $investments = new Investments
    )
    {
        parent::__construct($isa);
    }

    /**
     * Deposit money into investment ISA account.
     *
     * @param Decimal $decimal
     * @return bool
     */
    public function deposit(Decimal $decimal) : bool {
        // @todo Needs thought, does a customer have to deposit money (and have it cleared) in ISA before investing / allocating to funds.
        return true;
    }

    /**
     * Isa annual allowance.
     *
     * @return Decimal
     */
    public function getAnnualAllowance(): Decimal
    {
        return $this->isa->getAnnualAllowance();
    }

    /**
     * Remaining ISA annual allowance.
     *
     * @return Decimal
     */
    public function getRemainingAnnualAllowance(): Decimal
    {
        $invested = $this->investments
            ? $this->investments->getTotal()
            : new Decimal(0);

        return $this->getAnnualAllowance()->sub($invested);
    }

    /**
     * Invest into a fund.
     *
     * @todo This will be refactored in more depth; and more likely that not introduce another decorator for single fund investment ISA's only.
     * @todo Required to update an funds investments amounts i.e. add monthly deposits etc ...
     *
     * @param Fund $fund
     * @param Decimal $amount
     * @return bool
     * @throws AnnualAllowanceException
     * @throws SingleFundOnlyException
     */
    public function invest(Fund $fund, Decimal $amount): bool
    {
        if ($amount > $this->getRemainingAnnualAllowance()) {
            throw new AnnualAllowanceException;
        }

        if (count($this->investments->get()) === 1) {
            throw new SingleFundOnlyException;
        }

        if (!$this->investments->hasFund($fund)) {
            $this->investments->add(
                (new Investment($fund))->add($amount)
            );
            return true;
        }
        return false;
    }
}