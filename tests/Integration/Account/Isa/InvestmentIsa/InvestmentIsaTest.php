<?php declare(strict_types=1);

use Decimal\Decimal;
use Entities\Account\Account;
use Entities\Account\Isa\InvestmentIsa\InvestmentIsa;
use Entities\Account\Isa\Isa;
use Entities\Fund\Fund;
use Exceptions\Account\Isa\AnnualAllowanceException;
use Exceptions\Account\Isa\InvestmentIsa\SingleFundOnlyException;
use PHPUnit\Framework\TestCase;

class InvestmentIsaTest extends TestCase
{
    protected InvestmentIsa $investmentIsa;
    public function setUp(): void {
        // Arrange.
        $this->investmentIsa = new InvestmentIsa(
            new Isa(
                new Account, new Decimal(20000)
            ),
        );
    }

    /**
     * Test is an investment ISA (has an ISA fund wrapper).
     *
     * @return void
     */
    public function test_is_an_investment_isa() : void{
        // Assert.
        $this->assertInstanceOf(InvestmentIsa::class, $this->investmentIsa);
    }

    /**
     * Test investment ISA has an annual allowance.
     *
     * @return void
     */
    public function test_annual_allowance() : void {
        // Act / Assert.
        $this->assertInstanceOf(Decimal::class, $this->investmentIsa->getAnnualAllowance());
        $this->assertTrue(
            (new Decimal(20000))->equals($this->investmentIsa->getAnnualAllowance()));
    }

    /**
     * Test invest into a single fund.
     *
     * @return void
     */
    public function test_invest_to_a_single_fund() : void {
        // Act
        $invested = $this->investmentIsa->invest(
            new Fund('Cushon Equities Fund'),
            new Decimal(10001)
        );

        // Assert.
        $this->assertTrue($invested);
    }

    /**
     * Test invest into multiple funds.
     *
     * @return void
     */
    public function test_invest_into_multiple_funds(): void {
        // Pre assert.
        $this->expectException(SingleFundOnlyException::class);

        // Act.
        $this->investmentIsa->invest(
            new Fund('Cushon Equities Fund'),
            new Decimal(5000)
        );

        $this->investmentIsa->invest(
            new Fund('Random Fund'),
            new Decimal(5000)
        );
    }

    /**
     * Test invest into single fund and check remaining allowance.
     *
     * @return void
     */
    public function test_invest_into_single_find_and_check_remaining_allowance() : void {
        // Act.
        $this->investmentIsa->invest(
            new Fund('Cushon Equities Fund'),
            new Decimal(5000)
        );

        // Assert.
       $this->assertTrue(
           (new Decimal(15000))->equals($this->investmentIsa->getRemainingAnnualAllowance())
       );
    }

    /**
     * Test invest amount larger than annual allowance.
     *
     * @return void
     */
    public function test_invest_amount_larger_than_annual_allowance() : void {
        // Pre assert.
        $this->expectException(AnnualAllowanceException::class);

        // Act.
        $this->investmentIsa->invest(
            new Fund('Cushon Equities Fund'),
            new Decimal(25000)
        );
    }

    /**
     * Test invest amount at a later date that take will take you over remaining annual allowance.
     *
     * @todo Scope to continue, updating investments with more amounts (monthly investments into a fund i.e. direct debit') and going over remaining allowance.
     *
     * @return void
     */
    public function test_invest_amount_larger_than_remaining_annual_allowance_when_existing_amounts_exist() : void {
        $this->markTestSkipped('Scope to continue, updating investments with more amounts (monthly investments into a fund i.e. direct debit)');

        // Pre assert.
        $this->expectException(AnnualAllowanceException::class);

        // Arrange.
        $cushonEquitiesFund = new Fund('Cushon Equities Fund');

        // Act.
        $this->investmentIsa->invest( // Previous deposit.
            $cushonEquitiesFund,
            new Decimal(19000)
        );

        $this->investmentIsa->invest(
            $cushonEquitiesFund,
            new Decimal(6000)
        );
    }

}