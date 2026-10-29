<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Información del producto')->schema([
                Select::make('category_id')->label('Categoría')->relationship('category', 'name')->searchable()->preload()->required(),
                Select::make('brand_id')->label('Marca')->relationship('brand', 'name')->searchable()->preload(),
                TextInput::make('name')->label('Nombre')->required()->maxLength(255)->live(onBlur: true)->afterStateUpdated(fn ($state, callable $set, callable $get) => blank($get('slug')) ? $set('slug', Str::slug($state)) : null),
                TextInput::make('slug')->label('URL')->required()->unique(ignoreRecord: true)->maxLength(255),
                TextInput::make('summary')->label('Resumen')->maxLength(500),
                RichEditor::make('description')->label('Descripción')->columnSpanFull(),
                RichEditor::make('information')->label('Información técnica')->columnSpanFull(),
            ])->columns(2),
            Section::make('Precio e inventario')->schema([
                TextInput::make('price')->label('Precio')->numeric()->prefix('S/')->required()->minValue(0),
                TextInput::make('regular_price')->label('Precio anterior')->numeric()->prefix('S/')->minValue(0)->helperText('Precio anterior opcional. Si es mayor que el precio, el producto se muestra como oferta.'),
                TextInput::make('stock')->label('Stock')->numeric()->integer()->required()->minValue(0),
                TextInput::make('unit')->label('Unidad')->required()->maxLength(40),
                Toggle::make('is_active')->label('Activo')->default(true),
                Toggle::make('is_featured')->label('Destacado'),
                SpatieMediaLibraryFileUpload::make('images')->label('Imágenes')->collection('images')->disk('public')->multiple()->reorderable()->image()->imageEditor()->columnSpanFull(),
            ])->columns(2),
        ]);
    }
}
