<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Role Configuration
    |--------------------------------------------------------------------------
    |
    | This is the single source of truth for role definitions.
    | Adding a new role or renaming an existing one only requires updating
    | this file + the database roles table.
    |
    | 'home_route'  → Route name to redirect to after login
    | 'label'       → Display label (used in UI)
    | 'is_admin'    → Whether this role accesses the Filament admin panel
    | 'panel'       → Which Filament panel this role can access (if any)
    |
    */

    'admin' => [
        'label' => 'Administrator',
        'home_route' => 'filament.admin.pages.dashboard',
        'is_admin' => true,
        'panel' => 'admin',
    ],

    'pimpinan-pusat' => [
        'label' => 'Pimpinan Pusat',
        'home_route' => '/page/dashboard',
        'is_admin' => false,
        'panel' => null,
    ],

    'pimpinan-daerah' => [
        'label' => 'Pimpinan Daerah',
        'home_route' => '/page/dashboard',
        'is_admin' => false,
        'panel' => null,
    ],

    'viewer' => [
        'label' => 'Viewer',
        'home_route' => '/page/dashboard',
        'is_admin' => false,
        'panel' => null,
    ],
];
