Feature: User feature
    @loginAsUser @logout
    Scenario: as a logged user I want to get my user details
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/users/1"
        Then the response status code should be 200
        And the response should be in JSON
        And the JSON node "id" should be equal to 1
        And the JSON node "email" should be equal to "user1@mail.com"
        And the JSON node "firstname" should not be null
        And the JSON node "lastname" should not be null
        And the JSON node "posts" should exist

    @loginAsUser @logout
    Scenario: as a logged user I want to get other user details
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/users/2"
        Then the response status code should be 200
        And the response should be in JSON
        And the JSON node "id" should be equal to 2
        And the JSON node "email" should be equal to "user2@mail.com"
        And the JSON node "firstname" should not be null
        And the JSON node "lastname" should not be null
        And the JSON node "posts" should exist

    @loginAsUser @logout
    Scenario: as a logged user I want to get details of a user with a non-existent id
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/users/xxx"
        Then the response status code should be 404
        And the response should be in JSON

    @loginAsUser @logout
    Scenario: as a logged user I want to get all users
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/users"
        Then the response status code should be 403
        And the response should be in JSON

    @loginAsAdmin @logout
    Scenario: as a logged user I want to get all users
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/users"
        Then the response status code should be 200
        And the response should be in JSON