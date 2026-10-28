<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Enums\OrderStatus;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('code')->label('Código')->searchable()->sortable(),
            TextColumn::make('customer_name')->label('Cliente')->state(fn ($record) => $record->customerName())->searchable(['first_name', 'last_name', 'business_name']),
            TextColumn::make('phone')->label('Teléfono')->searchable(),
            TextColumn::make('total')->label('Total')->money('PEN')->sortable(),
            TextColumn::make('delivery_method')->label('Entrega')->badge(),
            TextColumn::make('payment_preference')->label('Pago preferido')->badge(),
            TextColumn::make('status')->label('Estado')->badge(),
            TextColumn::make('created_at')->label('Fecha')->dateTime('d/m/Y H:i')->sortable(),
        ])->filters([SelectFilter::make('status')->label('Estado')->options(collect(OrderStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->getLabel()]))])->recordActions([ViewAction::make()])->defaultSort('created_at', 'desc');
    }
}
