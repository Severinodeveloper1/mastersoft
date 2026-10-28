<?php

namespace App\Filament\Resources\Claims\Tables;

use App\Enums\ClaimStatus;
use App\Enums\ClaimType;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClaimsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('code')->label('Código')->searchable()->sortable(),
            TextColumn::make('full_name')->label('Consumidor')->state(fn ($record) => $record->fullName())->searchable(['first_name', 'paternal_surname', 'maternal_surname']),
            TextColumn::make('claim_type')->label('Tipo')->badge(),
            TextColumn::make('good_type')->label('Bien contratado')->badge(),
            TextColumn::make('status')->label('Estado')->badge(),
            TextColumn::make('created_at')->label('Fecha')->dateTime('d/m/Y')->sortable(),
            TextColumn::make('response_due_on')->label('Fecha límite')->date('d/m/Y')->sortable(),
            TextColumn::make('business_days_remaining')->label('Días hábiles restantes')->state(fn ($record) => self::remaining($record))->color(fn ($record) => self::overdue($record) ? 'danger' : 'gray'),
        ])->filters([
            SelectFilter::make('status')->label('Estado')->options(collect(ClaimStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()])),
            SelectFilter::make('claim_type')->label('Tipo')->options(collect(ClaimType::cases())->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()])),
            Filter::make('overdue')->label('Vencidos')->query(fn (Builder $query) => $query->where('status', '!=', ClaimStatus::Answered->value)->whereDate('response_due_on', '<', today())),
        ])->recordActions([ViewAction::make(), EditAction::make()])->defaultSort('created_at', 'desc');
    }

    private static function remaining($record): string|int
    {
        if (self::overdue($record)) {
            return 'Vencido';
        }
        if ($record->status === ClaimStatus::Answered || ! $record->response_due_on) {
            return '—';
        }
        $date = today();
        $due = $record->response_due_on->copy()->startOfDay();
        $days = 0;
        while ($date->lt($due)) {
            $date->addDay();
            if (! $date->isWeekend()) {
                $days++;
            }
        }

        return $days;
    }

    private static function overdue($record): bool
    {
        return $record->status !== ClaimStatus::Answered && $record->response_due_on && $record->response_due_on->lt(today());
    }
}
