<?php

namespace App\Helpers;

use App\Models\JsonTemplate;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class JsonTemplateHelper
{
    /**
     * Get template by name and category
     */
    public static function getTemplate(string $name, string $category = null): ?JsonTemplate
    {
        $cacheKey = "json_template_{$name}" . ($category ? "_{$category}" : '');
        
        return Cache::remember($cacheKey, 3600, function () use ($name, $category) {
            return JsonTemplate::getTemplate($name, $category);
        });
    }

    /**
     * Generate formatted JSON response using template
     */
    public static function generateResponse(
        string $templateName,
        array $data = [],
        array $variables = [],
        string $category = null,
        int $httpCode = 200,
        string $message = null
    ): array {
        $template = static::getTemplate($templateName, $category);
        
        if (!$template) {
            return static::getDefaultErrorResponse("Template '{$templateName}' not found", 404);
        }

        // Default variables
        $defaultVariables = [
            'timestamp' => Carbon::now()->toISOString(),
            'code' => $httpCode,
            'status' => $httpCode >= 200 && $httpCode < 300 ? 'success' : 'error',
            'message' => $message ?? static::getDefaultMessage($httpCode),
        ];

        // Merge variables
        $allVariables = array_merge($defaultVariables, $variables);

        // Get formatted template
        $formattedTemplate = $template->getFormattedTemplate($allVariables);

        // Merge with provided data
        if (!empty($data) && isset($formattedTemplate['data'])) {
            if (is_array($formattedTemplate['data'])) {
                $formattedTemplate['data'] = array_merge($formattedTemplate['data'], $data);
            } else {
                $formattedTemplate['data'] = $data;
            }
        } elseif (!empty($data)) {
            $formattedTemplate['data'] = $data;
        }

        return $formattedTemplate;
    }

    /**
     * Generate JSON response and return as JsonResponse
     */
    public static function jsonResponse(
        string $templateName,
        array $data = [],
        array $variables = [],
        string $category = null,
        int $httpCode = 200,
        string $message = null
    ): JsonResponse {
        $response = static::generateResponse($templateName, $data, $variables, $category, $httpCode, $message);
        
        return response()->json($response, $httpCode);
    }

    /**
     * Get default success response structure
     */
    public static function getDefaultSuccessResponse(array $data = [], string $message = 'Operation completed successfully'): array
    {
        return [
            'status' => 'success',
            'code' => 200,
            'message' => $message,
            'timestamp' => Carbon::now()->toISOString(),
            'data' => $data,
        ];
    }

    /**
     * Get default error response structure
     */
    public static function getDefaultErrorResponse(string $message = 'An error occurred', int $code = 500, array $errors = []): array
    {
        $response = [
            'status' => 'error',
            'code' => $code,
            'message' => $message,
            'timestamp' => Carbon::now()->toISOString(),
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return $response;
    }

    /**
     * Get default message based on HTTP code
     */
    private static function getDefaultMessage(int $httpCode): string
    {
        return match ($httpCode) {
            200 => 'Operation completed successfully',
            201 => 'Resource created successfully',
            202 => 'Request accepted for processing',
            204 => 'Operation completed successfully',
            400 => 'Bad request',
            401 => 'Unauthorized access',
            403 => 'Access forbidden',
            404 => 'Resource not found',
            409 => 'Conflict occurred',
            422 => 'Validation failed',
            429 => 'Too many requests',
            500 => 'Internal server error',
            502 => 'Bad gateway',
            503 => 'Service unavailable',
            default => 'Response',
        };
    }

    /**
     * Get available templates by category
     */
    public static function getTemplatesByCategory(string $category): \Illuminate\Database\Eloquent\Collection
    {
        return JsonTemplate::active()
            ->category($category)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get all active templates grouped by category
     */
    public static function getAllTemplatesGrouped(): array
    {
        $templates = JsonTemplate::active()
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return $templates->groupBy('category')->toArray();
    }

    /**
     * Validate template JSON structure
     */
    public static function validateTemplate(array $templateData): array
    {
        $errors = [];

        // Check for required fields in API response templates
        if (!isset($templateData['status'])) {
            $errors[] = 'Template should include "status" field';
        }

        if (!isset($templateData['code'])) {
            $errors[] = 'Template should include "code" field';
        }

        if (!isset($templateData['message'])) {
            $errors[] = 'Template should include "message" field';
        }

        if (!isset($templateData['timestamp'])) {
            $errors[] = 'Template should include "timestamp" field';
        }

        return $errors;
    }

    /**
     * Clear template cache
     */
    public static function clearCache(string $name = null, string $category = null): void
    {
        if ($name) {
            $cacheKey = "json_template_{$name}" . ($category ? "_{$category}" : '');
            Cache::forget($cacheKey);
        } else {
            // Clear all template cache
            Cache::flush();
        }
    }

    /**
     * Generate dashboard response using the dashboard template
     */
    public static function generateDashboardResponse(array $dashboardData): array
    {
        return static::generateResponse(
            'dashboard',
            $dashboardData,
            [
                'message' => 'Dashboard data fetched successfully.'
            ],
            'dashboard'
        );
    }

    /**
     * Generate API response for data listing
     */
    public static function generateListResponse(array $items, array $meta = [], string $message = null): array
    {
        $data = ['items' => $items];
        
        if (!empty($meta)) {
            $data['meta'] = $meta;
        }

        return static::generateResponse(
            'list',
            $data,
            [
                'message' => $message ?? 'Data retrieved successfully.'
            ],
            'api-response'
        );
    }

    /**
     * Generate API response for single item
     */
    public static function generateItemResponse(array $item, string $message = null): array
    {
        return static::generateResponse(
            'item',
            $item,
            [
                'message' => $message ?? 'Data retrieved successfully.'
            ],
            'api-response'
        );
    }
}