Feature: Post feature
    @loginAsUser @logout
    Scenario: as a logged user I want to get the list of posts
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/posts"
        Then the response status code should be 200
        And the response should be in JSON

    @loginAsBlogger @logout
    Scenario: as a logged blogger I want to get the list of posts
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/posts"
        Then the response status code should be 200
        And the response should be in JSON

    @loginAsAdmin @logout
    Scenario: as a logged admin I want to get the list of posts
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/posts"
        Then the response status code should be 200
        And the response should be in JSON

    Scenario: as an anonymous user I want to get the list of posts
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/posts"
        Then the response status code should be 401
        And the response should be in JSON

    @loginAsUser @logout
    Scenario: as a logged user I want to get create a new post
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "POST" request to "/api/posts" with body:
        """
        {
          "title": "Test blog post",
          "content": "This is a test blog post"
        }
        """
        Then the response status code should be 403
        And the response should be in JSON

    @loginAsBlogger @logout
    Scenario: as a logged blogger I want to get create a new post
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "POST" request to "/api/posts" with body:
        """
        {
          "title": "Test blog post",
          "content": "This is a test blog post"
        }
        """
        Then the response status code should be 201
        And the response should be in JSON
        And the header "Content-Location" should match "~/api/posts/(\d+)~"
        And the JSON nodes should contain:
            | title                  | Test blog post                        |
            | content                | This is a test blog post              |
        And the JSON node "id" should not be null
        And the JSON node "id" should match "~(\d+)~"
        And the JSON node "user" should not be null
        And the JSON node "user" should match "~/api/users/(\d+)~"
        And the JSON node "date" should not be null

    @loginAsAdmin @logout
    Scenario: as a logged admin I want to get create a new post
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "POST" request to "/api/posts" with body:
        """
        {
          "title": "Test admin blog post",
          "content": "This is a test admin blog post"
        }
        """
        Then the response status code should be 201
        And the response should be in JSON
        And the header "Content-Location" should match "~/api/posts/(\d+)~"
        And the JSON nodes should contain:
            | title                  | Test admin blog post                  |
            | content                | This is a test admin blog post        |
        And the JSON node "id" should not be null
        And the JSON node "id" should match "~(\d+)~"
        And the JSON node "user" should not be null
        And the JSON node "user" should match "~/api/users/(\d+)~"
        And the JSON node "date" should not be null

    @loginAsBlogger @logout
    Scenario: as a logged blogger try to create a new post with missing parameters
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "POST" request to "/api/posts" with body:
        """
        {
          "title": "",
          "content": ""
        }
        """
        Then the response status code should be 400
        And the response should be in JSON
        And the JSON node "violations[0].propertyPath" should be equal to "title"
        And the JSON node "violations[1].propertyPath" should be equal to "content"

    @loginAsUser @logout
    Scenario: as a logged user I want to get blog post with id 1
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/posts/1"
        Then the response status code should be 200
        And the response should be in JSON
        And the JSON node "id" should be equal to 1
        And the JSON node "title" should not be null
        And the JSON node "content" should not be null
        And the JSON node "user" should not be null
        And the JSON node "user" should match "~/api/users/(\d+)~"
        And the JSON node "date" should not be null

    @loginAsBlogger @logout
    Scenario: as a logged blogger I want to get blog post with id 6
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/posts/6"
        Then the response status code should be 200
        And the response should be in JSON
        And the JSON node "id" should be equal to 6
        And the JSON node "title" should not be null
        And the JSON node "content" should not be null
        And the JSON node "user" should not be null
        And the JSON node "user" should match "~/api/users/(\d+)~"
        And the JSON node "date" should not be null

    @loginAsAdmin @logout
    Scenario: as a logged admin I want to get blog post with id 15
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/posts/15"
        Then the response status code should be 200
        And the response should be in JSON
        And the JSON node "id" should be equal to 15
        And the JSON node "title" should not be null
        And the JSON node "content" should not be null
        And the JSON node "user" should not be null
        And the JSON node "user" should match "~/api/users/(\d+)~"
        And the JSON node "date" should not be null

    @loginAsUser @logout
    Scenario: as a logged user I want to get blog post with id with non-existent id
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/posts/xxx"
        Then the response status code should be 404
        And the response should be in JSON

    @loginAsBlogger @logout
    Scenario: as a logged blogger I want to get blog post with with non-existent id
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "GET" request to "/api/posts/xxx"
        Then the response status code should be 404
        And the response should be in JSON

    @loginAsUser @logout
    Scenario: as a logged user I want to update a post
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "PUT" request to "/api/posts/1" with body:
        """
        {
          "title": "Test blog post",
          "content": "This is a test blog post"
        }
        """
        Then the response status code should be 403
        And the response should be in JSON

    @loginAsBlogger @logout
    Scenario: as a logged blogger I want to update one of my posts
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "PUT" request to "/api/posts/1" with body:
        """
        {
          "title": "Test blog post Updated",
          "content": "This is a test blog post Updated"
        }
        """
        Then the response status code should be 200
        And the response should be in JSON
        And the JSON nodes should contain:
            | title                  | Test blog post Updated                |
            | content                | This is a test blog post Updated      |
        And the JSON node "id" should be equal to 1
        And the JSON node "title" should not be null
        And the JSON node "content" should not be null
        And the JSON node "user" should not be null
        And the JSON node "user" should match "~/api/users/(\d+)~"
        And the JSON node "date" should not be null

    @loginAsAdmin @logout
    Scenario: as a logged admin I want to update one of blogger 1 posts
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "PUT" request to "/api/posts/1" with body:
        """
        {
          "title": "Test blog post Updated by admin",
          "content": "This is a test blog post Updated by admin"
        }
        """
        Then the response status code should be 200
        And the response should be in JSON
        And the JSON nodes should contain:
            | title                  | Test blog post Updated by admin                |
            | content                | This is a test blog post Updated by admin      |
        And the JSON node "id" should be equal to 1
        And the JSON node "title" should not be null
        And the JSON node "content" should not be null
        And the JSON node "user" should not be null
        And the JSON node "user" should match "~/api/users/(\d+)~"
        And the JSON node "date" should not be null

    @loginAsBlogger @logout
    Scenario: as a logged blogger I want to update one of my posts with missing parameters
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "PUT" request to "/api/posts/1" with body:
        """
        {
          "title": "",
          "content": ""
        }
        """
        Then the response status code should be 400
        And the response should be in JSON
        And the JSON node "violations[0].propertyPath" should be equal to "title"
        And the JSON node "violations[1].propertyPath" should be equal to "content"

    @loginAsBlogger @logout
    Scenario: as a logged blogger I want to update one post that doesn't belong to me
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "PUT" request to "/api/posts/6" with body:
        """
        {
          "title": "Test blog post Updated",
          "content": "This is a test blog post Updated"
        }
        """
        Then the response status code should be 403
        And the response should be in JSON

    @loginAsBlogger @logout
    Scenario: as a logged blogger I want to update a post with non-existent id
        When I add "Content-Type" header equal to "application/json"
        And I add "Accept" header equal to "application/json"
        And I send a "PUT" request to "/api/posts/xxx" with body:
        """
        {
          "title": "Test blog post Updated",
          "content": "This is a test blog post Updated"
        }
        """
        Then the response status code should be 404
        And the response should be in JSON