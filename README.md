# Fee calculation app
The application that makes the calculation and calculates a commission fee based on defined rules.

## Requirements
- To run the application, you must have Docker installed and compose plugin configured
- To receive data on currency exchange rates, you must have an account on https://exchangeratesapi.io/ to receive a token

## Install application
1. First you need to rename .env.example to .env and specify the token
2. You need to rename the file phpunit.xml.dist to phpunit.xml
3. Run in project folder cli command `bin/start`
4. Run in project folder cli command `bin/composer install`

That's it, the installation is complete

## Using the app
In the pub directory, there is a test transaction data file `input.csv` this file can be replaced with another one with different data, but it is important that the file structure remains unchanged.

To launch the application, just run the command `bin/fee_calculation`
The result of the script calculation will be displayed in the terminal, here is an example:
```
0.60
3.60
0.00
0.06
1.50
0
0.56
0.83
1.13
3.00
0.00
0.00
53
```

To run the tests, you need to run the command  `bin/cli bin/phpunit`

## Alternative launch

The application can be run without using Docker, for this, PHP must be installed on the computer, Composer must be configured. You need to repeat all the steps described above.

To launch the application, you need to run the command `php public/index.php`