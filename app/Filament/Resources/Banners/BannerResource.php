<?php

namespace App\Filament\Resources\Banners;

use App\Enums\BannerPlacement;
use App\Models\Banner;
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

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|\UnitEnum|null $navigationGroup = 'Contenido';

    protected static ?string $modelLabel = 'banner';

    protected static ?string $pluralModelLabel = 'banners';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([Select::make('placement')->label('Ubicación')->options(collect(BannerPlacement::cases())->mapWithKeys(fn ($v) => [$v->value => $v->getLabel()]))->required(), TextInput::make('title')->label('Título')->maxLength(255), TextInput::make('link_url')->label('Enlace')->url(), TextInput::make('sort_order')->label('Orden')->numeric()->default(0), Toggle::make('is_active')->label('Activo')->default(true), SpatieMediaLibraryFileUpload::make('image')->label('Imagen')->collection('image')->disk('public')->image()->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([SpatieMediaLibraryImageColumn::make('image')->label('Imagen')->collection('image'), TextColumn::make('title')->label('Título')->searchable(), TextColumn::make('placement')->label('Ubicación')->badge(), TextColumn::make('sort_order')->label('Orden')->sortable(), ToggleColumn::make('is_active')->label('Activo')])->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => BannerList::route('/'), 'create' => BannerCreate::route('/create'), 'edit' => BannerEdit::route('/{record}/edit')];
    }
}
class BannerList extends ListRecords
{
    protected static string $resource = BannerResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
class BannerCreate extends CreateRecord
{
    protected static string $resource = BannerResource::class;
}
class BannerEdit extends EditRecord
{
    protected static string $resource = BannerResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
