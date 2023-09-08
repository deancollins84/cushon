# Cushon
Cushon

## Scenarios

```gherkin
  Feature: Customer opening an ISA account.

  Background:
    Given The customer is an existing authenticated user; making requests via a JSON API.

  Scenario: Customer applies to open an ISA account and declares they do not have an ISA for current tax year.
    Given I am a customer
      And I am opening an ISA account
     When I request POST "/customer/{customerId}/account"
      And I set JSON payload to
      """
      {
        "type": "ISA",
        "declaration": true
      }
      """
     Then I should get a status code of 200
    
  Scenario Customer applies to open an ISA account and declares they an ISA for current tax year.
    Given I am a customer
      And I am opening an ISA account
     When I request POST "/customer/{customerId}/account"
      And I set JSON payload to
      """
      {
        "type": "ISA",
        "declaration": false
      }
      """
     Then I should get a status code of 422

  Scenario Customer applies to open an ISA account and provides no details.
    Given I am a customer 
     And I am opening a customer account
    When I request POST "/customer/{customerId}/account"
    Then I should get a status code of 422
```

```gherkin
  Feature: Customer transfers money into an ISA account.

  Background:
    Given The customer is an existing authenticated user with a valid ISA account; making requests via a JSON API.

  Scenario Customer transfers money into ISA account for a single fund.
    Given I am a customer
      And I have an ISA account
     When I request POST "/customer/{customerId}/account/{accountId}"
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
    
  Scenario Customer transfers money into ISA account for a single fund and provides no details.
    Given I am a customer
     And I have an ISA account
    When I request POST "/customer/{customerId}/account/{accountId}"
    Then I should get a status code of 422

  Scenario Customer transfers money into ISA account for a single fund with amount above allowance of £20,000.
    Given I am a customer
    And I have an ISA account
    When I request POST "/customer/{customerId}/account/{accountId}"
    And I set JSON payload to
      """
      {
        "funds": {
          "fund_id": "ac5de1df-f9bb-4929-bdae-c560c925a84f",
          "amount": 25000,
        }
      }
      """
    Then I should get a status code of 400

  Scenario Customer transfers money into ISA account split across multiple funds.
    Given I am a customer
    And I have an ISA account
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
    Then I should get a status code of 400
```