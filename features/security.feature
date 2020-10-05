Feature: Security feature
    Scenario: register a new user
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "POST" request to "/api/register" with body:
        """
        {
          "email": "test@mail.com",
          "firstname": "Test",
          "lastname": "Test",
          "password": "password"
        }
        """
        Then the response status code should be 201
        And the response should be in JSON
        And the header "Content-Location" should match "~/api/users/(\d+)~"
        And the JSON nodes should contain:
            | email                   | test@mail.com     |
            | firstname               | Test              |
            | lastname                | Test              |

    Scenario: try to register a user with an email that is already used
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "POST" request to "/api/register" with body:
        """
        {
          "email": "test@mail.com",
          "firstname": "Test",
          "lastname": "Test",
          "password": "password"
        }
        """
        Then the response status code should be 400
        And the JSON node "violations[0].propertyPath" should be equal to "email"


    Scenario: try to register a user with a wrong email address
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "POST" request to "/api/register" with body:
        """
        {
          "email": "test",
          "firstname": "Test",
          "lastname": "Test",
          "password": "password"
        }
        """
        Then the response status code should be 400
        And the JSON node "violations[0].propertyPath" should be equal to "email"

    Scenario: try to register a user with a password less than 8 characters
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "POST" request to "/api/register" with body:
        """
        {
          "email": "anothertest@mail.com",
          "firstname": "Test",
          "lastname": "Test",
          "password": "pwd"
        }
        """
        Then the response status code should be 400
        And the JSON node "violations[0].propertyPath" should be equal to "password"

    Scenario: try to register a user with a password more than 16 characters
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "POST" request to "/api/register" with body:
        """
        {
          "email": "anothertest@mail.com",
          "firstname": "Test",
          "lastname": "Test",
          "password": "123456789123456789"
        }
        """
        Then the response status code should be 400
        And the JSON node "violations[0].propertyPath" should be equal to "password"

    Scenario: try to register with missing parameters
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "POST" request to "/api/register" with body:
        """
        {
          "email": "",
          "firstname": "",
          "lastname": "",
          "password": ""
        }
        """
        Then the response status code should be 400
        And the JSON node "violations[0].propertyPath" should be equal to "email"
        And the JSON node "violations[1].propertyPath" should be equal to "firstname"
        And the JSON node "violations[2].propertyPath" should be equal to "lastname"
        And the JSON node "violations[3].propertyPath" should be equal to "password"

    Scenario: as a registered user I want to get my authentication token
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "POST" request to "/api/authentication_token" with body:
        """
        {
          "email": "user1@mail.com",
          "password": "12345678"
        }
        """
        Then the response status code should be 200
        And the response should be in JSON
        And the JSON node "user" should not be null
        And the JSON node "user" should match "~/api/users/(\d+)~"
        And the JSON node "token" should not be null
        And the JSON node "refresh_token" should not be null

    Scenario: try to login with a not registered user
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "POST" request to "/api/authentication_token" with body:
        """
        {
          "email": "undefinedemail@mail.com",
          "password": "12345678"
        }
        """
        Then the response status code should be 401

    Scenario: try to login with a wrong password
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "POST" request to "/api/authentication_token" with body:
        """
        {
          "email": "user1@mail.com",
          "password": "wrongpassword"
        }
        """
        Then the response status code should be 401

    Scenario: try to login with missing parameters
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "POST" request to "/api/authentication_token" with body:
        """
        {
          "email": "",
          "password": ""
        }
        """
        Then the response status code should be 401

    Scenario: as a registered user I want to refresh my authentication token
        Given am user with id 1
        And My refresh token is "this-is-not-a-secure-refresh-token"
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "POST" request to "/api/refresh_authentication_token" with body:
        """
        {
          "refresh_token": "this-is-not-a-secure-refresh-token"
        }
        """
        Then the response status code should be 200
        And the response should be in JSON
        And the JSON node "token" should not be null
        And the JSON node "refresh_token" should not be null

    Scenario: try to get a new token with a wrong refresh_token
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "POST" request to "/api/refresh_authentication_token" with body:
        """
        {
          "refresh_token": "this-is-a-wrong-refresh-token"
        }
        """
        Then the response status code should be 401
        And the response should be in JSON

    Scenario: try to get a new token with missing refresh_token parameter
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "POST" request to "/api/refresh_authentication_token" with body:
        """
        {
          "refresh_token": ""
        }
        """
        Then the response status code should be 401
        And the response should be in JSON