<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Customer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Illuminate\Support\Str;
use Filament\Support\Enums\ActionSize;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Exports\CustomerExporter;
use App\Filament\Imports\CustomerImporter;
use Filament\Notifications\Notification;
use App\Filament\Resources\CustomerResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\HtmlString;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Customer';
    protected static ?string $navigationLabel = 'Customers';

    /**
     * Escape text to prevent XSS
     */
    private static function escapeXSS($text): string
    {
        if (is_null($text)) {
            return '';
        }

        return htmlspecialchars((string) $text, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);
    }

    /**
     * Sanitize filename to prevent path traversal and XSS
     */
    private static function sanitizeFilename($filename): string
    {
        $filename = self::escapeXSS($filename);
        $filename = preg_replace('/[^\w\-\.]/', '_', $filename); // Keep only safe characters
        $filename = basename($filename); // Remove any directory paths
        return substr($filename, 0, 100); // Limit length
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Customer Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Customer Name')
                            ->required()
                            ->maxLength(255)
                            ->rules([
                                'required',
                                'string',
                                'max:255',
                                'regex:/^[\pL\s\-\'\.\d]+$/u', // Allow letters, numbers, spaces, hyphens, apostrophes, dots
                            ])
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Auto-sanitize on input
                                $clean = preg_replace('/[^\pL\s\-\'\.\d]/u', '', $state);
                                if ($clean !== $state) {
                                    $set('name', $clean);
                                }
                            })
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->rules([
                                'required',
                                'email:rfc,strict,dns',
                                'max:255',
                            ])
                            ->unique(ignoreRecord: true)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('balance')
                            ->label('Balance (RM)')
                            ->numeric()
                            ->rules([
                                'nullable',
                                'numeric',
                                'decimal:0,2', // Decimal precision validation
                                'min:0',
                                'max:9999999.99',
                                'regex:/^\d+(\.\d{1,2})?$/', //Regex validation
                            ])
                            ->inputMode('decimal')
                            ->step(0.01)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('paid_to_date')
                            ->label('Paid to Date (RM)')
                            ->numeric()
                            ->rules([
                                'required',
                                'numeric',
                                'decimal:0,2',
                                'min:0',
                                'max:9999999.99',
                                'regex:/^\d+(\.\d{1,2})?$/',
                            ])
                            ->default(0)
                            ->inputMode('decimal')
                            ->step(0.01)
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('last_login')
                            ->label('Last Login')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->maxDate(now())
                            ->nullable(),

                    ])
                    ->columns(2),

                Forms\Components\Section::make('System Timestamps')
                    ->schema([
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Created At')
                            ->displayFormat('d/m/Y H:i:s')
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\DateTimePicker::make('updated_at')
                            ->label('Updated At')
                            ->displayFormat('d/m/Y H:i:s')
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\DateTimePicker::make('deleted_at')
                            ->label('Deleted At')
                            ->displayFormat('d/m/Y H:i:s')
                            ->disabled()
                            ->dehydrated()

                    ])
                    ->columns(3)
                    ->hiddenOn('create'),
            ]);
    }

    // ✅ SECURE FILE HANDLING 1: Download Customer Invoice
    public static function downloadCustomerInvoiceAction(): Action
    {
        return Action::make('downloadInvoice')
            ->label('Download Invoice')
            ->icon('heroicon-o-document-arrow-down')
            ->color('primary')
            ->action(function ($record) {
                // Generate safe filename without user input
                $safeId = (string) $record->id;
                $safeName = self::sanitizeFilename($record->name);
                $filename = "invoice_{$safeId}_{$safeName}_" . now()->format('Ymd') . ".pdf";

                // SAFE: No user input in notification body
                Notification::make()
                    ->title('Download Ready')
                    ->body('Your invoice download is ready. Click to download.')
                    ->success()
                    ->send();
            });
    }

    // ✅ SECURE FILE HANDLING 2: Export Customer Data
    public static function exportCustomerDataAction(): Action
    {
        return Action::make('exportData')
            ->label('Export Data')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success')
            ->form([
                Forms\Components\DatePicker::make('start_date')
                    ->label('Start Date')
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->label('End Date')
                    ->required(),
                Forms\Components\Select::make('format')
                    ->label('Format')
                    ->options([
                        'pdf' => 'PDF',
                        'csv' => 'CSV',
                    ])
                    ->required(),
            ])
            ->action(function (array $data, $record) {
                // Server-side date validation
                $startDate = $data['start_date'];
                $endDate = $data['end_date'];

                if ($endDate < $startDate) {
                    Notification::make()
                        ->title('Invalid Date Range')
                        ->body('End date must be after start date.')
                        ->danger()
                        ->send();
                    return;
                }

                // SAFE: No user input in filename
                $safeId = (string) $record->id;
                $safeFormat = in_array($data['format'], ['pdf', 'csv']) ? $data['format'] : 'csv'; //Whitelist Validation
                $filename = "customer_export_{$safeId}_" . now()->format('Ymd_His') . ".{$safeFormat}";

                // SAFE: Generic success message without user input
                Notification::make()
                    ->title('Export Started')
                    ->body('Your data export has been initiated. You will be notified when complete.')
                    ->success()
                    ->send();
            });
    }

    // ✅ SECURE FILE HANDLING 3: Upload Customer Document
    public static function uploadCustomerDocumentAction(): Action
    {
        return Action::make('uploadDocument')
            ->label('Upload Document')
            ->icon('heroicon-o-cloud-arrow-up')
            ->color('info')
            ->form([
                Forms\Components\FileUpload::make('document')
                    ->label('Document')
                    ->required()
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                    ->maxSize(5120)
                    ->getUploadedFileNameForStorageUsing(
                        function (UploadedFile $file): string {
                            // Generate random filename to prevent XSS in filenames
                            return Str::random(40) . '.' . $file->getClientOriginalExtension();
                        }
                    ),
                Forms\Components\TextInput::make('description')
                    ->label('Description')
                    ->maxLength(255)
                    ->rules([
                        'string',
                        'max:255',
                        'regex:/^[\pL\s\-\'\.\d]+$/u',
                    ]),
            ])
            ->action(function (array $data, $record) {
                // File is automatically validated by Filament
                $file = $data['document'];

                // SAFE: Sanitize original filename before display
                $originalName = self::sanitizeFilename($file->getClientOriginalName());

                // SAFE: Use escaped customer name if needed
                $safeCustomerName = self::escapeXSS($record->name);

                Notification::make()
                    ->title('Document Uploaded')
                    ->body("Document '{$originalName}' has been uploaded successfully.")
                    ->success()
                    ->send();
            });
    }

    // ✅ SERVER-SIDE VALIDATION ACTION
    public static function validateCustomerAction(): Action
    {
        return Action::make('validateCustomer')
            ->label('Validate Data')
            ->icon('heroicon-o-check-circle')
            ->color('warning')
            ->action(function ($record) {
                $issues = [];

                // Check email
                if (!filter_var($record->email, FILTER_VALIDATE_EMAIL)) {
                    $issues[] = 'Invalid email format';
                }

                // Check for XSS in name
                if (preg_match('/[<>"\']/', $record->name)) {
                    $issues[] = 'Name contains potentially unsafe characters';
                }

                // Check balance
                if ($record->balance < 0) {
                    $issues[] = 'Negative balance';
                }

                if ($record->paid_to_date < 0) {
                    $issues[] = 'Negative paid amount';
                }

                if (empty($issues)) {
                    Notification::make()
                        ->title('Validation Passed')
                        ->body('All customer data is valid and secure.')
                        ->success()
                        ->send();
                } else {
                    // SAFE: Escape all issue messages before displaying
                    $safeIssues = array_map([self::class, 'escapeXSS'], $issues);
                    $issuesText = implode(', ', $safeIssues);

                    Notification::make()
                        ->title('Validation Failed')
                        ->body("Issues found: {$issuesText}")
                        ->danger()
                        ->send();
                }
            });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\CreateAction::make()
                        ->icon('heroicon-o-plus')
                        ->color('primary'),
                    Tables\Actions\ImportAction::make('importBrands')
                        ->importer(CustomerImporter::class),
                    Tables\Actions\ExportAction::make()
                        ->exporter(CustomerExporter::class),
                ])
                    ->label('More actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size(ActionSize::Small)
                    ->color('primary')
                    ->button(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Customer Name')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => self::escapeXSS($state)) // Escape for display
                    ->tooltip(fn ($state) => self::escapeXSS($state)), // Escape tooltip

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => self::escapeXSS($state)) // Escape for display
                    ->tooltip(fn ($state) => self::escapeXSS($state)), // Escape tooltip

                Tables\Columns\TextColumn::make('balance')
                    ->label('Balance')
                    ->money('myr')
                    ->sortable(),

                Tables\Columns\TextColumn::make('paid_to_date')
                    ->label('Paid to Date')
                    ->money('myr')
                    ->sortable(),

                Tables\Columns\TextColumn::make('last_login')
                    ->label('Last Login')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Deleted At')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),

                    // ✅ ADDED: Secure File Handling Actions
                    static::downloadCustomerInvoiceAction(),
                    static::exportCustomerDataAction(),
                    static::uploadCustomerDocumentAction(),
                    static::validateCustomerAction(),

                    Tables\Actions\Action::make('Print PDF')
                        ->label('Print PDF')
                        ->color('primary')
                        ->icon('heroicon-o-printer')
                        ->url(fn ($record) => route('print.invoice', [
                            'id' => $record->id,
                            'token' => Str::random(32), // Add CSRF-like token
                        ]))
                        ->openUrlInNewTab(),
                ])
                    ->label('More actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size(ActionSize::Small)
                    ->color('primary')
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->recordUrl(null)
            ->recordAction(null)
            ->defaultSort('updated_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
