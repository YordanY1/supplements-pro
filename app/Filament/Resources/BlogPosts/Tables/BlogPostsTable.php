<?php

namespace App\Filament\Resources\BlogPosts\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class BlogPostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                ImageColumn::make('image')
                    ->label('Снимка')
                    ->circular()
                    ->size(50),

                TextColumn::make('title')
                    ->label('Заглавие')
                    ->searchable()
                    ->limit(40)
                    ->sortable(),

                TextColumn::make('author')
                    ->label('Автор')
                    ->searchable()
                    ->sortable(),

                BadgeColumn::make('categories')
                    ->label('Категории')
                    ->colors([
                        'primary',
                    ])
                    ->formatStateUsing(fn($state) => is_array($state) ? implode(', ', $state) : $state)
                    ->limit(40),

                IconColumn::make('published')
                    ->label('Публикувана')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Дата')
                    ->date('d.m.Y')
                    ->sortable(),
            ])

            ->recordActions([
                EditAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
