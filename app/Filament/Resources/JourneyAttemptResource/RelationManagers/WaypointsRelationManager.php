<?php

namespace App\Filament\Resources\JourneyAttemptResource\RelationManagers;

use App\Models\JourneyAttempt;
use App\Models\User;
use App\Models\Waypoint;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WaypointsRelationManager extends RelationManager
{
    protected static string $relationship = 'waypoints';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                         ->required()
                         ->columnSpanFull()
                         ->maxLength(255),
                Map::make('location')
                   ->label('Location')
                   ->columnSpanFull()
                   ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set, $old, ?array $state): void {
                       $set('latitude', $state['lat']);
                       $set('longitude', $state['lng']);
                   })
                   ->afterStateHydrated(function ($state, $record, Forms\Set $set): void {
                       $set('location', [ 'lat' => $record ? $record->latitude : '', 'lng' => $record ? $record->longitude : '' ]);
                   })
                   ->liveLocation()
                   ->showMarker()
                   ->markerColor("#22c55eff")
                   ->showFullscreenControl()
                   ->showZoomControl()
                   ->draggable()
                   ->tilesUrl("http://tile.openstreetmap.de/{z}/{x}/{y}.png")
                   ->zoom(15)
                   ->extraTileControl([])
                   ->extraControl([
                       'zoomDelta' => 1,
                       'zoomSnap'  => 2,
                   ]),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                                           ->mutateFormDataUsing(function (array $data): array {
                                               $data['user_id'] = auth()->id();

                                               $data['latitude']  = array_get($data, 'location.lat', 0);
                                               $data['longitude'] = array_get($data, 'location.lng', 0);

                                               return $data;
                                           }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                                         ->mutateFormDataUsing(function (array $data): array {
                                             $data['latitude']  = array_get($data, 'location.lat', 0);
                                             $data['longitude'] = array_get($data, 'location.lng', 0);

                                             return $data;
                                         }),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }
}
