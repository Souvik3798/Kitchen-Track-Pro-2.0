<?php

namespace App\Filament\Admin\Resources\OrganizationResource\Pages;

use App\Filament\Admin\Resources\OrganizationResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListOrganizations extends ListRecords
{
    protected static string $resource = OrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'today' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->whereDate('created_at', Carbon::today())),
            'this_week' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])),
            'this_month' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)),
            'this_year' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->whereYear('created_at', Carbon::now()->year)),
        ];
    }
}
