<?php

namespace App\Filament\Pages;

use App\Models\CompanySetting;
use Filament\Actions\Action;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class CompanySettings extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static string|\UnitEnum|null $navigationGroup = 'Contenido';

    protected static ?string $navigationLabel = 'Datos de la empresa';

    protected static ?string $title = 'Datos de la empresa';

    protected static ?int $navigationSort = 20;

    protected function getHeaderActions(): array
    {
        return [Action::make('edit')->label('Editar datos')->record(fn () => CompanySetting::current())->form([
            TextInput::make('name')->label('Nombre comercial')->maxLength(255), TextInput::make('legal_name')->label('Razón social')->maxLength(255), TextInput::make('ruc')->label('RUC')->maxLength(11),
            Textarea::make('description')->label('Descripción')->rows(4), TextInput::make('phone')->label('Teléfono'), TextInput::make('whatsapp_number')->label('WhatsApp')->helperText('Número con código de país, ej. 51942123456'),
            TextInput::make('email')->label('Correo')->email(), TextInput::make('claims_email')->label('Correo de reclamos')->email(), TextInput::make('address')->label('Dirección'), TextInput::make('opening_hours')->label('Horario'), TextInput::make('map_embed_url')->label('Mapa (URL)')->url(),
            TextInput::make('youtube_url')->label('YouTube')->url(), TextInput::make('facebook_url')->label('Facebook')->url(), TextInput::make('linkedin_url')->label('LinkedIn')->url(), TextInput::make('instagram_url')->label('Instagram')->url(),
            SpatieMediaLibraryFileUpload::make('logo')->label('Logo')->collection('logo')->disk('public')->image(),
        ])->fillForm(fn () => CompanySetting::current()->attributesToArray())->action(function (array $data): void {
            CompanySetting::current()->fill($data)->save();
            Notification::make()->title('Datos guardados')->success()->send();
        })];
    }
}
