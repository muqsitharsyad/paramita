<?php

namespace App\Helpers;

class RoleHelper
{
    /**
     * Get the home route name for a given role.
     */
    public static function getHomeRoute(string $roleName): ?string
    {
        $roles = config('roles', []);

        return $roles[$roleName]['home_route'] ?? null;
    }

    /**
     * Get the label for a given role.
     */
    public static function getLabel(string $roleName): string
    {
        $roles = config('roles', []);

        return $roles[$roleName]['label'] ?? ucfirst(str_replace('-', ' ', $roleName));
    }

    /**
     * Get the first assigned role name for a user.
     */
    public static function getPrimaryRoleName($user): ?string
    {
        if (! $user) {
            return null;
        }

        $user->loadMissing('roles:id,name');

        return $user->roles->first()?->name;
    }

    /**
     * Get the Filament panel ID for a role (if any).
     */
    public static function getPanel(string $roleName): ?string
    {
        $roles = config('roles', []);

        return $roles[$roleName]['panel'] ?? null;
    }

    /**
     * Check if a role has access to a specific Filament panel.
     */
    public static function canAccessPanel(string $roleName, string $panelId): bool
    {
        return self::getPanel($roleName) === $panelId;
    }

    /**
     * Get the dashboard/home route for a user.
     * Falls back to login if no matching role is found.
     */
    public static function getUserHomeRoute($user): ?string
    {
        if (! $user) {
            return 'login';
        }

        $role = self::getPrimaryRoleName($user);

        if (! $role) {
            return null;
        }

        return self::getHomeRoute($role);
    }

}
