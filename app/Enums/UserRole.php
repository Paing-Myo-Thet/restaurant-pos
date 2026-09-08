<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case Waiter = 'waiter';
    case KITCHEN = 'kitchen';
    case CASHIER = 'cashier';
}