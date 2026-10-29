<?php

namespace App\Filament\Pages;

use App\Models\AboutPage as AboutContent;
use Filament\Actions\Action;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class AboutPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedInformationCircle;

    protected static string|\UnitEnum|null $navigationGroup = 'Contenido';

    protected static ?string $navigationLabel = 'Nosotros';

    protected static ?string $title = 'Nosotros';

    protected function getHeaderActions(): array
    {
        return [Action::make('edit')->label('Editar contenido')->record(fn () => AboutContent::current())->form([
            TextInput::make('kicker')->label('Antetítulo'), TextInput::make('title')->label('Título')->required(), Textarea::make('description')->label('Descripción')->rows(5), TextInput::make('years_of_experience')->label('Años de experiencia')->numeric(), TextInput::make('phone')->label('Teléfono'), Textarea::make('mission')->label('Misión')->rows(4), Textarea::make('vision')->label('Visión')->rows(4), Textarea::make('values')->label('Valores')->rows(4), SpatieMediaLibraryFileUpload::make('image')->label('Imagen')->collection('image')->disk('public')->image(),
        ])->fillForm(fn () => AboutContent::current()->attributesToArray())->action(function (array $data): void {
            AboutContent::current()->fill($data)->save();
            Notification::make()->title('Contenido guardado')->success()->send();
        })];
    }
}
