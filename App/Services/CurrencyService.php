<?php

namespace App\Services;

use App\Api\ApiClientInterface;
use App\Database\SqliteDatabase;
use App\Models\Transaction;

class CurrencyService
{
    private ApiClientInterface $apiClient;
    private SqliteDatabase $database;

    public function __construct(
        ApiClientInterface $apiClient,
        SqliteDatabase     $database
    )
    {
        $this->apiClient = $apiClient;
        $this->database = $database;
    }

    public function buy(string $symbol, float $amount): void
    {
        $currency = $this->apiClient->getCryptoBySymbol($symbol);
        $transaction = new Transaction('buy', $symbol, $amount, $currency->getPrice());
        $this->database->save($transaction);
    }

    public function sell(string $symbol, float $amount): void
    {
        $currency = $this->apiClient->getCryptoBySymbol($symbol);
        $transaction = new Transaction('sell', $symbol, $amount, $currency->getPrice());
        $this->database->save($transaction);
    }

    public function getAllTransactions(): array
    {
        return $this->database->getAll();
    }
}