<?php

namespace App\Filament\Resources\ContactMessages;

use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactMessageView extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([Section::make('Mensaje')->schema([TextEntry::make('name')->label('Nombre'), TextEntry::make('email')->label('Correo'), TextEntry::make('phone')->label('Teléfono'), TextEntry::make('subject')->label('Asunto'), TextEntry::make('message')->label('Mensaje')->columnSpanFull(), TextEntry::make('created_at')->label('Recibido')->dateTime(), TextEntry::make('read_at')->label('Leído')->dateTime()])->columns(2)]);
    }
}
