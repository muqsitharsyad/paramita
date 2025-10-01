<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApiEndpointResource\Pages;
use App\Models\ApiEndpoint;
use App\Models\VendorApi;
use App\Models\JsonTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use App\Helpers\VendorApiAuthHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;

class ApiEndpointResource extends Resource
{
    protected static ?string $model = ApiEndpoint::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationGroup = 'API Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Endpoint Information')
                    ->schema([
                        Forms\Components\Select::make('vendor_api_id')
                            ->relationship('vendorApi', 'api_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Vendor API'),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Endpoint Name'),
                        Forms\Components\TextInput::make('path')
                            ->required()
                            ->maxLength(255)
                            ->label('Endpoint Path')
                            ->helperText('e.g., /users/{id} or /products'),
                        Forms\Components\Select::make('method')
                            ->options([
                                'GET' => 'GET',
                                'POST' => 'POST',
                                'PUT' => 'PUT',
                                'PATCH' => 'PATCH',
                                'DELETE' => 'DELETE',
                            ])
                            ->default('GET')
                            ->required()
                            ->native(false)
                            ->label('HTTP Method'),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->label('Description')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'deprecated' => 'Deprecated',
                            ])
                            ->default('active')
                            ->required()
                            ->native(false)
                            ->label('Status'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Health Status')
                    ->schema([
                        Forms\Components\Select::make('health_status')
                            ->options([
                                'unknown' => 'Unknown',
                                'healthy' => 'Healthy',
                                'unhealthy' => 'Unhealthy',
                                'warning' => 'Warning',
                            ])
                            ->default('unknown')
                            ->disabled()
                            ->label('Health Status')
                            ->helperText('Health status is automatically updated when testing the endpoint'),
                        Forms\Components\DateTimePicker::make('last_tested_at')
                            ->disabled()
                            ->label('Last Tested At')
                            ->helperText('Timestamp of the last test run'),
                        Forms\Components\Textarea::make('health_message')
                            ->disabled()
                            ->label('Health Message')
                            ->helperText('Details about the health status')
                            ->rows(3),
                    ])
                    ->columns(2)
                    ->visibleOn('edit'),

                Forms\Components\Section::make('JSON Template')
                    ->schema([
                        Forms\Components\Select::make('json_template_id')
                            ->relationship('jsonTemplate', 'name')
                            ->searchable()
                            ->preload()
                            ->label('JSON Response Template')
                            ->helperText('Select a JSON template to validate response format')
                            ->options(function () {
                                return JsonTemplate::where('is_active', true)->pluck('name', 'id');
                            })
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $template = JsonTemplate::find($state);
                                    if ($template) {
                                        $set('template_preview', $template->template_data);
                                    }
                                } else {
                                    $set('template_preview', null);
                                }
                            }),
                        Forms\Components\Textarea::make('template_preview')
                            ->label('Template Preview')
                            ->rows(10)
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn (Forms\Get $get) => !empty($get('template_preview')))
                            ->formatStateUsing(function ($state) {
                                if (!$state) return '';
                                $decoded = json_decode($state, true);
                                return $decoded ? json_encode($decoded, JSON_PRETTY_PRINT) : $state;
                            })
                            ->extraAttributes([
                                'style' => 'font-family: monospace; font-size: 12px; background: #f8f9fa;'
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Parameters')
                    ->schema([
                        Forms\Components\KeyValue::make('parameters')
                            ->label('Request Parameters')
                            ->keyLabel('Parameter Name')
                            ->valueLabel('Parameter Description')
                            ->addActionLabel('Add Parameter')
                            ->helperText('Define the parameters required for this endpoint'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Security')
                    ->schema([
                        Forms\Components\Toggle::make('requires_auth')
                            ->default(true)
                            ->label('Requires Authentication')
                            ->helperText('Whether this endpoint requires authentication'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vendorApi.api_name')
                    ->searchable()
                    ->sortable()
                    ->label('API Name'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Endpoint Name'),
                // Tables\Columns\TextColumn::make('path')
                //     ->searchable()
                //     ->label('Path')
                //     ->copyable(),
                Tables\Columns\BadgeColumn::make('method')
                    ->colors([
                        'primary' => 'GET',
                        'success' => 'POST',
                        'warning' => 'PUT',
                        'info' => 'PATCH',
                        'danger' => 'DELETE',
                    ])
                    ->label('Method'),
                Tables\Columns\BadgeColumn::make('health_status')
                    ->colors([
                        'success' => 'healthy',
                        'danger' => 'unhealthy',
                        'warning' => 'warning',
                        'gray' => 'unknown',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'healthy' => 'Healthy',
                        'unhealthy' => 'Unhealthy',
                        'warning' => 'Warning',
                        'unknown' => 'Unknown',
                        default => 'Unknown',
                    })
                    ->label('Health'),
                Tables\Columns\TextColumn::make('jsonTemplate.name')
                    ->label('JSON Template')
                    ->badge()
                    ->color('info')
                    ->default('No Template')
                    ->formatStateUsing(fn ($state) => $state ?? 'No Template'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'inactive',
                        'danger' => 'deprecated',
                    ])
                    ->label('Status'),
                Tables\Columns\IconColumn::make('requires_auth')
                    ->boolean()
                    ->label('Auth')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('last_tested_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Last Tested')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Created At'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Updated At'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('vendor_api_id')
                    ->relationship('vendorApi', 'api_name')
                    ->searchable()
                    ->preload()
                    ->label('Vendor API'),
                Tables\Filters\SelectFilter::make('json_template_id')
                    ->relationship('jsonTemplate', 'name')
                    ->searchable()
                    ->preload()
                    ->label('JSON Template'),
                Tables\Filters\SelectFilter::make('method')
                    ->options([
                        'GET' => 'GET',
                        'POST' => 'POST',
                        'PUT' => 'PUT',
                        'PATCH' => 'PATCH',
                        'DELETE' => 'DELETE',
                    ])
                    ->label('HTTP Method'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'deprecated' => 'Deprecated',
                    ])
                    ->label('Status'),
                Tables\Filters\SelectFilter::make('health_status')
                    ->options([
                        'unknown' => 'Unknown',
                        'healthy' => 'Healthy',
                        'unhealthy' => 'Unhealthy',
                        'warning' => 'Warning',
                    ])
                    ->label('Health Status'),
                Tables\Filters\TernaryFilter::make('requires_auth')
                    ->label('Authentication Required'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('test')
                    ->icon('heroicon-o-play')
                    ->color('info')
                    ->form([
                        Forms\Components\Section::make('Test Configuration')
                            ->schema([
                                Forms\Components\Placeholder::make('endpoint_info')
                                    ->label('Endpoint')
                                    ->content(fn (ApiEndpoint $record) => "{$record->method} {$record->vendorApi->base_url}{$record->path}")
                                    ->columnSpanFull(),
                                Forms\Components\Placeholder::make('template_info')
                                    ->label('JSON Template')
                                    ->content(fn (ApiEndpoint $record) => $record->jsonTemplate 
                                        ? "{$record->jsonTemplate->name} (v{$record->jsonTemplate->version})" 
                                        : 'No template assigned - response validation will be skipped')
                                    ->columnSpanFull(),
                                Forms\Components\Placeholder::make('health_info')
                                    ->label('Current Health Status')
                                    ->content(fn (ApiEndpoint $record) => $record->health_status_label . 
                                        ($record->last_tested_at ? " (Last tested: {$record->last_tested_at->diffForHumans()})" : " (Never tested)"))
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Section::make('Test Parameters')
                            ->schema([
                                Forms\Components\KeyValue::make('test_parameters')
                                    ->label('URL Parameters')
                                    ->keyLabel('Parameter Name')
                                    ->valueLabel('Parameter Value')
                                    ->addActionLabel('Add Parameter')
                                    ->helperText('Add test values for URL parameters (e.g., {id} in path)'),
                                Forms\Components\Textarea::make('test_body')
                                    ->label('Request Body (JSON)')
                                    ->helperText('For POST/PUT/PATCH requests')
                                    ->rows(5)
                                    ->visible(fn (ApiEndpoint $record) => in_array($record->method, ['POST', 'PUT', 'PATCH']))
                                    ->columnSpanFull(),
                                Forms\Components\KeyValue::make('test_headers')
                                    ->label('Additional Headers')
                                    ->keyLabel('Header Name')
                                    ->valueLabel('Header Value')
                                    ->addActionLabel('Add Header')
                                    ->helperText('Add custom headers for the test request'),
                            ])
                    ])
                    ->action(function (ApiEndpoint $record, array $data) {
                        return self::testEndpoint($record, $data);
                    })
                    ->modalHeading('Test Endpoint')
                    ->modalDescription(fn (ApiEndpoint $record) => "Test the endpoint and validate against JSON template")
                    ->modalSubmitActionLabel('Run Test')
                    ->modalWidth('3xl'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('bulk_test')
                        ->label('Test Selected Endpoints')
                        ->icon('heroicon-o-play')
                        ->color('info')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                self::testEndpoint($record, []);
                            }
                            
                            Notification::make()
                                ->title('Bulk Test Completed')
                                ->body('All selected endpoints have been tested. Check individual results.')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Test Multiple Endpoints')
                        ->modalDescription('This will test all selected endpoints with default parameters. Continue?')
                        ->modalSubmitActionLabel('Test All'),
                ]),
            ]);
    }

    private static function createSuccessNotification(ApiEndpoint $endpoint, $response, $healthStatus, $healthMessage, $templateValidation, $fullUrl): void
    {
        $statusCode = $response->status();
        $responseSize = strlen($response->body());
        
        $healthBadge = match ($healthStatus) {
            'healthy' => '<span style="background: #10b981; color: white; padding: 4px 8px; border-radius: 6px; font-weight: bold;">✅ HEALTHY</span>',
            'warning' => '<span style="background: #f59e0b; color: white; padding: 4px 8px; border-radius: 6px; font-weight: bold;">⚠️ WARNING</span>',
            'unhealthy' => '<span style="background: #ef4444; color: white; padding: 4px 8px; border-radius: 6px; font-weight: bold;">❌ UNHEALTHY</span>',
            default => '<span style="background: #6b7280; color: white; padding: 4px 8px; border-radius: 6px; font-weight: bold;">❓ UNKNOWN</span>'
        };
        
        $templateBadge = '';
        if ($templateValidation) {
            if ($templateValidation['valid']) {
                $templateBadge = '<span style="background: #10b981; color: white; padding: 4px 8px; border-radius: 6px; font-weight: bold;">✅ PASSED</span>';
            } else {
                $templateBadge = '<span style="background: #ef4444; color: white; padding: 4px 8px; border-radius: 6px; font-weight: bold;">❌ FAILED</span>';
            }
        } else {
            $templateBadge = '<span style="background: #6b7280; color: white; padding: 4px 8px; border-radius: 6px; font-weight: bold;">➖ SKIPPED</span>';
        }
        
        $htmlBody = "
        <div style='font-family: ui-monospace, SFMono-Regular, Consolas, monospace; font-size: 13px; line-height: 1.6;'>
            <div style='background: #f8fafc; padding: 12px; border-radius: 8px; margin-bottom: 12px;'>
                <div style='font-weight: bold; color: #1f2937; margin-bottom: 8px;'>📡 Request Information</div>
                <div style='color: #374151;'>
                    <strong>URL:</strong> <code style='background: #e5e7eb; padding: 2px 6px; border-radius: 4px;'>{$fullUrl}</code><br>
                    <strong>Method:</strong> <code style='background: #e5e7eb; padding: 2px 6px; border-radius: 4px;'>{$endpoint->method}</code><br>
                    <strong>Status Code:</strong> <code style='background: #e5e7eb; padding: 2px 6px; border-radius: 4px;'>{$statusCode}</code><br>
                    <strong>Response Size:</strong> " . number_format($responseSize) . " bytes
                </div>
            </div>
            
            <div style='background: #f0fdf4; padding: 12px; border-radius: 8px; margin-bottom: 12px; border-left: 4px solid #10b981;'>
                <div style='font-weight: bold; color: #1f2937; margin-bottom: 8px;'>🏥 Health Status</div>
                <div style='margin-bottom: 8px;'>{$healthBadge}</div>
                <div style='color: #374151;'><strong>Message:</strong> {$healthMessage}</div>
            </div>
            
            <div style='background: #fefce8; padding: 12px; border-radius: 8px; border-left: 4px solid #eab308;'>
                <div style='font-weight: bold; color: #1f2937; margin-bottom: 8px;'>🎯 Template Validation</div>
                <div style='margin-bottom: 8px;'>{$templateBadge}</div>";
        
        if ($templateValidation) {
            $htmlBody .= "<div style='color: #374151;'><strong>Template:</strong> <code style='background: #e5e7eb; padding: 2px 6px; border-radius: 4px;'>{$endpoint->jsonTemplate->name}</code> (v{$endpoint->jsonTemplate->version})</div>";
            
            if (!$templateValidation['valid'] && !empty($templateValidation['errors'])) {
                $htmlBody .= "<div style='margin-top: 8px; color: #dc2626;'><strong>Errors:</strong></div>";
                $htmlBody .= "<ul style='margin: 4px 0; padding-left: 20px; color: #7f1d1d;'>";
                foreach (array_slice($templateValidation['errors'], 0, 3) as $error) {
                    $htmlBody .= "<li>{$error}</li>";
                }
                if (count($templateValidation['errors']) > 3) {
                    $htmlBody .= "<li><em>... and " . (count($templateValidation['errors']) - 3) . " more errors</em></li>";
                }
                $htmlBody .= "</ul>";
            }
        } else {
            $htmlBody .= "<div style='color: #374151;'>No template assigned to this endpoint</div>";
        }
        
        $htmlBody .= "
            </div>
        </div>";
        
        $notificationColor = match ($healthStatus) {
            'healthy' => 'success',
            'warning' => 'warning',
            default => 'danger'
        };
        
        Notification::make()
            ->title('🚀 Endpoint Test Results')
            ->body($htmlBody)
            ->$notificationColor()
            ->duration(10000)
            ->send();
    }

    public static function testEndpoint(ApiEndpoint $endpoint, array $testData): void
    {
        $healthStatus = 'unknown';
        $healthMessage = '';
        
        try {
            // Build the full URL
            $baseUrl = rtrim($endpoint->vendorApi->base_url, '/');
            $path = ltrim($endpoint->path, '/');
            
            // Replace path parameters first
            if (isset($testData['test_parameters'])) {
                foreach ($testData['test_parameters'] as $key => $value) {
                    $path = str_replace("{{$key}}", $value, $path);
                    $path = str_replace("{" . $key . "}", $value, $path);
                }
            }
            
            $fullUrl = $baseUrl . '/' . $path;

            // Initialize HTTP client with default headers
            $client = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->withOptions(['verify' => false]);

            // Add authentication if required using VendorApiAuthHelper
            if ($endpoint->requires_auth) {
                $authClient = VendorApiAuthHelper::authenticate($endpoint->vendorApi, $baseUrl);
                if ($authClient) {
                    $client = $authClient->withHeaders([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ]);
                } else {
                    throw new \Exception('Authentication failed for vendor API: ' . $endpoint->vendorApi->api_name);
                }
            }

            // Add custom headers from test form
            if (isset($testData['test_headers']) && !empty($testData['test_headers'])) {
                $client = $client->withHeaders($testData['test_headers']);
            }

            // Set timeout
            $client = $client->timeout(30);

            // Prepare request body for POST/PUT/PATCH
            $requestBody = [];
            if (in_array($endpoint->method, ['POST', 'PUT', 'PATCH']) && isset($testData['test_body'])) {
                $requestBody = json_decode($testData['test_body'] ?: '{}', true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Invalid JSON in request body: ' . json_last_error_msg());
                }
            }

            // Make the request based on method
            $response = match ($endpoint->method) {
                'GET' => $client->get($fullUrl),
                'POST' => $client->post($fullUrl, $requestBody),
                'PUT' => $client->put($fullUrl, $requestBody),
                'PATCH' => $client->patch($fullUrl, $requestBody),
                'DELETE' => $client->delete($fullUrl),
                default => throw new \Exception('Unsupported HTTP method: ' . $endpoint->method),
            };

            $statusCode = $response->status();
            $responseData = $response->json();

            // Validate against JSON template if exists
            $templateValidation = null;
            if ($endpoint->jsonTemplate) {
                $templateValidation = self::validateAgainstTemplate($responseData, $endpoint->jsonTemplate);
            }

            // Determine health status based on response and validation
            if ($response->successful()) {
                if ($templateValidation && !$templateValidation['valid']) {
                    $healthStatus = 'warning';
                    $healthMessage = 'Request successful but response doesn\'t match template';
                } else {
                    $healthStatus = 'healthy';
                    $healthMessage = 'Endpoint is working correctly' . ($templateValidation ? ' and response matches template' : '');
                }
            } else {
                $healthStatus = 'unhealthy';
                $healthMessage = "HTTP {$statusCode}: " . ($response->json('message') ?? 'Request failed');
            }

            // Update endpoint health status
            $endpoint->update([
                'health_status' => $healthStatus,
                'last_tested_at' => now(),
                'health_message' => $healthMessage,
            ]);

            // Log the test
            Log::info('API Endpoint Test', [
                'endpoint' => $endpoint->name,
                'url' => $fullUrl,
                'method' => $endpoint->method,
                'status_code' => $statusCode,
                'health_status' => $healthStatus,
                'response_size' => strlen($response->body()),
                'has_template' => !is_null($endpoint->jsonTemplate),
            ]);

            // Send formatted notification
            self::createSuccessNotification($endpoint, $response, $healthStatus, $healthMessage, $templateValidation, $fullUrl);

        } catch (\Exception $e) {
            // Update health status to unhealthy
            $healthStatus = 'unhealthy';
            $healthMessage = 'Test failed: ' . $e->getMessage();
            
            $endpoint->update([
                'health_status' => $healthStatus,
                'last_tested_at' => now(),
                'health_message' => $healthMessage,
            ]);

            // Log the error
            Log::error('API Endpoint Test Failed', [
                'endpoint' => $endpoint->name,
                'error' => $e->getMessage(),
                'health_status' => $healthStatus,
                'trace' => $e->getTraceAsString(),
            ]);

            // Create formatted error notification
            $errorHtml = "
            <div style='font-family: ui-monospace, SFMono-Regular, Consolas, monospace; font-size: 13px; line-height: 1.6;'>
                <div style='background: #fef2f2; padding: 12px; border-radius: 8px; border-left: 4px solid #ef4444;'>
                    <div style='font-weight: bold; color: #7f1d1d; margin-bottom: 8px;'>🚨 Test Execution Failed</div>
                    <div style='color: #991b1b; margin-bottom: 8px;'>
                        <strong>Endpoint:</strong> <code style='background: #fee2e2; padding: 2px 6px; border-radius: 4px;'>{$endpoint->method} {$endpoint->path}</code>
                    </div>
                    <div style='color: #991b1b; margin-bottom: 8px;'>
                        <strong>Error:</strong> {$e->getMessage()}
                    </div>
                    <div style='background: #ef4444; color: white; padding: 6px 10px; border-radius: 6px; font-weight: bold; display: inline-block;'>
                        ❌ UNHEALTHY
                    </div>
                </div>
                <div style='margin-top: 8px; color: #6b7280; font-style: italic;'>
                    Check application logs for detailed error information.
                </div>
            </div>";

            Notification::make()
                ->title('💥 Endpoint Test Failed')
                ->body($errorHtml)
                ->danger()
                ->duration(10000)
                ->send();
        }
    }

    private static function validateAgainstTemplate(?array $responseData, JsonTemplate $template): array
    {
        try {
            if (!$responseData) {
                return [
                    'valid' => false,
                    'errors' => ['Response is not valid JSON or empty']
                ];
            }

            // Check if template_data is already an array or a JSON string
            if (is_array($template->template_data)) {
                $templateData = $template->template_data;
            } else {
                $templateData = json_decode($template->template_data, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return [
                        'valid' => false,
                        'errors' => ['Invalid template JSON format: ' . json_last_error_msg()]
                    ];
                }
            }

            // Debug logging
            Log::info('Template Validation Debug', [
                'template_name' => $template->name,
                'template_data_type' => gettype($template->template_data),
                'template_data' => $templateData,
                'response_data' => $responseData,
            ]);

            $errors = [];
            self::validateStructure($responseData, $templateData, '', $errors);

            Log::info('Template Validation Result', [
                'template_name' => $template->name,
                'errors_count' => count($errors),
                'errors' => $errors,
            ]);

            return [
                'valid' => empty($errors),
                'errors' => $errors
            ];

        } catch (\Exception $e) {
            Log::error('Template Validation Exception', [
                'template_name' => $template->name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return [
                'valid' => false,
                'errors' => ['Template validation error: ' . $e->getMessage()]
            ];
        }
    }

    private static function validateStructure($response, $template, string $path, array &$errors): void
    {
        foreach ($template as $key => $value) {
            $currentPath = $path ? "{$path}.{$key}" : $key;
            
            // Check if key exists in response
            if (!array_key_exists($key, $response)) {
                $errors[] = "Missing key: {$currentPath}";
                continue;
            }

            $responseValue = $response[$key];
            
            // Type validation
            if (is_array($value)) {
                if (!is_array($responseValue)) {
                    $errors[] = "Type mismatch at {$currentPath}: expected array, got " . gettype($responseValue);
                    continue;
                }
                
                // If template array is empty, just check if response is array (already done above)
                if (empty($value)) {
                    continue;
                }
                
                // If template array has specific structure (object-like array)
                if (self::isAssociativeArray($value)) {
                    // Validate as object structure
                    self::validateStructure($responseValue, $value, $currentPath, $errors);
                } else {
                    // It's a sequential array with sample structure
                    $templateFirstElement = reset($value);
                    
                    // If template has sample structure, validate each response item against it
                    if (is_array($templateFirstElement)) {
                        foreach ($responseValue as $index => $responseItem) {
                            if (is_array($responseItem)) {
                                self::validateStructure($responseItem, $templateFirstElement, "{$currentPath}[{$index}]", $errors);
                            } else {
                                $errors[] = "Type mismatch at {$currentPath}[{$index}]: expected object, got " . gettype($responseItem);
                            }
                        }
                    } else {
                        // Template array contains primitive values, check types
                        foreach ($responseValue as $index => $responseItem) {
                            // Skip template variables
                            if (is_string($templateFirstElement) && preg_match('/^\{\{.*\}\}$/', $templateFirstElement)) {
                                continue;
                            }
                            
                            if (gettype($responseItem) !== gettype($templateFirstElement)) {
                                $errors[] = "Type mismatch at {$currentPath}[{$index}]: expected " . gettype($templateFirstElement) . ", got " . gettype($responseItem);
                            }
                        }
                    }
                }
            } else {
                // Skip template variables ({{variable_name}})
                if (is_string($value) && preg_match('/^\{\{.*\}\}$/', $value)) {
                    continue;
                }
                
                // Type validation for primitive values
                if (is_string($value)) {
                    if (!is_string($responseValue)) {
                        $errors[] = "Type mismatch at {$currentPath}: expected string, got " . gettype($responseValue);
                    }
                } elseif (is_int($value)) {
                    if (!is_int($responseValue)) {
                        $errors[] = "Type mismatch at {$currentPath}: expected integer, got " . gettype($responseValue);
                    }
                } elseif (is_float($value)) {
                    if (!is_float($responseValue) && !is_int($responseValue)) {
                        $errors[] = "Type mismatch at {$currentPath}: expected number, got " . gettype($responseValue);
                    }
                } elseif (is_bool($value)) {
                    if (!is_bool($responseValue)) {
                        $errors[] = "Type mismatch at {$currentPath}: expected boolean, got " . gettype($responseValue);
                    }
                } elseif (is_null($value)) {
                    if (!is_null($responseValue)) {
                        $errors[] = "Type mismatch at {$currentPath}: expected null, got " . gettype($responseValue);
                    }
                }
            }
        }
        
        // Optional: Check for missing structure in response (strict validation)
        // This checks if response has all the structure defined in template
        if (is_array($response) && self::isAssociativeArray($template)) {
            foreach ($template as $key => $value) {
                $currentPath = $path ? "{$path}.{$key}" : $key;
                
                if (is_array($value) && self::isAssociativeArray($value) && 
                    array_key_exists($key, $response) && is_array($response[$key])) {
                    
                    // Check if response object has the required nested structure
                    if (self::isAssociativeArray($response[$key])) {
                        // Continue validation for nested objects
                        continue;
                    }
                }
            }
        }
    }
    
    /**
     * Check if array is associative (object-like) or sequential
     */
    private static function isAssociativeArray(array $array): bool
    {
        if (empty($array)) {
            return false;
        }
        return array_keys($array) !== range(0, count($array) - 1);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Endpoint Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('vendorApi.api_name')
                            ->label('Vendor API'),
                        Infolists\Components\TextEntry::make('name')
                            ->label('Endpoint Name'),
                        Infolists\Components\TextEntry::make('path')
                            ->label('Endpoint Path')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('method')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'GET' => 'primary',
                                'POST' => 'success',
                                'PUT' => 'warning',
                                'PATCH' => 'info',
                                'DELETE' => 'danger',
                            })
                            ->label('HTTP Method'),
                        Infolists\Components\TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'inactive' => 'warning',
                                'deprecated' => 'danger',
                            })
                            ->label('Status'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Health Status')
                    ->schema([
                        Infolists\Components\TextEntry::make('health_status')
                            ->label('Health Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'healthy' => 'success',
                                'unhealthy' => 'danger',
                                'warning' => 'warning',
                                'unknown' => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'healthy' => 'Healthy',
                                'unhealthy' => 'Unhealthy',
                                'warning' => 'Warning',
                                'unknown' => 'Unknown',
                            }),
                        Infolists\Components\TextEntry::make('last_tested_at')
                            ->label('Last Tested')
                            ->dateTime()
                            ->placeholder('Never tested'),
                        Infolists\Components\TextEntry::make('health_message')
                            ->label('Health Message')
                            ->columnSpanFull()
                            ->placeholder('No health information available'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('JSON Template Configuration')
                    ->schema([
                        Infolists\Components\TextEntry::make('jsonTemplate.name')
                            ->label('Template Name')
                            ->default('No template assigned'),
                        Infolists\Components\TextEntry::make('jsonTemplate.version')
                            ->label('Template Version')
                            ->badge()
                            ->color('info')
                            ->visible(fn ($record) => $record->jsonTemplate),
                        Infolists\Components\TextEntry::make('jsonTemplate.description')
                            ->label('Template Description')
                            ->visible(fn ($record) => $record->jsonTemplate && $record->jsonTemplate->description),
                        Infolists\Components\TextEntry::make('jsonTemplate.template_data')
                            ->label('Template Structure')
                            ->columnSpanFull()
                            ->formatStateUsing(fn (string $state): string => json_encode(json_decode($state), JSON_PRETTY_PRINT))
                            ->extraAttributes(['style' => 'font-family: monospace; background: #f8f9fa; padding: 1rem; border-radius: 0.375rem; white-space: pre-wrap;'])
                            ->visible(fn ($record) => $record->jsonTemplate),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Parameters')
                    ->schema([
                        Infolists\Components\KeyValueEntry::make('parameters')
                            ->label('Request Parameters')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->visible(fn ($record) => !empty($record->parameters)),

                Infolists\Components\Section::make('Security')
                    ->schema([
                        Infolists\Components\IconEntry::make('requires_auth')
                            ->boolean()
                            ->label('Requires Authentication'),
                    ])
                    ->columns(1),

                Infolists\Components\Section::make('Timestamps')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime()
                            ->label('Created At'),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->dateTime()
                            ->label('Updated At'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApiEndpoints::route('/'),
            'create' => Pages\CreateApiEndpoint::route('/create'),
            // 'view' => Pages\ViewApiEndpoint::route('/{record}'),
            'edit' => Pages\EditApiEndpoint::route('/{record}/edit'),
        ];
    }
}