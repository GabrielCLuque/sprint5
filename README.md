                                  Rolling Dices API                                                                           
To run this API made with Laravel 11.0, you will need a web server application that can handle PHP and also MySQL 
(for example: XAMPP) and another application to handle the API requests (for example: Postman).

You can see the routes for the requests in \api\routes\api.php.

The objective of the project is to create an API with Laravel. It consists of a dice game where users roll two dice 
with six faces each. The two numbers added together will be the result. If the result is 7, and only 7, the game 
will be considered a win. Any other result will be considered a loss.

The users can register with an email and can choose their names. Names are unique, and if they don’t choose a name, 
they will be called "Anonimo" (Anonymous). There are also two types of users: administrators and regular users. 
Administrators can perform additional functions (changing other users’ names, resetting the games of specific users, 
and checking all existing users' information on a list).

Users and administrators will be able to change their account names and play games. Non-registered users, 
registered users, and administrators will be able to check who has the best win ratio, the worst win ratio, 
see the ranking, and view the statistics of a specific player.

The API testing is done with PHPUnit and Mock. Seeders are provided in the \api\database\seeders\ folder. 
Additionally, one admin user and one regular user are provided to manually test the requests.

      1. Clone the repository:
  git clone https://github.com/your-username/your-repo.git
  cd your-repo

  (The branch to check the API is the one called develop)
  
      2. Install the dependencies:
  Ensure you have Composer installed. Then run:
  composer install
  
      3. Set up the environment:
  Copy the .env.example file to .env:
  cp .env.example .env
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=your_database
  DB_USERNAME=your_username
  DB_PASSWORD=your_password

  Make sure to configure the database credentials in the .env file and also in the .env.testing file.

      4. Run migrations and seed the database:
  php artisan migrate
  php artisan migrate --seed
  
      5. Generate the passport client:
  php artisan passport:client --personal
  
      6. Serve the application:
  php artisan serve
  
      7. Use the API client to interact with the API routes:
  To create a user, you will need to provide the following data:
  {
      "email": "ExamplePassword@example.com",
      "user_name": "ExampleName",
      "password": "123456789"
  }
  
      8. Login and get the user token to access other routes:
  {
      "email": "ExamplePassword@example.com",
      "password": "123456789"
  }
  
  
  
  Running Tests:
  
      1. Ensure the testing database is set up in your .env file. Make sure isn't called same as productions DataBase:
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=your_testing_database
  DB_USERNAME=your_username
  DB_PASSWORD=your_password

      2. Run the testing database migrations:
  php artisan migrate --env=testing
  
      3. Run the tests using PHPUnit:
  Can run all tests with: vendor/bin/phpunit tests
  Can run one specific test with: php artisan test --filter=GamesDeleteTest       (In this case we test the GamesDeleteTest.php document)
