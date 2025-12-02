<?php

namespace App\Enum;

enum ItemCategory: string
{
    case WORK = 'work';
    case TABLE = 'table';
    case ARTS = 'arts';
    case AGRO = 'agro';
    case LEASURE = 'leasure';
    case OTHER = 'other';
}
