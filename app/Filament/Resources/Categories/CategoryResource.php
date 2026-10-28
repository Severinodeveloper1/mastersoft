<?php

namespace App\Filament\Resources\Categories;

use App\Models\Category;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|\UnitEnum|null $navigationGroup = 'Catálogo';

    protected static ?string $navigationLabel = 'Categorías';

    protected static ?string $modelLabel = 'categoría';

    protected static ?string $pluralModelLabel = 'categorías';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->label('Nombre')->required()->maxLength(255)->live(onBlur: true)->afterStateUpdated(fn ($state, callable $set, callable $get) => blank($get('slug')) ? $set('slug', Str::slug($state)) : null), TextInput::make('slug')->label('URL')->required()->unique(ignoreRecord: true), TextInput::make('sort_order')->label('Orden')->numeric()->default(0), Toggle::make('is_active')->label('Activa')->default(true)]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->label('Nombre')->searchable()->sortable(), TextColumn::make('products_count')->label('Productos')->counts('products'), TextColumn::make('sort_order')->label('Orden')->sortable(), ToggleColumn::make('is_active')->label('Activa')])->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => CategoryList::route('/'), 'create' => CategoryCreate::route('/create'), 'edit' => CategoryEdit::route('/{record}/edit')];
    }
}
class CategoryList extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
class CategoryCreate extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
}
class CategoryEdit extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
