<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\OrderStatusEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Schema;
use App\Models\Product;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        $updateTotal = function ($get, $set) {
            $items = $get('../../items') ?? [];
            $total = 0;
            foreach ($items as $item) {
                $total += (float) ($item['subtotal'] ?? 0);
            }
            $set('../../total', $total);
        };

        return $schema
            ->components([
                Select::make('customer_id')
                    ->relationship('customer', 'full_name', fn ($query) => $query->where('is_active', true))
                    ->label('Cliente')
                    ->searchable()
                    ->required(),
                DateTimePicker::make('order_date')
                    ->label('Fecha del Pedido')
                    ->default(now())
                    ->required(),
                Select::make('status')
                    ->label('Estado')
                    ->options(OrderStatusEnum::class)
                    ->default('PENDING')
                    ->required(),
                TextInput::make('total')
                    ->label('Total (Bs.)')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->readOnly(),
                Repeater::make('items')
                    ->relationship()
                    ->label('Productos del Pedido')
                    ->minItems(1)
                    ->schema([
                        Select::make('product_id')
                            ->relationship('product', 'name')
                            ->label('Producto')
                            ->required()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get) use ($updateTotal) {
                                $price = Product::find($state)?->price ?? 0;
                                $set('unit_price', $price);
                                $set('subtotal', $price * (int) $get('quantity'));
                                $updateTotal($get, $set);
                            }),
                        TextInput::make('quantity')
                            ->label('Cantidad')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->required()
                            ->rule(function ($get) {
                                return function (string $attribute, $value, \Closure $fail) use ($get) {
                                    $productId = $get('product_id');
                                    if (!$productId) return;
                                    $product = Product::find($productId);
                                    if ($product && $product->stock < $value) {
                                        $fail("Stock insuficiente. Solo quedan {$product->stock} unidades disponibles.");
                                    }
                                };
                            })
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get) use ($updateTotal) {
                                $set('subtotal', (int) $state * (float) $get('unit_price'));
                                $updateTotal($get, $set);
                            }),
                        TextInput::make('unit_price')
                            ->label('Precio Unitario')
                            ->numeric()
                            ->required()
                            ->readOnly(),
                        TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->numeric()
                            ->required()
                            ->readOnly(),
                    ])
                    ->live()
                    ->afterStateUpdated(function ($get, $set) {
                        $items = $get('items') ?? [];
                        $total = 0;
                        foreach ($items as $item) {
                            $total += (float) ($item['subtotal'] ?? 0);
                        }
                        $set('total', $total);
                    })
                    ->deleteAction(
                        fn ($action) => $action->after(function ($get, $set) {
                            $items = $get('items') ?? [];
                            $total = 0;
                            foreach ($items as $item) {
                                $total += (float) ($item['subtotal'] ?? 0);
                            }
                            $set('total', $total);
                        })
                    )
                    ->columnSpanFull()
                    ->columns(4),
            ]);
    }
}
