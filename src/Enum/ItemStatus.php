<?php

namespace App\Enum;

enum ItemStatus: string
{
    case AVAILABLE = 'available';
    case UNAVAILABLE = 'unavailable';
    case RESERVED = 'reserved';
    case LEASED = 'leased';
    case RETURNED = 'returned';
}
