Feature: Media object feature
    @createDummyIDPhoto @loginAsUser @logout
    Scenario: as a logged user I want to upload my ID photo that will be used in the verification request
        And I add "Accept" header equal to "application/json"
        When I send a "POST" request to "/api/media_objects" with parameters:
            | key     | value           |
            | file    | @dummy-ID.jpg   |
        Then the response status code should be 201
        And the header "Content-Location" should match "~/api/media_objects/(\d+)~"
        And the response should be in JSON
        And the JSON node "contentUrl" should not be null

    @createDummyIDPhoto @loginAsAdmin @logout
    Scenario: as a logged admin I should not be able to upload an ID
        And I add "Accept" header equal to "application/json"
        When I send a "POST" request to "/api/media_objects" with parameters:
            | key     | value           |
            | file    | @dummy-ID.jpg   |
        Then the response status code should be 403
        And the response should be in JSON

    @loginAsUser @logout
    Scenario: as a logged user I want to upload my ID photo with missing parameters
        And I add "Accept" header equal to "application/json"
        When I send a "POST" request to "/api/media_objects" with parameters:
            | key     | value           |
            | file    |                 |
        Then the response status code should be 400
        And the response should be in JSON

    @createDummyIDPhoto @deleteDummyIDPhoto
    Scenario: as an anonymous user I want to upload my ID photo
        And I add "Accept" header equal to "application/json"
        When I send a "POST" request to "/api/media_objects" with parameters:
            | key     | value           |
            | file    | @dummy-ID.jpg   |
        Then the response status code should be 401
        And the response should be in JSON

    @loginAsUser @logout
    Scenario: as a logged user I want to get all IDs
        And I add "Accept" header equal to "application/json"
        When I send a "GET" request to "/api/media_objects"
        Then the response status code should be 403
        And the response should be in JSON

    @loginAsAdmin @logout
    Scenario: as a logged admin I want to get all IDs
        And I add "Accept" header equal to "application/json"
        When I send a "GET" request to "/api/media_objects"
        Then the response status code should be 200
        And the response should be in JSON

    Scenario: as an anonymous user I want to get all IDs
        And I add "Accept" header equal to "application/json"
        When I send a "GET" request to "/api/media_objects"
        Then the response status code should be 401
        And the response should be in JSON

    @loginAsUser @logout
    Scenario: as a logged user I want to get the ID linked to my verification request
        And I add "Accept" header equal to "application/json"
        When I send a "GET" request to "/api/media_objects/1"
        Then the response status code should be 200
        And the response should be in JSON
        And the JSON node "contentUrl" should not be null

    @loginAsUser @logout
    Scenario: as a logged user I want to get the ID linked to a verification request that belongs to another user
        And I add "Accept" header equal to "application/json"
        When I send a "GET" request to "/api/media_objects/2"
        Then the response status code should be 403
        And the response should be in JSON

    @loginAsAdmin @logout
    Scenario: as a logged admin I want to get the ID with non-existent id
        And I add "Accept" header equal to "application/json"
        When I send a "GET" request to "/api/media_objects/xxx"
        Then the response status code should be 404
        And the response should be in JSON

    Scenario: as an anonymous user I want to get the ID with id 1
        And I add "Accept" header equal to "application/json"
        When I send a "GET" request to "/api/media_objects/1"
        Then the response status code should be 401
        And the response should be in JSON