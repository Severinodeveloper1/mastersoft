<?php

namespace App\Filament\Resources\LegalPages;

use App\Models\LegalPage;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LegalPageResource extends Resource
{
    protected static ?string $model = LegalPage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    protected static string|\UnitEnum|null $navigationGroup = 'Contenido';

    protected static ?string $modelLabel = 'página legal';

    protected static ?string $pluralModelLabel = 'páginas legales';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('title')->label('Título')->required()->maxLength(255), TextInput::make('slug')->label('URL')->disabled()->dehydrated(false), RichEditor::make('content')->label('Contenido')->required()->columnSpanFull()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('title')->label('Título')->searchable(), TextColumn::make('slug')->label('URL')->copyable()])->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => LegalPageList::route('/'), 'edit' => LegalPageEdit::route('/{record}/edit')];
    }
}
class LegalPageList extends ListRecords
{
    protected static string $resource = LegalPageResource::class;
}
class LegalPageEdit extends EditRecord
{
    protected static string $resource = LegalPageResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
