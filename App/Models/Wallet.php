<?php

namespace App\Models;

use App\Database\SqliteDatabase;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\ConsoleOutput;

class Wallet
{
    private float $balance;
    private SqliteDatabase $database;

    public function __construct(float $balance, SqliteDatabase $database)
    {
        $this->balance = $balance;
        $this->database = $database;
    }

    public function getBalance(): float
    {
        return $this->walletBalance()['balance'];
    }

    public function walletBalance(): array
    {
        $wallet = [];
        $walletBalance = $this->balance;

        foreach ($this->database->getAll() as $transaction) {
            $symbol = strtoupper($transaction->getSymbol());
            $amount = $transaction->getAmount();
            $total = $transaction->getPrice() * $amount;

            if ($transaction->getType() === 'buy') {
                $walletBalance -= $total;
                $wallet[$symbol] = ($wallet[$symbol] ?? 0) + $amount;
            } elseif ($transaction->getType() === 'sell') {
                $walletBalance += $total;
                $wallet[$symbol] = ($wallet[$symbol] ?? 0) - $amount;
            }
        }

        $wallet['balance'] = $walletBalance;
        return $wallet;
    }

    public function showWallet(): void
    {
        $wallet = $this->walletBalance();
        $output = new ConsoleOutput();
        $table = new Table($output);
        $headers = [
            '<fg=red;options=bold>Symbol</>',
            '<fg=red;options=bold>Amount</>'
        ];
        $table->setHeaders($headers);
        foreach ($wallet as $symbol => $amount) {
            if ($symbol !== 'balance') {
                $table->addRow([strtoupper($symbol), $amount]);
            }
        }
        $table->addRow(new TableSeparator());
        $table->addRow([new TableCell('<fg=red;options=bold>Balance: </><options=bold>' . number_format($wallet['balance'], 2) . '$', ['colspan' => 2])]);

        $table->render();
    }
}