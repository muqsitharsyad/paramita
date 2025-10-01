<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JsonTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'category',
        'description',
        'template_data',
        'version',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'template_data' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the creator of this template
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the last updater of this template
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope for active templates only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get template data with merged variables
     */
    public function getFormattedTemplate(array $variables = [])
    {
        $template = $this->template_data;
        
        if (!empty($variables)) {
            $template = $this->replaceVariables($template, $variables);
        }
        
        return $template;
    }

    /**
     * Replace variables in template recursively
     */
    private function replaceVariables($data, array $variables)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->replaceVariables($value, $variables);
            }
        } elseif (is_string($data)) {
            foreach ($variables as $var => $value) {
                $data = str_replace('{{' . $var . '}}', $value, $data);
            }
        }
        
        return $data;
    }

    /**
     * Get template by name and category
     */
    public static function getTemplate($name, $category = null)
    {
        $query = static::active()->where('name', $name);
        
        if ($category) {
            $query->where('category', $category);
        }
        
        return $query->first();
    }

    /**
     * Create a new version of existing template
     */
    public function createNewVersion()
    {
        $newVersion = $this->replicate();
        $newVersion->version = $this->version + 1;
        $newVersion->save();
        
        return $newVersion;
    }
}