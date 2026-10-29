<?php

namespace App\Filament\Resources\Brands;

use App\Models\Brand;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
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
use Illuminate\Support\Str;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static string|\UnitEnum|null $navigationGroup = 'Catálogo';

    protected static ?string $modelLabel = 'marca';

    protected static ?string $pluralModelLabel = 'marcas';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->label('Nombre')->required()->live(onBlur: true)->afterStateUpdated(fn ($state, callable $set, callable $get) => blank($get('slug')) ? $set('slug', Str::slug($state)) : null), TextInput::make('slug')->label('URL')->required()->unique(ignoreRecord: true), Toggle::make('is_active')->label('Activa')->default(true), SpatieMediaLibraryFileUpload::make('logo')->label('Logo')->collection('logo')->disk('public')->image()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([SpatieMediaLibraryImageColumn::make('logo')->label('Logo')->collection('logo'), TextColumn::make('name')->label('Nombre')->searchable()->sortable(), TextColumn::make('products_count')->label('Productos')->counts('products'), ToggleColumn::make('is_active')->label('Activa')])->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => BrandList::route('/'), 'create' => BrandCreate::route('/create'), 'edit' => BrandEdit::route('/{record}/edit')];
    }
}
class BrandList extends ListRecords
{
    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
class BrandCreate extends CreateRecord
{
    protected static string $resource = BrandResource::class;
}
class BrandEdit extends EditRecord
{
    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
