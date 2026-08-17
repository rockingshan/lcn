<?php
namespace App;

final class Access
{
    public const PERMISSIONS = [
        'dashboard' => 'Dashboard', 'edit_channel' => 'Edit Channel',
        'modify_lcn' => 'Modify LCN', 'swap_lcn' => 'Swap LCN',
        'ird' => 'IRD Inventory', 'add_records' => 'Add Records',
        'logs' => 'Logs', 'generator' => 'LCN String Generator',
    ];

    public static function can(string $permission): bool
    {
        return !empty($_SESSION['is_admin']) || in_array($permission, $_SESSION['permissions'] ?? [], true);
    }

    public static function require(string $permission): void
    {
        if (!self::can($permission)) {
            http_response_code(403);
            echo 'You do not have permission to access this feature.';
            exit;
        }
    }
}
