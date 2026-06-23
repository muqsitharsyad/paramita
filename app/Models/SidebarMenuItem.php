<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class SidebarMenuItem extends Model
{
    protected $fillable = [
        'label',
        'icon',
        'route_name',
        'url',
        'parent_id',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'sidebar_menu_item_role');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public static function iconSvg(?string $icon): HtmlString
    {
        $icons = static::icons();

        return new HtmlString($icons[$icon ?: 'info'] ?? $icons['info']);
    }

    public static function iconOptions(): array
    {
        return collect(static::icons())
            ->mapWithKeys(fn (string $svg, string $key) => [$key => $svg])
            ->all();
    }

    private static function icons(): array
    {
        $icons = require config_path('sidebar-icons.php');

        if (! Schema::hasTable('sidebar_icons')) {
            return $icons;
        }

        return array_replace($icons, SidebarIcon::query()
            ->where('is_active', true)
            ->pluck('svg', 'key')
            ->all());
    }

    /**
     * Get the URL for this menu item, preferring route_name over url.
     */
    public function getUrl(): ?string
    {
        if ($this->route_name && Route::has($this->route_name)) {
            return route($this->route_name);
        }
        return $this->url;
    }

    /**
     * Check if this menu item is currently active.
     */
    public function isActiveRoute(): bool
    {
        if ($this->route_name && request()->routeIs($this->route_name . '*')) {
            return true;
        }

        if (! $this->url) {
            return false;
        }

        $path = trim((string) parse_url($this->url, PHP_URL_PATH), '/');

        return $path !== '' && request()->is($path);
    }

    public static function treeForRole(string $roleName): Collection
    {
        return static::query()
            ->select(['id', 'label', 'icon', 'route_name', 'url', 'parent_id', 'order', 'is_active'])
            ->root()
            ->forRole($roleName)
            ->with([
                'children' => fn ($query) => $query
                    ->select(['id', 'label', 'icon', 'route_name', 'url', 'parent_id', 'order', 'is_active'])
                    ->forRole($roleName),
            ])
            ->get();
    }

    /**
     * Scope: get visible menu items for a given role name, ordered.
     */
    public function scopeForRole($query, string $roleName)
    {
        return $query->where('is_active', true)
            ->whereHas('roles', fn($q) => $q->where('name', $roleName))
            ->orderBy('order');
    }

    /**
     * Scope: top-level items only (no parent).
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
}
