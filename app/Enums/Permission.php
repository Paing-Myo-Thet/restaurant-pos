<?php

namespace App\Enums;

/**
 * Everything a role can be allowed to do. Each case is checked somewhere in
 * the code, so permissions are fixed here while roles are managed by admins.
 */
enum Permission: string
{
    case DashboardView = 'dashboard.view';
    case OrdersView = 'orders.view';
    case OrdersManage = 'orders.manage';
    case OrdersCancel = 'orders.cancel';
    case OrdersVoid = 'orders.void';
    case OrdersPrintDraft = 'orders.print_draft';
    case OrdersCheckout = 'orders.checkout';
    case KitchenManage = 'kitchen.manage';
    case TablesManage = 'tables.manage';
    case MenuManage = 'menu.manage';
    case InventoryManage = 'inventory.manage';
    case CustomersManage = 'customers.manage';
    case ReportsView = 'reports.view';
    case UsersManage = 'users.manage';
    case RolesManage = 'roles.manage';

    public function label(): string
    {
        return match ($this) {
            self::DashboardView => 'See the dashboard',
            self::OrdersView => 'See orders',
            self::OrdersManage => 'Take, edit, send, serve and cancel new orders',
            self::OrdersCancel => 'Cancel orders already sent to the kitchen',
            self::OrdersVoid => 'Cancel orders the kitchen has started (food is written off)',
            self::OrdersPrintDraft => 'Print a draft bill once the order is served',
            self::OrdersCheckout => 'Take payment and print receipts',
            self::KitchenManage => 'Use the kitchen screen and update cooking status',
            self::TablesManage => 'Manage tables',
            self::MenuManage => 'Manage products and categories',
            self::InventoryManage => 'Manage stock and see low-stock alerts',
            self::CustomersManage => 'Manage customers',
            self::ReportsView => 'See sales reports',
            self::UsersManage => 'Manage staff accounts',
            self::RolesManage => 'Manage roles and their permissions',
        };
    }

    public function group(): string
    {
        return match ($this) {
            self::DashboardView, self::ReportsView => 'Overview',
            self::OrdersView, self::OrdersManage, self::OrdersCancel, self::OrdersVoid,
            self::OrdersPrintDraft, self::OrdersCheckout => 'Orders',
            self::KitchenManage, self::TablesManage => 'Floor & kitchen',
            self::MenuManage, self::InventoryManage, self::CustomersManage => 'Menu & stock',
            self::UsersManage, self::RolesManage => 'Administration',
        };
    }

    /**
     * @return array<string, list<self>>
     */
    public static function grouped(): array
    {
        $groups = [];

        foreach (self::cases() as $permission) {
            $groups[$permission->group()][] = $permission;
        }

        return $groups;
    }
}
