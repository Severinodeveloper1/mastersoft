<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Cliente')->schema([TextEntry::make('customerName')->label('Cliente')->state(fn ($record) => $record->customerName()), TextEntry::make('phone')->label('Teléfono'), TextEntry::make('email')->label('Correo'), TextEntry::make('document_number')->label('DNI'), TextEntry::make('ruc')->label('RUC'), TextEntry::make('business_name')->label('Razón social')])->columns(3),
            Section::make('Entrega')->schema([TextEntry::make('delivery_method')->label('Método'), TextEntry::make('department')->label('Departamento'), TextEntry::make('province')->label('Provincia'), TextEntry::make('district')->label('Distrito'), TextEntry::make('address')->label('Dirección'), TextEntry::make('reference')->label('Referencia')])->columns(3),
            Section::make('Pago y notas')->schema([TextEntry::make('payment_preference')->label('Pago preferido'), TextEntry::make('notes')->label('Notas')->columnSpanFull()])->columns(2),
            Section::make('Productos')->schema([RepeatableEntry::make('items')->schema([TextEntry::make('product_name')->label('Producto'), TextEntry::make('unit_price')->label('Precio unitario')->money('PEN'), TextEntry::make('quantity')->label('Cantidad'), TextEntry::make('line_total')->label('Total')->money('PEN')])->columns(4)]),
            Section::make('Totales')->schema([TextEntry::make('subtotal')->label('Subtotal')->money('PEN'), TextEntry::make('tax')->label('IGV')->money('PEN'), TextEntry::make('total')->label('Total')->money('PEN')])->columns(3),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [Action::make('editStatus')->label('Cambiar estado')->form([Select::make('status')->label('Estado')->options(collect(OrderStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()]))->required()])->fillForm(fn () => ['status' => $this->record->status->value])->action(fn (array $data) => $this->record->update($data))];
    }
}
