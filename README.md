## TrackTik challenge

###How to run the application
1. Clone the repo
2. Create a new .env file using .env.example
   1. `cp .env.example .env`
3. Be sure to set `TRACKTIK_TOKEN` and `TRACKTIK_REFRESH_TOKEN` with newly generated values.
4. Run `composer install`
5. Run `php artisan serve`
6. Open in Postman the collection found in project root
   1. TrackTik Challenge.postman_collection.json
7. Play around



### How to test different identity providers ?
In the route `/api/{idp}/employees` the idp is a param corresponding to the identity provider
By default I've added _first_ and _second_. you can replace that with whatever you chose to build in the code
### first Identity Provider accept in body
`{
"name": "testabs",
"surname": "testabs",
"email": "sss@test.com"
}`
### second Identity Provider accept in body
`{
"first_name": "testabs",
"last_name": "testabs",
"email": "sss@test.com"
}`
To create you own IdP follow the steps
1. Create a new class in app/IdentityProviders and extend Provider class
2. add `$mappingField` key -> value. Copy the value from the other IdP and set the keys tha one your IdP will send in Request Body
3. Do the same thing in the method `getValidationRules` so change the keys.
4. In the constructor of EmployeeController add the reference of you class in $this->pathToIdentityProvider, key is the string to be set in the path. value the instance of the class.
5. make the request to `/api/whatever/employees
