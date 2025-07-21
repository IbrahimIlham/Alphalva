<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\RelationManagers\AddressRelationManager;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;
use Illuminate\Support\Collection;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 5; //berguna untuk mengurutkan menu navigasi

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('Order Information')
                            ->schema([
                                Select::make('user_id')
                                    ->required()
                                    ->preload()
                                    ->searchable()
                                    ->relationship('user', 'name'),

                                Select::make('payment_method')
                                    ->options([
                                        'midtrans' => 'Midtrans',
                                    ])
                                    ->required(),

                                Select::make('payment_status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'paid' => 'Paid',
                                        'failed' => 'Failed',
                                    ])
                                    ->required()
                                    ->default('pending'),

                                ToggleButtons::make('status')
                                    ->options([
                                        'new' => 'New',
                                        'processing' => 'Processing',
                                        'shipped' => 'Shipped',
                                        'delivered' => 'Deliverd',
                                        'canceled' => 'Cancelled',
                                    ])
                                    ->colors([
                                        'new' => 'info',
                                        'processing' => 'warning',
                                        'shipped' => 'success',
                                        'deliverd' => 'success',
                                        'cancelled' => 'danger',
                                    ])
                                    ->icons([
                                        'new' => 'heroicon-m-sparkles',
                                        'processing' => 'heroicon-m-arrow-path',
                                        'shipped' => 'heroicon-o-truck',
                                        'deliverd' => 'heroicon-m-check-badge',
                                        'cancelled' => 'heroicon-o-x-circle',
                                    ])
                                    ->required()
                                    ->default('new')
                                    ->inline(),

                                Select::make('currency')
                                    ->options([
                                        'idr' => 'Indonesian Rupiah (Rp)',
                                        'usd' => 'United States Dollar (USD)',
                                        'eur' => 'Euro (EUR)',

                                    ])
                                    ->required()
                                    ->default('idr'),

                                Select::make('shipping_method')
                                    ->options([
                                        'jne' => 'JNE',
                                        'pos' => 'POS Indonesia',
                                        'tiki' => 'TIKI',
                                    ]),

                                TextInput::make('shipping_amount')
                                    ->label('Shipping Cost')
                                    ->numeric()
                                    ->prefix('IDR')
                                    ->disabled()
                                    ->dehydrated(),

                                Placeholder::make('total_with_shipping')
                                    ->label('Total with Shipping')
                                    ->content(function (Get $get) {
                                        $grandTotal = $get('grand_total') ?? 0;
                                        $shippingAmount = $get('shipping_amount') ?? 0;
                                        return Number::currency($grandTotal + $shippingAmount, 'IDR');
                                    }),

                                Textarea::make('notes')
                                    ->columnSpanFull()
                            ])->columns(2),

                        Section::make('Order Items')
                            ->schema([
                                Repeater::make('items') // dari model order (fn items)
                                    ->relationship()
                                    ->schema([
                                        Select::make('product_id')
                                            ->required()
                                            ->relationship('product', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->distinct()
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems() //mencegah select yang sama
                                            ->reactive()
                                            ->afterStateUpdated(fn($state, Set $set) => $set('unit_amount', Product::find($state)?->price ?? 0))
                                            ->afterStateUpdated(fn($state, Set $set) => $set('total_amount', Product::find($state)?->price ?? 0))
                                            ->columnSpan(4),


                                        TextInput::make('quantity')
                                            ->required()
                                            ->minValue(1)
                                            ->numeric()
                                            ->default(1)
                                            ->reactive()
                                            ->afterStateUpdated(fn($state, Set $set, Get $get) => $set('total_amount', $state * $get('unit_amount')))
                                            ->columnSpan(2),

                                        TextInput::make('unit_amount')
                                            ->required()
                                            ->numeric()
                                            ->disabled()
                                            ->dehydrated()
                                            ->columnSpan(3),

                                        TextInput::make('total_amount')
                                            ->required()
                                            ->numeric()
                                            ->dehydrated()
                                            ->columnSpan(3),
                                    ])->columns(12),

                                Placeholder::make('grand_total_placeholder')
                                    ->label('Grand Total')
                                    ->content(function (Get $get, Set $set) {
                                        $total = 0;
                                        // jika tidak ada repeater, return 0
                                        if (!$repeaters = $get('items')) {
                                            return $total;
                                        }

                                        // jika ada repeater
                                        foreach ($repeaters as $item) {
                                            $total += $item['total_amount'];
                                        }

                                        $set('grand_total', $total);
                                        return Number::currency($total, 'IDR');
                                    }),

                                Hidden::make('grand_total')
                                    ->required()
                                    ->default(0)
                            ])
                    ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('grand_total')
                    ->label('Grand Total')
                    ->sortable()
                    ->money('IDR')
                    ->formatStateUsing(function ($state, $record) {
                        // Tampilkan grand_total + shipping_amount
                        return \Illuminate\Support\Number::currency(($record->grand_total ?? 0) + ($record->shipping_amount ?? 0), 'IDR');
                    }),

                TextColumn::make('payment_method')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('payment_status')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('currency')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('shipping_method')
                    ->label('Shipping Method')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function ($state) {
                        return strtoupper($state ?? 'N/A');
                    }),

                TextColumn::make('shipping_amount')
                    ->label('Shipping Cost')
                    ->sortable()
                    ->money('IDR')
                    ->formatStateUsing(function ($state) {
                        return $state ? Number::currency($state, 'IDR') : 'IDR 0';
                    }),

                SelectColumn::make('status')
                    ->options([
                        'new' => 'New',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'canceled' => 'cancelled',
                    ])
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), //mengaktifkan kolom toggle, dan menonaktifkan kolom default

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), //mengaktifkan kolom toggle, dan menonaktifkan kolom default


            ])
            ->filters([
                Tables\Filters\SelectFilter::make('shipping_method')
                    ->options([
                        'jne' => 'JNE',
                        'pos' => 'POS Indonesia',
                        'tiki' => 'TIKI',
                    ]),
                Tables\Filters\Filter::make('has_shipping')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('shipping_amount')->where('shipping_amount', '>', 0))
                    ->label('Has Shipping Cost'),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('recalculate_shipping')
                        ->label('Recalculate Shipping')
                        ->icon('heroicon-o-truck')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Recalculate Shipping Cost')
                        ->modalDescription('This will recalculate shipping cost using RajaOngkir API for this order.')
                        ->action(function (Order $record) {
                            // Recalculate shipping cost
                            $address = $record->address;
                            if (!$address) {
                                return;
                            }

                            // Get city ID from address
                            $cityName = $address->city;
                            $provinceName = $address->province;

                            // Find city ID from RajaOngkir
                            $provinces = \App\Helpers\RajaOngkirHelper::getProvinces();
                            $province = collect($provinces)->firstWhere('province', $provinceName);
                            
                            if ($province) {
                                $cities = \App\Helpers\RajaOngkirHelper::getCities($province['province_id']);
                                $city = collect($cities)->firstWhere('city_name', $cityName);
                                
                                if ($city && $record->shipping_method) {
                                    $weight = 1000; // 1 kg default
                                    $origin = 153; // Jakarta Selatan
                                    $newShippingCost = \App\Helpers\RajaOngkirHelper::getCost(
                                        $origin, 
                                        $city['city_id'], 
                                        $weight, 
                                        $record->shipping_method
                                    );
                                    
                                    if ($newShippingCost > 0) {
                                        $record->update(['shipping_amount' => $newShippingCost]);
                                        \Filament\Notifications\Notification::make()
                                            ->title('Shipping cost updated')
                                            ->body('New shipping cost: IDR ' . number_format($newShippingCost, 0, ',', '.'))
                                            ->success()
                                            ->send();
                                    } else {
                                        \Filament\Notifications\Notification::make()
                                            ->title('Shipping service not available')
                                            ->body('The selected shipping method is not available for this destination.')
                                            ->warning()
                                            ->send();
                                    }
                                }
                            }
                        }),
                    DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                BulkAction::make('recalculate_shipping_bulk')
                    ->label('Recalculate Shipping (Bulk)')
                    ->icon('heroicon-o-truck')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Recalculate Shipping Cost for Selected Orders')
                    ->modalDescription('This will recalculate shipping cost using RajaOngkir API for all selected orders.')
                    ->action(function (Collection $records) {
                        $updatedCount = 0;
                        $failedCount = 0;

                        foreach ($records as $record) {
                            $address = $record->address;
                            if (!$address || !$record->shipping_method) {
                                $failedCount++;
                                continue;
                            }

                            // Get city ID from address
                            $cityName = $address->city;
                            $provinceName = $address->province;

                            // Find city ID from RajaOngkir
                            $provinces = \App\Helpers\RajaOngkirHelper::getProvinces();
                            $province = collect($provinces)->firstWhere('province', $provinceName);
                            
                            if ($province) {
                                $cities = \App\Helpers\RajaOngkirHelper::getCities($province['province_id']);
                                $city = collect($cities)->firstWhere('city_name', $cityName);
                                
                                if ($city) {
                                    $weight = 1000; // 1 kg default
                                    $origin = 153; // Jakarta Selatan
                                    $newShippingCost = \App\Helpers\RajaOngkirHelper::getCost(
                                        $origin, 
                                        $city['city_id'], 
                                        $weight, 
                                        $record->shipping_method
                                    );
                                    
                                    if ($newShippingCost > 0) {
                                        $record->update(['shipping_amount' => $newShippingCost]);
                                        $updatedCount++;
                                    } else {
                                        $failedCount++;
                                    }
                                } else {
                                    $failedCount++;
                                }
                            } else {
                                $failedCount++;
                            }
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Bulk shipping recalculation completed')
                            ->body("Updated: {$updatedCount} orders, Failed: {$failedCount} orders")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AddressRelationManager::class
        ];
    }

    public static function getNavigationBadge(): ?string {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null {
        return static::getModel()::count() > 10 ? 'success' : 'danger';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
