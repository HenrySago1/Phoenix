<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre del Producto')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Descripción')
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->default(null),
                TextInput::make('price')
                    ->label('Precio (Bs.)')
                    ->required()
                    ->numeric()
                    ->gt(0)
                    ->default(0.00),
                TextInput::make('stock')
                    ->label('Stock Disponible')
                    ->required()
                    ->numeric()
                    ->gte(0)
                    ->default(0),
                Toggle::make('is_active')
                    ->label('¿Activo?')
                    ->required()
                    ->default(true),
            ]);
    }
}
