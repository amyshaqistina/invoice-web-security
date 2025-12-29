<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Customer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\Enums\ActionSize;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Exports\CustomerExporter;
use App\Filament\Imports\CustomerImporter;
use App\Filament\Resources\CustomerResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Actions\PasswordProtectedDeleteBulkAction;
use App\Filament\Actions\PasswordProtectedExportAction;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 2;


    // protected static ?string $navigationLabel = 'Client';
    // protected static ?string $modelLabel = 'Client';
    // protected static ?string $pluralModelLabel = 'Client';
    protected static ?string $modelLabel = 'Customer';
    protected static ?string $navigationLabel = 'Customers';

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
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('balance')
                            ->label('Balance (RM)')
                            ->numeric()
                            ->nullable()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('paid_to_date')
                            ->label('Paid to Date (RM)')
                            ->numeric()
                            ->default(0)
                            ->columnSpan(1),

                            Forms\Components\DatePicker::make('last_login')
                            ->label('Last Login')
                            ->native(false)
                            ->displayFormat('d/m/Y')
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
                        ->exporter(CustomerExporter::class)
                        ->requiresConfirmation()
                        ->modalHeading('Export Customer Data')
                        ->modalDescription('This will export customer data. Continue?'),
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
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

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
                    Tables\Actions\Action::make('Print PDF')
                        ->label('Print PDF')
                        ->color('primary')
                        ->icon('heroicon-o-printer')
                        ->url(fn ($record) => route('print.invoice', $record->id))
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
                    PasswordProtectedDeleteBulkAction::make(),
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
