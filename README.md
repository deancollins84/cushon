# Cushon
Cushon

## Scenarios

```gherkin
  Feature: Customer opening an ISA account.

  Background:
    Given The customer is an existing authenticated user.

  Scenario: Customer applies to open an ISA account and declares they do not have an ISA for current tax year.
    Given I am an existing authenticated user
      And I am opening a customer account
     When I request POST "/customer/{id}/account"
      And I set "type" to "ISA"
      And I set "declaration" to false
     Then I should get a status code of 200
    
  Scenario Customer applies to open an ISA account and declares they an ISA for current tax year.
    Given I am an existing authenticated user
      And I am opening a customer account
     When I request POST "/customer/{id}/account"
      And I set type to "ISA"
      And I set "declaration" to false
     Then I should get a status code of 422

  Scenario Customer applies to open an ISA account and provides not required details.
    Given I am an existing authenticated user
     And I am opening a customer account
    When I request POST "/customer/{id}/account"
    Then I should get a status code of 422
```