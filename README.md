# Cushon
Cushon

## Scenarios

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

```gherkin
  Feature: Customer transfers money into an ISA account.

  Background:
    Given I am an authenticated user
      And I have an existing customer account
      And I have a valid ISA account
     When making requests via a JSON API.

  Scenario Customer transfers money into ISA account for a single fund.
    Given I am a transferring money into my ISA account
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
       And I should see a success message
    
  Scenario Customer attempts to transfer money into ISA account for a single fund and provides no details.
    Given I am a transferring money into my ISA account
     When I request POST "/customer/{customerId}/account/{accountId}"
     Then I should get a status code of 422
      And I should see why my request failed

  Scenario Customer attempts to transfer money into ISA account for a single fund with amount above allowance of £20,000.
    Given I am a transferring money into my ISA account
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