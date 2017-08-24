# wag-tech-test
This service consumes users' scores and generates report that answers for the following questions:
- How many total players are there?
- How many people played the game today?
- List the top 10 players (by score)
- List the top 10 players who improved their score over the course of the week

API description:

Title : Post new score
URL : /users.php
Method : POST
Body : {"signed_request":"[alphanumeric and dot]",
        "user_score":[int]}
Response Codes : Success (200 OK), Bad Request (400)
Response Body: none

Title : Get report
URL : /users.php
Method : GET
Response Codes : Success (200 OK), Bad Request (400)
Response Body : {
                    "Total players": [int],
                    "Players today": [int],
                    "Top players": [
                        {
                            "user_id": [int],
                            "user_score": [int]
                        }],
                    "Improved players": {
                        "1": {
                            "user_id": [int],
                            "old_best": [int],
                            "new_best": [int] 
                            }
                    }
                }
                
Title : Generate sample data
URL : /testDataGenerator.php
Method : GET
Response Codes : Success (200 OK)
Response Body : none