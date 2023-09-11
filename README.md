# Cushon

- [Brief](#brief)
- [Top level assumptions](#top-level-assumptions)
- [Proposal](#proposal)
- [Scenarios](#scenarios)
  - [Opening an ISA account](#opening-an-isa-account)
  - [Investing (transferring/depositing) money into an Investment ISA account](#investing-transferringdepositing-money-into-an-investment-isa-account)
  - [Investment ISA account investment history](#investment-isa-account-investment-history)
  - [Account transactions](#account-transactions)
  - [Available investment funds](#available-investment-funds)
- [OpenAPI 3 / Swagger documentation](#openapi-3--swagger-documentation)
- [ERD / Lucidchart documentation](#erd--lucidchart-documentation)
- [Docker containers and PHPUnit tests](#docker-containers-and-phpunit-tests)
- [TDD; Early steps](#tdd-early-steps)

## Brief
Cushon already offers ISAs and Pensions to Employees of Companies (Employers) who have an existing arrangement with
Cushon. Cushon would like to be able to offer ISA investments to retail (direct) customers who are not associated with an
employer. Cushon would like to keep the functionality for retail ISA customers separate from it’s Employer based offering
where practical.

When customers invest into a Cushon ISA they should be able to select a single fund from a list of available options. Currently
they will be restricted to selecting a single fund however in the future we would anticipate allowing selection of multiple
options.

Once the customer’s selection has been made, they should also be able to provide details of the amount they would like to
invest.

Given the customer has both made their selection and provided the amount the system should record these values and allow
these details to be queried at a later date.
As a specific use case please consider a customer who wishes to deposit £25,000 into a Cushon ISA all into the Cushon
Equities Fund.

## Top level assumptions
- Natwest relationship with Cushon <em>could</em> open up international ventures.
- <em>Cushon already offers ISAs and Pensions to Employees of Companies (Employers) who have an existing arrangement with Cushon</em>;
initial interpretation without detailed knowledge of existing functionality:
- - a solution exists for employers and employees currently in the UK ISA space.
- <em>"Cushon would like to keep the functionality for retail ISA customers separate from it’s Employer based offering where practical"</em>; 
initial interpretation without detailed knowledge of existing domain and architectural would be:
- - business logic (and requirements) between retail and existing implementation could be different,
- - and to build solution externally; possibly a microservice,
- Investments funds assumed to be part of this new retail domain.
- Deposits vs investing; and the mechanisms of them likely to exist (depositing money):
- - Line of thinking would be you deposit money into a standard ISA,
- - Whereas an investment ISA you could deposit then allocate / invest into a fund.
- - Will go with investing; and that will cover depositing money for this project.
- Initial thoughts are in using banking terms (ubiquitous language would be agreed with product team):
- - "deposit" for putting money into ISA account,
- - "in", "out" for transactions,
- - "invest" for investing into a fund.

## Proposal
Illustrate possible solution for new functionality.

Authentication will not be covered.

Will revolve around the <strong>abstracted idea of tax-free savings accounts</strong> for potential future proofing
for non UK equivalent saving accounts (Natwest <em>could</em> introduce).

Ideally a new microservice; <strong>Tax Free Savings Account Service</strong>.
Considered domain to possibly be <strong>Investment Account Service</strong> in contrast to tax-free savings as abstract concept.

Framework-agnostic at this stage.

Decorator pattern initially comes to mind to keep adding functionality as required i.e.
1. A general account can take deposits,
2. A standard ISA could take deposits and also have an annual allowance,
3. An Investment ISA would do the same as a standard ISA as well as allocate / invest funds (or deposits?) to many investment funds.
4. A variation on an investment ISA for example, Limited Investment ISA for example; limiting the investment into one investment fund.


## Scenarios

### Opening an ISA account

> #### Assumptions
> - Customer records exists outside the scope of this project.
> - Account type would be to introduce other future tax-free savings accounts, 
> -  - Junior ISA, Investment/Stocks and Shares ISA(Cushon fund equivalent?), Lifetime ISAs, etc within the UK,
> -  - ISA non UK equivalent / similar tax saving structures outside the UK i.e. Roth IRA in the USA.
> - Currency if omitted from payload will be assumed to be GBP.

```gherkin
  Feature: Customer opening an ISA account.

  Background:
    Given I am an authenticated user
      And I have an existing customer account
     When making requests via a JSON API.

  Scenario: Customer applies to open an Investment ISA account and declares they do not have an ISA of the same type for current tax year.
    Given I am opening an ISA account
     When I request POST "/customer/{customerId}/account"
      And I set JSON payload to
      """
      {
        "type": "INVESTMENT-ISA",
        "declaration": true
      }
      """
     Then I should get a status code of 201
      And I should see a success message 
    
  Scenario Customer applies to open an Investment ISA account and declares they already have an ISA of the same type for current tax year.
    Given I am opening an ISA account
     When I request POST "/customer/{customerId}/account"
      And I set JSON payload to
      """
      {
        "type": "INVESTMENT-ISA",
        "declaration": false
      }
      """
     Then I should get a status code of 422
      And I should see why my request failed

  Scenario Customer applies to open an Investment ISA account and provides no details.
    Given I am opening a ISA account
     When I request POST "/customer/{customerId}/account"
     Then I should get a status code of 422
      And I should see why my request failed
```

### Investing (transferring/depositing) money into an Investment ISA account

> #### Assumptions
> - Mechanism to transfer money between accounts (internal?/external) exists, for example;
> - - deposit money into an ISA account from external bank, 
> - - move money from my another Cushon account (internal?) to ISA account,
> - - bank transfer money into ISA account,
> - - direct debit money i.e. monthly transfer (deposit).
> - Fund spreading anticipated so will accept an array of funds for spreading amounts across fund and limit to accepting only 1 programmatically.
> - Amount will be decimal using [PHP Decimal - Arbitrary-precision decimal arithmetic](https://php-decimal.io/). Accepting/storing as pennies equally an option.
> - Currency if omitted will be assumed to be GBP.

```gherkin
  Feature: Customer invest money into an Investment ISA account.

  Background:
    Given I am an authenticated user
      And I have an existing customer account
      And I have a valid Investment ISA account
     When making requests via a JSON API.

  Scenario Customer invests money into an Investment ISA account for a single fund.
    Given I am a investing money into my Investment ISA account
     When I request POST "/customer/{customerId}/account/{accountId}/investment"
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
    
  Scenario Customer attempts to invest money into an Investment ISA account for a single fund and provides no details.
    Given I am a investing money into my Investment ISA account
     When I request POST "/customer/{customerId}/account/{accountId}/investment"
     Then I should get a status code of 422
      And I should see why my request failed

  Scenario Customer attempts to invest money into an Investment ISA account for a single fund with amount above allowance of £20,000.
    Given I am a investing money into my Investment ISA account
     When I request POST "/customer/{customerId}/account/{accountId}/investment"
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

  Scenario Customer attempts to invest money into an Investment ISA account for a single fund however allowance is already 75% full.
    Given I am a investing money into my Investment ISA account
      And I have already invested £15,000.00 before now
     When I request POST "/customer/{customerId}/account/{accountId}/investment"
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

  Scenario Customer attempts to invest money into an Investment ISA account split across multiple funds.
    Given I am a investing money into my ISA account
     When I request POST "/customer/{customerId}/account/{accountId}/investment"
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
### Investment ISA account investment history

```gherkin
  Feature: Customer has invested money into a fund over a period of time.

  Background:
    Given I am an authenticated user
      And I have an existing customer account
      And I have a valid Investment ISA account
      And I have made investments into a fund
     When making requests via a JSON API.

  Scenario: Customer who would like to see all the investments (made from Investment ISA account) they have made into multiple funds.
    Given I want to see my investment summary
     When I request GET "/customer/{customerId}/account/{accountId}/investment"
     Then I should get a status code of 200
     And See multiple results, paginated
     And Grouped by funds and summed total amount invested

  Scenario: Customer who would like to see an investment (made from Investment ISA account) to a fund and the breakdown of amounts (i.e. monthly deposits).
    Given I want to see a specific transaction
     When I request GET "/customer/{customerId}/account/{accountId}/investment/{investmentId}"
     Then I should get a status code of 200
      And See only one result, but still paginated
```

### Account transactions

> [!NOTE]
> Touching on transactions briefly, however would need a more in depth exploration.
> Initially thinking here, if money comes into the system we just need to keep track of it.

```gherkin
  Feature: Customer would like to see their transaction history on their account.

  Background:
    Given I am an authenticated user
      And I have an existing customer account
      And I have a valid Investment ISA account
      And I have made deposits (and or investments)
     When making requests via a JSON API.

  Scenario: Customer who would like to see all the transactions on their account.
    Given I want to see my latest transactions
     When I request GET "/customer/{customerId}/account/{accountId}/transaction"
     Then I should get a status code of 200
      And See multiple results, paginated

  Scenario: Customer who would like see the latest transaction on their account.
    Given I want to see a specific transaction
     When I request GET "/customer/{customerId}/account/{accountId}/transaction?limit=1"
     Then I should get a status code of 200
      And See only one result, but still paginated
```

### Available investment funds

> #### Assumptions
> - Retail customers have a different set of available funds then can invest in.
> - Investment fund and how they work are beyound the scope of the project i.e for Stock and Share ISA's a total of share, share prices, ledger, etc. need attention.

```gherkin
  Feature: Available investment funds.

  Background:
    Given I am an authenticated user
      And I have an existing customer account
      And I have a valid Investment ISA account
     When making requests via a JSON API.

  Scenario: Customer who would like to invest money into an Investment ISA account can see available funds.
    Given I am investing money into my Investment ISA account
      And I need to view available funds
     When I request GET "/funds"
     Then I should get a status code of 200
      And See multiple results, paginated

  Scenario: Customer who would like to invest money into an Investment ISA account can see only the latest available fund.
    Given I am deposit money into my ISA account
      And I need to view available funds
     When I request POST "/funds?limit=1"
     Then I should get a status code of 200
      And See only one result, but still paginated
```

## OpenAPI 3 / Swagger documentation
API design for the above scenarios:
[SwaggerHub - Retail tax-free saving account service](https://app.swaggerhub.com/apis/dean.collins/cushon-tax-free-savings/1.0.0)

## ERD / Lucidchart documentation
Database design for the above scenarios:
[Lucidchart - Retail tax-free saving account service](https://lucid.app/documents/view/11a53f1d-cf00-450f-b529-357c7dede8a3)

> [!NOTE]
> Design choice of having both an integer ID (technical key; PK) and a string UUID (unique) in the database table is just my opinion. 
> I recognise this is effectively redundant, just personal preference to not expose the technical key and have a clear separation. Uuid would be indexed.

## Docker containers and PHPUnit tests
Two simple docker containers included, one for PHP CLI and one for Composer.

Build and start the containers;
```
docker compose up -d --build
```
Install composer dependencies with ignore platform reqs due to required decimal package needing it;
```
docker compose run composer install --ignore-platform-reqs
```
Run PHPUnit tests;
```
docker compose run php vendor/bin/phpunit tests
```

## TDD; Early steps

Focused on the core entities identified in the README.MD:
- Investment ISA (wrapped functionality of a more simple entity that could exist (General account, standard ISA, etc)),
- Collection of invested Funds (restricted to a single fund at this time),
- And the invested Fund with seperated amounts over time (i.e. add money monthly for example).

More iterative rounds of refactoring required while identify more test cases.
