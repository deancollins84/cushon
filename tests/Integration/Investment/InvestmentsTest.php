<?php declare(strict_types=1);

use Decimal\Decimal;
use Entities\Fund\Fund;
use Entities\Investment\Investment;
use Entities\Investment\Investments;
use PHPUnit\Framework\TestCase;

/**
 * @group investments
 */
class InvestmentsTest extends TestCase
{
    /**
     * Test adding fund invested in with amount.
     *
     * @return void
     */
    public function test_add_fund_invested_in() : void{
        // Arrange.
        $cushonInvestment = new Investment(new Fund('Cushon Equities Fund'));
        $cushonInvestment->add($amount = new Decimal(10000));

        // Act.
        $investedFunds = (new Investments)->add($cushonInvestment);

        // Assert.
        $this->assertTrue(
            $amount->equals($investedFunds->getTotal())
        );
    }

    /**
     * Test adding funds invested in with amounts.
     *
     * @return void
     */
    public function test_add_funds_invested_in() : void{
        // Arrange.
        $cushonInvestment = new Investment(new Fund('Cushon Equities Fund'));
        $cushonInvestment->add(new Decimal(20000));

        $randomInvestment = new Investment(new Fund('Random Fund'));
        $randomInvestment->add(
            new Decimal(1000),
            new Decimal(1000)
        );

        // Act.
        $investedFunds = (new Investments)->add(
            $cushonInvestment,
            $randomInvestment
        );

        // Assert.
        $this->assertTrue(
            (new Decimal(22000))->equals($investedFunds->getTotal()));
    }
}