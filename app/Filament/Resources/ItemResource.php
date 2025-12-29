<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Item;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Directory;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ItemResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ItemResource\RelationManagers;
use App\Filament\Actions\PasswordProtectedDeleteAction;
use App\Filament\Actions\PasswordProtectedDeleteBulkAction;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 3;
    // protected static ?string $navigationGroup = 'Database';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make()
                    // ->description('Prevent abuse by limiting the number of requests per period')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\RichEditor::make('info')
                            ->columnSpan('full'),

                        Forms\Components\Textarea::make('short_description')
                            ->rows(2)
                            ->columnSpan('full'),

                        Forms\Components\Select::make('categories')
                            ->multiple()
                            ->preload()
                            ->relationship(
                                name: 'categories',
                                titleAttribute: 'name',

                            )
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                                Forms\Components\Hidden::make('team_id')
                                    ->default(null),
                            ]),

                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->prefix(' RM')
                            ->required()
                            ->visible(fn($get) => $get('activate_product_variations') == false),

                        Forms\Components\TextInput::make('cost_price')
                            ->numeric()
                            ->prefix(' RM')
                            ->required()
                            ->visible(fn($get) => $get('activate_product_variations') == false),

                        Forms\Components\TextInput::make('weight')
                            ->numeric()
                            ->suffix(' kg')
                            ->required()
                            ->visible(fn($get) => $get('activate_product_variations') == false),

                        Forms\Components\TextInput::make('order_limit')
                            ->numeric()
                            ->nullable(),

                        Forms\Components\TextInput::make('current_stock_balance')
                            ->numeric()
                            ->default(0),

                        Forms\Components\Toggle::make('activate_ecommerce')
                            ->label('Activate in E-Commerce?')
                            ->default(true),

                        Forms\Components\Toggle::make('activate_stock_management')
                            ->label('Activate Stock Management?')
                            ->default(false),

                        Forms\Components\Toggle::make('activate_product_variations')
                            ->label('Activate Product Variations?')
                            ->live()
                            ->default(false),

                        Forms\Components\Select::make('directory')
                            ->options(function () {
                                return Directory::with('subcategories')->get()->mapWithKeys(function ($directory) {
                                    return [
                                        $directory->name => $directory->subcategories->pluck('name', 'id')->toArray(),
                                    ];
                                })->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Repeater::make('variations')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('name')->required(),
                                Forms\Components\TextInput::make('cost_price')
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TextInput::make('price')
                                    ->numeric()
                                    ->required(),



                                Forms\Components\TextInput::make('weight')
                                    ->numeric()
                                    ->suffix(' kg')
                                    ->required(),

                                Forms\Components\TextInput::make('order_limit')
                                    ->numeric()
                                    ->nullable(),

                                Forms\Components\TextInput::make('current_stock_balance')
                                    ->numeric()
                                    ->default(0),

                            ])
                            ->visible(fn($get) => $get('activate_product_variations') == true)
                            ->columns(6)
                            ->columnSpanFull(),


                        Forms\Components\FileUpload::make('product_image')
                            ->multiple()
                            ->downloadable()
                            ->directory('product_image')
                            ->acceptedFileTypes(['image/*'])
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('attachment')
                            ->multiple()
                            ->downloadable()
                            ->directory('products')
                            ->acceptedFileTypes([
                                'image/*',          // Accept all image formats (jpg, png, gif, webp, etc.)
                                'application/pdf',  // PDF documents
                                'application/msword', // DOC
                                'application/vnd.openxmlformats-officedocument.*', // DOCX, XLSX, PPTX
                                'text/*',           // Plain text, CSV, JSON, XML, HTML, CSS
                                'audio/*',          // All audio formats (mp3, wav, ogg, etc.)
                                'video/*',          // All video formats (mp4, webm, mkv, etc.)
                                'application/zip',  // ZIP files
                                'application/x-rar-compressed', // RAR files
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label('No.')
                    ->rowIndex(),
                Tables\Columns\ImageColumn::make('product_image')
                    ->label('Product Images'),
                Tables\Columns\TextColumn::make('product_information')
                    ->label('Product Information')
                    ->state(function ($record) {
                        $html = $record->name . "<br>";
                        $html .= "<span style='font-size:12px;'>" . Str::limit($record->short_description, 20, '...') . "</span>";
                        return $html;
                    })
                    ->html()
                    ->wrap(),

                Tables\Columns\TextColumn::make('stock_balance')
                    ->state(function ($record) {
                        $html = '';
                        if ($record->activate_stock_management) {
                            if ($record->activate_product_variations) {
                                $html = "<table style='border: 1px solid #eceeef; background-color: #f5f5f5;'>";
                                foreach ($record->variations as $k => $v) {
                                    $html .= "<tr><td style='border: 1px solid #eceeef;text-align: left;padding: 2px;'>" . $v->name . ":</td><td style='border: 1px solid #eceeef;text-align: right;padding: 2px;'>" . $v->current_stock_balance . "</td></tr>";
                                }
                                $html .= "</table>";
                            } else {
                                $html = $record->current_stock_balance;
                            }
                        } else {
                            $html = '-';
                        }
                        return $html;
                    })
                    ->html()
                    ->wrap(),

                Tables\Columns\TextColumn::make('price_info')
                    ->state(function ($record) {
                        $html = '';

                        if ($record->activate_product_variations) {
                            $html = "<table style='border: 1px solid #eceeef; background-color: #f5f5f5;'>";
                            foreach ($record->variations as $k => $v) {
                                $html .= "<tr><td style='border: 1px solid #eceeef;text-align: left;padding: 2px;'>" . $v->name . ":</td><td style='border: 1px solid #eceeef;text-align: right;padding: 2px;'>" . $v->price . "</td></tr>";
                            }
                            $html .= "</table>";
                        } else {
                            $html = $record->price;
                        }

                        return $html;
                    })
                    ->html()
                    ->wrap(),





                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('info')
                    ->limit(50)
                    ->wrap()
                    ->html()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('short_description')
                    ->limit(50)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('price')
                    ->money('MYR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('cost_price')
                    ->money('MYR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('weight')
                    ->suffix(' kg')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('order_limit')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('current_stock_balance')
                    ->sortable()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color(fn($state) => $state > 10 ? 'success' : ($state > 0 ? 'warning' : 'danger')),

                Tables\Columns\ToggleColumn::make('activate_ecommerce')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('E-Commerce'),

                Tables\Columns\ToggleColumn::make('activate_stock_management')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Stock Mgmt'),

                Tables\Columns\ToggleColumn::make('activate_product_variations')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Product Variations'),

                Tables\Columns\TextColumn::make('directory')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->limit(30),



                Tables\Columns\TextColumn::make('attachment')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Download File')
                    ->state(function ($record) {
                        $html = '';
                        foreach ($record->attachment as $k => $v) {
                            $html .= '<a href="' . asset('storage/' . $v) . '" download class="text-blue-500 underline">Download</a>';
                        }
                        return $html;
                    })
                    ->html(),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                PasswordProtectedDeleteAction::make(),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
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
