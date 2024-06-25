<?php

namespace App\Database;

use App\Models\Transaction;
use Medoo\Medoo;

class SqliteDatabase
{
    private Medoo $database;

    public function __construct(string $databaseFile = 'storage/database.sqlite')
    {
        $this->database = new Medoo([
            'database_type' => 'sqlite',
            'database_name' => $databaseFile,
        ]);
        $this->createTable();
    }

    private function createTable(): void
    {
        $this->database->exec('CREATE TABLE IF NOT EXISTS transactions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            type TEXT NOT NULL,
            symbol TEXT NOT NULL,
            amount REAL NOT NULL,
            price REAL NOT NULL
            )');
    }

    public function save(Transaction $transaction): void
    {

        $this->database->insert('transactions', [
            'type' => $transaction->getType(),
            'symbol' => strtoupper($transaction->getSymbol()),
            'amount' => $transaction->getAmount(),
            'price' => $transaction->getPrice(),
        ]);

    }

    public function getAll(): array
    {
        $transactions = [];

        $transactionsData = $this->database->select('transactions',
            ['id', 'type', 'symbol', 'amount', 'price']
        );

        foreach ($transactionsData as $data) {
            $transactions[] = Transaction::fromArray($data);
        }

        return $transactions;
    }
}