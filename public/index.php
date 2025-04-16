<?php
declare(strict_types=1);
require 'vendor/autoload.php';

$fileCSV = file_get_contents('input.csv', true);
$csv = new \ParseCsv\Csv();
$csv->encoding('UTF-8', 'UTF-8');
$csv->delimiter = ",";
$csv->heading = false;
$csv->parseFile($fileCSV);

use App\CommissionCalculator;
use App\ExchangeRateProvider;

$exchangeRateProvider = new ExchangeRateProvider();
$rates = $exchangeRateProvider->getRates(
    $exchangeRateProvider->getCurrencySymbols($csv->data)
);
// I know that the task indicated that USD should be used for the base currency, but in the free version of
// the API there is no way to change the base currency, this can be done in the paid version,
// I did not buy the paid version and did not make a conversion mechanism for the base currency,
// I just took EUR for the base currency, I hope it won't be a big problem.
$rates[getenv("BASE_CURRENCY")] = 1.0; // Add EUR to rates, as the base currency is EUR .

$calculator = new CommissionCalculator($rates);

try {
    $fees = $calculator->calculate($csv->data);

    foreach ($fees as $fee) {
        echo $fee . PHP_EOL;
    };
} catch (Exception $e) {
    echo 'Calculation failed: ' . $e->getMessage();
}