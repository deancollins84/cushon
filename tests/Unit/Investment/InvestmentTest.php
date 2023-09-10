<?php declare(strict_types=1);

use Decimal\Decimal;
use Entities\Fund\Fund;
use Entities\Investment\Investment;
use PHPUnit\Framework\TestCase;

/**
 * @group investments
 */
class InvestmentTest extends TestCase
{
    /**
     * Test add amount to invest.
     *
     * @return void
     */
    public function test_add_amount_to_invest() : void{
        // Arrange.
        $investment = new Investment(
            new Fund('Cushon Equities Fund')
        );

        // Act.
        $investment->add(
            $amount = new Decimal(10000)
        );

        // Assert.
        $this->assertTrue(
            $amount->equals($investment->getTotal())
        );
    }

    /**
     * Test adding multiple amounts to invest.
     *
     * @return void
     */
    public function test_add_multiple_amounts_to_invest() : void{
        // Arrange.
        $investment = new Investment(
            new Fund('Cushon Equities Fund')
        );

        // Act.
        $investment->add(
            new Decimal(4000),
            new Decimal(1000),
            new Decimal(1000)
        );
        // Assert.

        $this->assertTrue(
            (new Decimal(6000))->equals($investment->getTotal())
        );
    }

    /**
     * Test adding amount to invest at different times.
     *
     * @return void
     */
    public function test_add_multiple_amounts_to_invest_at_different_times() : void{
        // Arrange.
        $investment = new Investment(
            new Fund('Cushon Equities Fund')
        );

        // Act.
        $investment->add(
            new Decimal(5000)
        );

        $investment->add( // Return at later date and deposit more money.
            new Decimal(1000),
            new Decimal(1000),
            new Decimal(1000)
        );

        // Assert.
        $this->assertTrue(
            (new Decimal(8000))->equals($investment->getTotal())
        );
    }

}