<?php
use PHPUnit\Framework\TestCase;
use App\CommissionCalculator;

class CommissionCalculatorTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCalculate()
    {
        // Устанавливаем курсы валют
        $rates = [
            'USD' => 1.083655,
            'JPY' => 169.612578,
            'EUR' => 1.0
        ];


        $depositFeeRate = 0.0003;
        $withdrawFeeRates = [
            'private' => 0.003,
            'business' => 0.005,
        ];

        $calculator = new CommissionCalculator($rates, $depositFeeRate, $withdrawFeeRates);


        $fileCSV = file_get_contents('public/input.csv', true);
        $csv = new \ParseCsv\Csv();
        $csv->encoding('UTF-8', 'UTF-8');
        $csv->delimiter = ",";
        $csv->heading = false;
        $csv->parseFile($fileCSV);

        $expectedOutput = [
            "0.60",
            "3.60",
            "0.00",
            "0.06",
            "1.50",
            "0",
            "0.54",
            "0.81",
            "0.30",
            "3.00",
            "0.00",
            "0.00",
            "50"
        ];

        $actualOutput = $calculator->calculate($csv->data);

        $this->assertEquals($expectedOutput, $actualOutput);
    }
}
