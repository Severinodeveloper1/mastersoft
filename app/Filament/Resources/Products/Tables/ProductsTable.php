<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            SpatieMediaLibraryImageColumn::make('images')->label('Imagen')->collection('images')->size(48)->square(),
            TextColumn::make('name')->label('Nombre')->searchable()->sortable(),
            TextColumn::make('category.name')->label('Categoría')->sortable(),
            TextColumn::make('brand.name')->label('Marca')->sortable(),
            TextColumn::make('price')->label('Precio')->money('PEN')->sortable(),
            TextColumn::make('regular_price')->label('Precio anterior')->money('PEN')->placeholder('—')->sortable(),
            TextColumn::make('stock')->label('Stock')->sortable(),
            ToggleColumn::make('is_active')->label('Activo'),
            ToggleColumn::make('is_featured')->label('Destacado'),
        ])->filters([
            SelectFilter::make('category')->label('Categoría')->relationship('category', 'name'),
            SelectFilter::make('brand')->label('Marca')->relationship('brand', 'name'),
            Filter::make('active')->label('Activos')->query(fn (Builder $query) => $query->where('is_active', true)),
            Filter::make('sale')->label('En oferta')->query(fn (Builder $query) => $query->whereNotNull('regular_price')->whereColumn('regular_price', '>', 'price')),
        ])->recordActions([EditAction::make()])->defaultSort('name');
    }
}
