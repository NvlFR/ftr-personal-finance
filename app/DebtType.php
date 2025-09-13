<?php

namespace App;

enum DebtType: string
{
    case DEBT = 'debt'; // Utang (Saya berutang)
    case RECEIVABLE = 'receivable'; // Piutang (Orang lain berutang ke saya)
}
