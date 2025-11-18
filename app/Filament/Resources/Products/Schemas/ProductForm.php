<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('source')
                    ->required(),
                TextInput::make('vendor_id')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug'),
                TextInput::make('brand_name'),
                TextInput::make('brand_slug'),
                TextInput::make('category'),
                TextInput::make('category_slug'),
                TextInput::make('price')
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('old_price')
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('stock')
                    ->numeric(),
                TextInput::make('weight')
                    ->numeric(),
                FileUpload::make('image')
                    ->image(),
                TextInput::make('images'),
                TextInput::make('label'),
                Textarea::make('short_description')
                    ->columnSpanFull(),
                Textarea::make('description_html')
                    ->columnSpanFull(),
                Textarea::make('supplement_facts_html')
                    ->columnSpanFull(),
                TextInput::make('sku')
                    ->label('SKU'),
                TextInput::make('upc'),
                TextInput::make('ean'),
                Textarea::make('raw')
                    ->columnSpanFull(),
            ]);
    }
}
