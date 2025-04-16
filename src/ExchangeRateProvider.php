<?php
declare(strict_types=1);

namespace App;

class ExchangeRateProvider
{
    private string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = 'http://api.exchangeratesapi.io/v1/latest?access_key='.getenv("EXCHANGERATES_ACCESS_KEY");
    }

    public function getRates($currencySymbols = null): array
    {
        $response = file_get_contents($currencySymbols ? $this->apiUrl.'&symbols='.$currencySymbols : $this->apiUrl);
        $data = json_decode($response, true);
        return $data['rates'];
    }

    public function getCurrencySymbols($data): string
    {
        $return = [];

        foreach ($data as $value) {
            [$date, $userId, $userType, $operationType, $amount, $currency] = $value;
            $return[] = $currency;
        }
        return implode(',',array_unique($return));
    }
}