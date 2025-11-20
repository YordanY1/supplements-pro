<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // Заглавие
            TextInput::make('title')
                ->label('Заглавие')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(
                    fn($state, callable $set) =>
                    $set('slug', Str::slug($state))
                ),

            // Slug
            TextInput::make('slug')
                ->label('URL Slug')
                ->required()
                ->unique(ignoreRecord: true),

            // Автор
            TextInput::make('author')
                ->label('Автор')
                ->required()
                ->default(fn() => auth()->user()->name ?? 'Администратор'),

            FileUpload::make('image')
                ->label('Основна снимка')
                ->disk('public')
                ->directory('blog')
                ->image()
                ->imageEditor()
                ->preserveFilenames(false)
                ->getUploadedFileNameForStorageUsing(function ($file) {
                    return Str::slug(
                        pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)
                    ) . '.' . $file->getClientOriginalExtension();
                })
                ->maxSize(2048),


            // Категории
            Select::make('categories')
                ->label('Категории')
                ->multiple()
                ->options([
                    'zdrave-i-krasota'     => 'Здраве и красота',
                    'polezni-savet'        => 'Полезни съвети',
                    'polezno'              => 'Полезно',
                    'za-zhenite'           => 'За жените',
                    'hrana-i-dobavki'      => 'Храна и добавки',
                ])
                ->searchable(),


            TextInput::make('tags')
                ->label('Тагове')
                ->placeholder('пример: билки, здраве, детокс')
                ->helperText('Разделяй със запетайки')
                ->afterStateHydrated(
                    fn($state, callable $set) =>
                    is_array($state)
                        ? $set('tags', implode(', ', $state))
                        : null
                )
                ->dehydrateStateUsing(
                    fn($state) =>
                    collect(explode(',', $state))
                        ->map(fn($t) => trim($t))
                        ->filter()
                        ->values()
                        ->toArray()
                ),


            Textarea::make('excerpt')
                ->label('Кратко описание')
                ->rows(3)
                ->maxLength(500),

            RichEditor::make('content')
                ->label('Съдържание')
                ->required()
                ->columnSpanFull(),
        ]);
    }
}
