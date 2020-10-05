Feature: User feature
    @loginAsUserWithNoVerificationRequest @logout
    Scenario: as a logged user I want to start my verification request process
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "POST" request to "/api/verification_requests" with body:
        """
        {
          "message": "I want to start my verification request process",
          "image": "/api/media_objects/11"
        }
        """
        Then the response status code should be 201
        And the header "Content-Location" should match "~/api/verification_requests/(\d+)~"
        And the response should be in JSON
        And the JSON node "id" should not be null
        And the JSON node "status" should be equal to "VERIFICATION_REQUESTED"
        And the JSON node "date" should not be null
        And the JSON node "message" should be equal to "I want to start my verification request process"
        And the JSON node "image" should be equal to "/api/media_objects/11"
        And the JSON node "user" should not be null

    @loginAsUser @logout
    Scenario: as a logged user I want to create another verification request
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "POST" request to "/api/verification_requests" with body:
        """
        {
          "message": "I want to create another verification request process",
          "image": "/api/media_objects/12"
        }
        """
        Then the response status code should be 403
        And the response should be in JSON

    @loginAsUserWithNoVerificationRequest @logout
    Scenario: as a logged user I want to start my verification request process with wrong media object id
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "POST" request to "/api/verification_requests" with body:
        """
        {
          "message": "I want to start my verification request process",
          "image": "wrong-media-object"
        }
        """
        Then the response status code should be 400
        And the response should be in JSON

    @loginAsUserWithNoVerificationRequest @logout
    Scenario: as a logged user I want to start my verification request process with a non-existent media object id
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "POST" request to "/api/verification_requests" with body:
        """
        {
          "message": "I want to start my verification request process",
          "image": "/api/media_objects/xxx"
        }
        """
        Then the response status code should be 400
        And the response should be in JSON

    @loginAsUser @logout
    Scenario: as a logged user I want get all verification requests
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/verification_requests"
        Then the response status code should be 403
        And the response should be in JSON

    @loginAsBlogger @logout
    Scenario: as a logged blogger I want get all verification requests
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/verification_requests"
        Then the response status code should be 403
        And the response should be in JSON

    @loginAsAdmin @logout
    Scenario: as a logged admin I want get all verification requests
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/verification_requests"
        Then the response status code should be 200
        And the response should be in JSON

    @loginAsAdmin @logout
    Scenario: as a logged admin I want get all verification requests filtered by status = VERIFICATION_REQUESTED
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/verification_requests?status=VERIFICATION_REQUESTED"
        Then the response status code should be 200
        And the response should be in JSON
        And the response should contain "VERIFICATION_REQUESTED"
        And the response should not contain "VERIFICATION_APPROVED"
        And the response should not contain "VERIFICATION_DECLINED"

    @loginAsAdmin @logout
    Scenario: as a logged admin I want get all verification requests filtered by status = VERIFICATION_APPROVED
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/verification_requests?status=VERIFICATION_APPROVED"
        Then the response status code should be 200
        And the response should be in JSON
        And the response should contain "VERIFICATION_APPROVED"
        And the response should not contain "VERIFICATION_REQUESTED"
        And the response should not contain "VERIFICATION_DECLINED"

    @loginAsAdmin @logout
    Scenario: as a logged admin I want get all verification requests filtered by status = VERIFICATION_DECLINED
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/verification_requests?status=VERIFICATION_DECLINED"
        Then the response status code should be 200
        And the response should be in JSON
        And the response should contain "VERIFICATION_DECLINED"
        And the response should not contain "VERIFICATION_REQUESTED"
        And the response should not contain "VERIFICATION_APPROVED"

    @loginAsAdmin @logout
    Scenario: as a logged admin I want get all verification requests filtered by a wrong status = WRONG_STATUS
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/verification_requests?status=WRONG_STATUS"
        Then the response status code should be 200
        And the response should be in JSON
        Then the response should be equal to:
        """
        []
        """

    @loginAsAdmin @logout
    Scenario: as a logged admin I want get the verification request only for user 1
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/verification_requests?user=1"
        Then the response status code should be 200
        And the response should be in JSON
        And the JSON node "[0].id" should be equal to 1
        And the JSON node "[0].status" should be equal to "VERIFICATION_REQUESTED"
        And the JSON node "[0].date" should not be null
        And the JSON node "[0].message" should not be null
        And the JSON node "[0].image" should be equal to "/api/media_objects/1"
        And the JSON node "[0].user" should be equal to "/api/users/1"
        And the JSON node "[1].id" should not exist

    @loginAsAdmin @logout
    Scenario: as a logged admin I want get the verification request for an non-existent user id
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/verification_requests?user=1000"
        Then the response status code should be 200
        And the response should be in JSON
        Then the response should be equal to:
        """
        []
        """

    @loginAsAdmin @logout
    Scenario: as a logged admin I want get the verification requests for multiple users : user 3 & user 5
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/verification_requests?user[]=3&user[]=5"
        Then the response status code should be 200
        And the response should be in JSON
        And the JSON node "[0].id" should be equal to 3
        And the JSON node "[0].status" should be equal to "VERIFICATION_REQUESTED"
        And the JSON node "[0].date" should not be null
        And the JSON node "[0].message" should not be null
        And the JSON node "[0].image" should be equal to "/api/media_objects/3"
        And the JSON node "[0].user" should be equal to "/api/users/3"
        And the JSON node "[1].id" should be equal to 5
        And the JSON node "[1].status" should be equal to "VERIFICATION_DECLINED"
        And the JSON node "[1].date" should not be null
        And the JSON node "[1].message" should not be null
        And the JSON node "[1].image" should be equal to "/api/media_objects/5"
        And the JSON node "[1].user" should be equal to "/api/users/5"

    @loginAsAdmin @logout
    Scenario: as a logged admin I want get all verification requests ordered by ascending date
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/verification_requests?order[date]=asc"
        Then the response status code should be 200
        And the response should be in JSON
        And the JSON node "[0].id" should be equal to 1
        And the JSON node "[0].status" should be equal to "VERIFICATION_REQUESTED"
        And the JSON node "[0].date" should not be null
        And the JSON node "[0].message" should not be null
        And the JSON node "[0].image" should be equal to "/api/media_objects/1"
        And the JSON node "[0].user" should be equal to "/api/users/1"

    @loginAsAdmin @logout
    Scenario: as a logged admin I want get all verification requests ordered by descending date
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/verification_requests?order[date]=desc"
        Then the response status code should be 200
        And the response should be in JSON
        And the JSON node "[0].id" should be equal to 11
        And the JSON node "[0].status" should be equal to "VERIFICATION_REQUESTED"
        And the JSON node "[0].date" should not be null
        And the JSON node "[0].message" should not be null
        And the JSON node "[0].image" should be equal to "/api/media_objects/11"
        And the JSON node "[0].user" should be equal to "/api/users/11"

    @loginAsUser @logout
    Scenario: as a logged user I want get my verification request
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/verification_requests/1"
        Then the response status code should be 200
        And the response should be in JSON
        And the JSON node "id" should be equal to 1
        And the JSON node "status" should be equal to "VERIFICATION_REQUESTED"
        And the JSON node "date" should not be null
        And the JSON node "message" should not be null
        And the JSON node "image" should be equal to "/api/media_objects/1"
        And the JSON node "user" should be equal to "/api/users/1"

    @loginAsUser @logout
    Scenario: as a logged user I want get the verification request of another user
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/verification_requests/7"
        Then the response status code should be 403
        And the response should be in JSON

    @loginAsAdmin @logout
    Scenario: as a logged admin I want get the verification request of user with id 7
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/verification_requests/7"
        Then the response status code should be 200
        And the response should be in JSON
        And the JSON node "id" should be equal to 7
        And the JSON node "status" should be equal to "VERIFICATION_APPROVED"
        And the JSON node "date" should not be null
        And the JSON node "message" should not be null
        And the JSON node "image" should be equal to "/api/media_objects/7"
        And the JSON node "user" should be equal to "/api/users/7"

    @loginAsAdmin @logout
    Scenario: as a logged admin I want get the verification request of a non-existent user id
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/verification_requests/xxx"
        Then the response status code should be 404
        And the response should be in JSON

    @loginAsUser @logout
    Scenario: as a logged user I want to modify my verification request
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "PUT" request to "/api/verification_requests/1" with body:
        """
        {
          "message": "I want to modify my verification request",
          "image": "/api/media_objects/13"
        }
        """
        Then the response status code should be 200
        And the header "Content-Location" should match "~/api/verification_requests/(\d+)~"
        And the response should be in JSON
        And the JSON node "id" should be equal to 1
        And the JSON node "status" should be equal to "VERIFICATION_REQUESTED"
        And the JSON node "date" should not be null
        And the JSON node "message" should be equal to "I want to modify my verification request"
        And the JSON node "image" should be equal to "/api/media_objects/13"
        And the JSON node "user" should be equal to "/api/users/1"

    @loginAsUser @logout
    Scenario: as a logged user I want to modify verification request for user with id = 3
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "PUT" request to "/api/verification_requests/3" with body:
        """
        {
          "message": "I want to modify verification request for user 3",
          "image": "/api/media_objects/14"
        }
        """
        Then the response status code should be 403
        And the response should be in JSON

    @loginAsUser @logout
    Scenario: as a logged user I want to modify only the message for my verification request
        When I add "Content-Type" header equal to "application/merge-patch+json"
        And I add "Accept" header equal to "application/json"
        And I send a "PATCH" request to "/api/verification_requests/1" with body:
        """
        {
          "message": "I want to modify only the message for my verification request"
        }
        """
        Then the response status code should be 200
        And the header "Content-Location" should match "~/api/verification_requests/(\d+)~"
        And the response should be in JSON
        And the JSON node "id" should be equal to 1
        And the JSON node "status" should be equal to "VERIFICATION_REQUESTED"
        And the JSON node "date" should not be null
        And the JSON node "message" should be equal to "I want to modify only the message for my verification request"
        And the JSON node "image" should be equal to "/api/media_objects/13"
        And the JSON node "user" should be equal to "/api/users/1"

    @loginAsUser @logout
    Scenario: as a logged user I want to modify only the message for for user with id = 3
        When I add "Content-Type" header equal to "application/merge-patch+json"
        And I add "Accept" header equal to "application/json"
        And I send a "PATCH" request to "/api/verification_requests/3" with body:
        """
        {
          "message": "I want to modify only the message for for user with id = 3"
        }
        """
        Then the response status code should be 403
        And the response should be in JSON

    @loginAsAdmin @logout
    Scenario: as a logged admin I want to decline the verification request of user with id = 1
        When I add "Content-Type" header equal to "application/merge-patch+json"
        And I add "Accept" header equal to "application/json"
        And I send a "PATCH" request to "/api/verification_requests/1" with body:
        """
        {
          "status": "VERIFICATION_DECLINED",
          "rejectionReason": "You will not be a blogger!"
        }
        """
        Then the response status code should be 200
        And the header "Content-Location" should match "~/api/verification_requests/(\d+)~"
        And the response should be in JSON
        And the JSON node "id" should be equal to 1
        And the JSON node "status" should be equal to "VERIFICATION_DECLINED"
        And the JSON node "date" should not be null
        And the JSON node "message" should be equal to "I want to modify only the message for my verification request"
        And the JSON node "image" should be equal to "/api/media_objects/13"
        And the JSON node "user" should be equal to "/api/users/1"

    @loginAsAdmin @logout
    Scenario: as a logged admin I want to approve the verification request of user with id = 2
        When I add "Content-Type" header equal to "application/merge-patch+json"
        And I add "Accept" header equal to "application/json"
        And I send a "PATCH" request to "/api/verification_requests/2" with body:
        """
        {
          "status": "VERIFICATION_APPROVED"
        }
        """
        Then the response status code should be 200
        And the header "Content-Location" should match "~/api/verification_requests/(\d+)~"
        And the response should be in JSON
        And the JSON node "id" should be equal to 2
        And the JSON node "status" should be equal to "VERIFICATION_APPROVED"
        And the JSON node "date" should not be null
        And the JSON node "message" should not be null
        And the JSON node "image" should be equal to "/api/media_objects/2"
        And the JSON node "user" should be equal to "/api/users/2"

    @loginAsUser @logout
    Scenario: as a logged user I want to modify my verification request after being declined
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "PUT" request to "/api/verification_requests/1" with body:
        """
        {
          "message": "I want to modify my verification request after being declined",
          "image": "/api/media_objects/15"
        }
        """
        Then the response status code should be 403
        And the response should be in JSON

    @verification @loginAsUser @logout
    Scenario: as a logged user I want to modify only the message for my verification request after being declined
        When I add "Content-Type" header equal to "application/merge-patch+json"
        And I add "Accept" header equal to "application/json"
        And I send a "PATCH" request to "/api/verification_requests/1" with body:
        """
        {
          "message": "I want to modify only the message for my verification request after being declined"
        }
        """
        Then the response status code should be 403
        And the response should be in JSON

    @verification @loginAsBlogger @logout
    Scenario: as a logged blogged I want to modify my verification request after being approved
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "PUT" request to "/api/verification_requests/1" with body:
        """
        {
          "message": "I want to modify my verification request after being approved",
          "image": "/api/media_objects/16"
        }
        """
        Then the response status code should be 403
        And the response should be in JSON

    @verification @loginAsBlogger @logout
    Scenario: as a logged blogger I want to modify only the message for my verification request after being approved
        When I add "Content-Type" header equal to "application/merge-patch+json"
        And I add "Accept" header equal to "application/json"
        And I send a "PATCH" request to "/api/verification_requests/6" with body:
        """
        {
          "message": "I want to modify only the message for my verification request after being declined"
        }
        """
        Then the response status code should be 403
        And the response should be in JSON