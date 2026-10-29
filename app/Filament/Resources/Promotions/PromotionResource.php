<?php

namespace App\Filament\Resources\Promotions;

use App\Models\Promotion;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class PromotionResource extends Resource
{
    protected static ?string $model = Promotion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static string|\UnitEnum|null $navigationGroup = 'Contenido';

    protected static ?string $modelLabel = 'promoción';

    protected static ?string $pluralModelLabel = 'promociones';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('title')->label('Título')->required()->maxLength(255), Select::make('product_id')->label('Producto relacionado')->relationship('product', 'name')->searchable()->preload(), TextInput::make('link_url')->label('Enlace')->url(), TextInput::make('sort_order')->label('Orden')->numeric()->default(0), Toggle::make('is_active')->label('Activa')->default(true), SpatieMediaLibraryFileUpload::make('image')->label('Imagen')->collection('image')->disk('public')->image()->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([SpatieMediaLibraryImageColumn::make('image')->label('Imagen')->collection('image'), TextColumn::make('title')->label('Título')->searchable(), TextColumn::make('product.name')->label('Producto'), TextColumn::make('sort_order')->label('Orden')->sortable(), ToggleColumn::make('is_active')->label('Activa')])->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => PromotionList::route('/'), 'create' => PromotionCreate::route('/create'), 'edit' => PromotionEdit::route('/{record}/edit')];
    }
}
class PromotionList extends ListRecords
{
    protected static string $resource = PromotionResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
class PromotionCreate extends CreateRecord
{
    protected static string $resource = PromotionResource::class;
}
class PromotionEdit extends EditRecord
{
    protected static string $resource = PromotionResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
