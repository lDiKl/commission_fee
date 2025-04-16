<?php
declare(strict_types=1);

namespace App;

use Exception;

class CommissionCalculator
{
    private array $exchangeRates;
    private float $depositFeeRate;
    private array $withdrawFeeRates;

    public function __construct(
        array $exchangeRates,
        float $depositFeeRate = 0.0003,
        array $withdrawFeeRates = [
            'private' => 0.003,
            'business' => 0.005,
        ]
    ) {
        $this->exchangeRates = $exchangeRates;
        $this->depositFeeRate = $depositFeeRate;
        $this->withdrawFeeRates = $withdrawFeeRates;
    }

    /**
     * @throws Exception
     */
    public function calculate($operations): array
    {
        $fees = [];
        $weeklyWithdrawals = [];

        foreach ($operations as $operation) {
            [$date, $userId, $userType, $operationType, $amount, $currency] = $operation;

            if ($operationType === 'deposit') {
                $fee = $this->calculateDepositFee($amount);
            } elseif ($operationType === 'withdraw') {
                $fee = $this->calculateWithdrawFee($userId, $userType, $amount, $currency, $date, $weeklyWithdrawals);
            } else {
                throw new Exception("Unknown operation type: $operationType");
            }

            $fees[] = number_format($fee, $this->getDecimalPlaces($currency), '.', '');
        }

        return $fees;
    }

    private function calculateDepositFee($amount): float|int
    {
        return ceil($amount * $this->depositFeeRate * 100) / 100;
    }

    /**
     * @throws Exception
     */
    private function calculateWithdrawFee($userId, $userType, $amount, $currency, $date, &$weeklyWithdrawals): float|int
    {
        if (!isset($this->withdrawFeeRates[$userType])) {
            throw new Exception("Unknown user type: $userType");
        }

        $rate = $this->withdrawFeeRates[$userType];
        $amountInEur = $this->convertToBaseCurrency($amount, $currency);
        $week = $this->getWeek($date);

        if ($userType === 'private') {
            if (!isset($weeklyWithdrawals[$userId])) {
                $weeklyWithdrawals[$userId] = [];
            }

            if (!isset($weeklyWithdrawals[$userId][$week])) {
                $weeklyWithdrawals[$userId][$week] = ['count' => 0, 'amount' => 0];
            }

            $weeklyData = &$weeklyWithdrawals[$userId][$week];

            if ($weeklyData['count'] < 3) {
                if ($weeklyData['amount'] + $amountInEur <= 1000) {
                    // В пределах лимита — бесплатно
                    $weeklyData['count']++;
                    $weeklyData['amount'] += $amountInEur;
                    return 0;
                } else {
                    // Превышен лимит — комиссия с превышения
                    $exceedAmount = max(0, $weeklyData['amount'] + $amountInEur - 1000);
                    $weeklyData['count']++;
                    $weeklyData['amount'] += $amountInEur;
                    return ceil($exceedAmount * $rate * 100) / 100;
                }
            } else {
                // 4-я и последующие операции — комиссия со всей суммы
                $weeklyData['count']++;
                $weeklyData['amount'] += $amountInEur;
                return ceil($amountInEur * $rate * 100) / 100;
            }
        } else {
            // Business — комиссия со всей суммы
            return ceil($amount * $rate * 100) / 100;
        }
    }

    /**
     * @throws Exception
     */
    private function convertToBaseCurrency($amount, $currency)
    {
        if ($currency === getenv("BASE_CURRENCY")) {
            return $amount;
        }

        if (!isset($this->exchangeRates[$currency])) {
            throw new Exception("Unsupported currency: $currency");
        }

        return $amount / $this->exchangeRates[$currency];
    }

    private function getDecimalPlaces($currency): int
    {
        return match ($currency) {
            'JPY' => 0,
            default => 2,
        };
    }

    private function getWeek($date): string
    {
        $dt = new \DateTime($date);
        return $dt->format("oW");
    }
}
