<?php

namespace App;

enum TransactionType: string
{
    case INCOME = 'income';
    case EXPENSE = 'expense';
}
