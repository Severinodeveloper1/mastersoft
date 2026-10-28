<?php

namespace App\Filament\Resources\Claims\Pages;

use App\Filament\Resources\Claims\ClaimResource;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewClaim extends ViewRecord
{
    protected static string $resource = ClaimResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Consumidor')->schema([
                TextEntry::make('document_type')->label('Tipo de documento'), TextEntry::make('document_number')->label('Número de documento'),
                TextEntry::make('first_name')->label('Nombres'), TextEntry::make('paternal_surname')->label('Apellido paterno'), TextEntry::make('maternal_surname')->label('Apellido materno'),
                TextEntry::make('phone')->label('Teléfono'), TextEntry::make('email')->label('Correo'), TextEntry::make('department')->label('Departamento'), TextEntry::make('province')->label('Provincia'), TextEntry::make('district')->label('Distrito'), TextEntry::make('address')->label('Dirección'),
                TextEntry::make('is_minor')->label('Menor de edad')->boolean(), TextEntry::make('guardian_name')->label('Nombre del apoderado'), TextEntry::make('guardian_document')->label('Documento del apoderado'),
            ])->columns(3),
            Section::make('Bien contratado')->schema([TextEntry::make('good_type')->label('Tipo'), TextEntry::make('order_reference')->label('Número de pedido'), TextEntry::make('claimed_amount')->label('Monto')->money('PEN'), TextEntry::make('good_description')->label('Descripción')->columnSpanFull()])->columns(3),
            Section::make('Reclamo o queja')->schema([TextEntry::make('claim_type')->label('Tipo'), TextEntry::make('detail')->label('Detalle')->columnSpanFull(), TextEntry::make('consumer_request')->label('Pedido del consumidor')->columnSpanFull(), TextEntry::make('privacy_accepted_at')->label('Consentimiento')->dateTime(), TextEntry::make('email_notification_accepted')->label('Notificación por correo')->boolean()])->columns(2),
            Section::make('Atención')->schema([TextEntry::make('status')->label('Estado'), TextEntry::make('provider_response')->label('Respuesta del proveedor')->columnSpanFull(), TextEntry::make('responded_at')->label('Fecha de respuesta')->dateTime(), TextEntry::make('response_due_on')->label('Fecha límite')->date()])->columns(2),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
