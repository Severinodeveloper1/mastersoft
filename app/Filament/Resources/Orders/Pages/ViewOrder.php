<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Services\Erp\QuotationSender;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
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
            Section::make('ERP')->schema([TextEntry::make('erp_quotation_number')->label('Cotización')->placeholder('No enviada'), TextEntry::make('erp_sent_at')->label('Enviada el')->dateTime('d/m/Y H:i')->placeholder('—'), TextEntry::make('erp_error')->label('Último error')->placeholder('—')->columnSpanFull()])->columns(2),
            Section::make('Entrega')->schema([TextEntry::make('delivery_method')->label('Método'), TextEntry::make('department')->label('Departamento'), TextEntry::make('province')->label('Provincia'), TextEntry::make('district')->label('Distrito'), TextEntry::make('address')->label('Dirección'), TextEntry::make('reference')->label('Referencia')])->columns(3),
            Section::make('Pago y notas')->schema([TextEntry::make('payment_preference')->label('Pago preferido'), TextEntry::make('notes')->label('Notas')->columnSpanFull()])->columns(2),
            Section::make('Productos')->schema([RepeatableEntry::make('items')->schema([TextEntry::make('product_name')->label('Producto'), TextEntry::make('unit_price')->label('Precio unitario')->money('PEN'), TextEntry::make('quantity')->label('Cantidad'), TextEntry::make('line_total')->label('Total')->money('PEN')])->columns(4)]),
            Section::make('Totales')->schema([TextEntry::make('subtotal')->label('Subtotal')->money('PEN'), TextEntry::make('tax')->label('IGV')->money('PEN'), TextEntry::make('total')->label('Total')->money('PEN')])->columns(3),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sendToErp')
                ->label('Enviar al ERP')
                ->icon('heroicon-o-paper-airplane')
                ->color('gray')
                ->requiresConfirmation()
                ->modalDescription('Se registrará este pedido como cotización en el ERP. Si el envío anterior falló por tiempo de espera, revisa primero en el ERP que la cotización no se haya creado, para no duplicarla.')
                ->visible(fn (): bool => $this->record->erp_quotation_id === null)
                ->authorize('update')
                ->action(fn (QuotationSender $sender) => $this->sendToErp($sender)),
            Action::make('editStatus')->label('Cambiar estado')->form([Select::make('status')->label('Estado')->options(collect(OrderStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()]))->required()])->fillForm(fn () => ['status' => $this->record->status->value])->action(fn (array $data) => $this->record->update($data)),
        ];
    }

    private function sendToErp(QuotationSender $sender): void
    {
        $sent = $sender->send($this->record);
        $this->record->refresh();

        if (! $sent) {
            Notification::make()
                ->title('No se pudo enviar el pedido al ERP')
                ->body('El pedido sigue pendiente. Detalle: '.($this->record->erp_error ?? 'otro envío de este pedido está en curso.'))
                ->danger()
                ->persistent()
                ->send();

            return;
        }

        Notification::make()
            ->title('Pedido enviado al ERP')
            ->body("Cotización {$this->record->erp_quotation_number}.")
            ->success()
            ->send();
    }
}
