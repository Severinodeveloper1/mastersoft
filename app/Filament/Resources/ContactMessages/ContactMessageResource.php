<?php

namespace App\Filament\Resources\ContactMessages;

use App\Models\ContactMessage;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static string|\UnitEnum|null $navigationGroup = 'Ventas';

    protected static ?string $modelLabel = 'mensaje';

    protected static ?string $pluralModelLabel = 'mensajes de contacto';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->label('Nombre')->searchable(), TextColumn::make('email')->label('Correo')->searchable(), TextColumn::make('phone')->label('Teléfono'), TextColumn::make('subject')->label('Asunto'), TextColumn::make('created_at')->label('Fecha')->dateTime('d/m/Y H:i')->sortable(), TextColumn::make('read_at')->label('Leído')->dateTime('d/m/Y H:i')->placeholder('Pendiente')])->recordActions([ViewAction::make(), Action::make('markRead')->label('Marcar como leído')->icon(Heroicon::OutlinedCheck)->visible(fn ($record) => blank($record->read_at))->action(fn ($record) => $record->update(['read_at' => now()]))]);
    }

    public static function getPages(): array
    {
        return ['index' => ContactMessageList::route('/'), 'view' => ContactMessageView::route('/{record}')];
    }
}
