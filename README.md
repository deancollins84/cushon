# Cushon
Cushon

## Top level assumptions.
- Natwest relationship with Cushon could open up international ventures.
- A solution exists for employers and employees currently in the UK ISA space.
- <em>"Cushon would like to keep the functionality for retail ISA customers separate from it’s Employer based offering where practical."</em>; 
initial interpretation without detailed knowledge of existing domain and architectural would be:
- - business logic (and requirements) between retail and existing implementation could be different,
- - and to build solution externally; as a microservice,
- Funds assumed to be part of this new retail domain.
- Initial thoughts are in using banking terms;
- - "deposit" for invest into ISA, 
- - "in", "out" for transactions).

## Proposal
Illustrate possible solution for new functionality.

Authentication will not be covered.

Will resolved around the <strong>abstracted idea of tax free savings accounts</strong> for potential future proofing
for non UK equivalent saving accounts.

Ideally a new microservice; <strong>Tax Free Savings Account Service</strong>

## Scenarios

### Opening ISA account.

> #### Assumptions
> - Customer records exists outside the scope of this project.
> - Account type would be to introduce other future investment type accounts, 
> -  - Junior ISA within the UK,
> -  - ISA equivalent / similar tax saving structures outside the UK i.e. Roth IRA in the USA.
> - Currency if omitted from payload will be assumed to be GBP.

```gherkin
  Feature: Customer opening an ISA account.

  Background:
    Given I am an authenticated user
      And I have an existing customer account
     When making requests via a JSON API.

  Scenario: Customer applies to open an ISA account and declares they do not have an ISA for current tax year.
    Given I am opening an ISA account
     When I request POST "/customer/{customerId}/account"
      And I set JSON payload to
      """
      {
        "type": "ISA",
        "declaration": true
      }
      """
     Then I should get a status code of 201
      And I should see a success message 
    
  Scenario Customer applies to open an ISA account and declares they already have an ISA for current tax year.
    Given I am opening an ISA account
     When I request POST "/customer/{customerId}/account"
      And I set JSON payload to
      """
      {
        "type": "ISA",
        "declaration": false
      }
      """
     Then I should get a status code of 422
      And I should see why my request failed

  Scenario Customer applies to open an ISA account and provides no details.
    Given I am opening a ISA account
     When I request POST "/customer/{customerId}/account"
     Then I should get a status code of 422
      And I should see why my request failed
```

### Transferring money into an ISA account.

> #### Assumptions
> - Mechanism to transfer money between accounts (internal?/external) exists, for example;
> - - deposit money into ISA account from external bank, 
> - - move money from my another Cushon account (internal?) to ISA account,
> - - bank transfer money into ISA account,
> - - direct debit money i.e. monthly transfer (deposit).
> - Fund spreading anticipated so will accept an array of funds for spreading deposits and limit to accepting only 1 programmatically.
> - Amount will be decimal using [PHP Decimal - Arbitrary-precision decimal arithmetic](https://php-decimal.io/). Accepting/storing as pennies equally an option.
> - Currency if omitted will be assumed to be GBP.

```gherkin
  Feature: Customer deposits money into an ISA account.

  Background:
    Given I am an authenticated user
      And I have an existing customer account
      And I have a valid ISA account
     When making requests via a JSON API.

  Scenario Customer deposits money into ISA account for a single fund.
    Given I am a depositing money into my ISA account
     When I request POST "/customer/{customerId}/account/{accountId}/deposit"
      And I set JSON payload to
      """
      {
        "funds": {
          "fund_id": "ac5de1df-f9bb-4929-bdae-c560c925a84f",
          "amount": 20000,
        }
      }
      """
      Then I should get a status code of 201
       And I should see a success message
    
  Scenario Customer attempts to deposit money into ISA account for a single fund and provides no details.
    Given I am a depositing money into my ISA account
     When I request POST "/customer/{customerId}/account/{accountId}/deposit"
     Then I should get a status code of 422
      And I should see why my request failed

  Scenario Customer attempts to deposit money into ISA account for a single fund with amount above allowance of £20,000.
    Given I am a depositing money into my ISA account
     When I request POST "/customer/{customerId}/account/{accountId}/deposit"
      And I set JSON payload to
      """
      {
        "funds": {
          "fund_id": "ac5de1df-f9bb-4929-bdae-c560c925a84f",
          "amount": 25000,
        }
      }
      """
    Then I should get a status code of 422
     And I should see why my request failed

  Scenario Customer attempts to deposit money into ISA account for a single fund however allowance is already 75% full.
    Given I am a depositing money into my ISA account
      And I have already transferred £15,000.00 before now
     When I request POST "/customer/{customerId}/account/{accountId}/transfer"
      And I set JSON payload to
      """
      {
        "funds": {
          "fund_id": "ac5de1df-f9bb-4929-bdae-c560c925a84f",
          "amount": 7000,
        }
      }
      """
    Then I should get a status code of 422
    And I should see why my request failed

  Scenario Customer attempts to transfer money into ISA account split across multiple funds.
    Given I am a transferring money into my ISA account
     When I request POST "/customer/{customerId}/account/{accountId}"
      And I set JSON payload to
      """
      {
        "funds": {
          "fund_id": "ac5de1df-f9bb-4929-bdae-c560c925a84f",
          "amount": 10000,
        },
        "funds": {
          "fund_id": "c2257c68-52bc-4cd5-807e-0f93c7fc8d07",
          "amount": 10000,
        }
      }
      """
    Then I should get a status code of 422
     And I should see why my request failed
```

### Available investment funds

> #### Assumptions
> - Mechanism to transfer money between accounts (internal?/external) exists, for example;

```gherkin
  Feature: Available investment funds.

  Background:
    Given I am an authenticated user
      And I have an existing customer account
      And I have a valid ISA account
     When making requests via a JSON API.

  Scenario: Customer who would like to transfer money into ISA account can see available funds.
    Given I am transferring money into my ISA account
      And I need to view available funds
     When I request GET "/funds"
     Then I should get a status code of 200
      And See multiple results

  Scenario: Customer who would like to transfer money into ISA account can see only the latest available fund.
    Given I am transferring money into my ISA account
    And I need to view available funds
    When I request POST "/funds?limit=1"
    Then I should get a status code of 200
     And See only one result
```