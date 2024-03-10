<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WaypointResource\Pages;
use App\Models\JourneyAttempt;
use App\Models\User;
use App\Models\Waypoint;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WaypointResource extends Resource {
    protected static ?string $model = Waypoint::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 3;

    public static function form( Form $form ): Form {
        return $form
            ->schema( [
                TextInput::make( 'name' )
                         ->required()
                         ->maxLength( 255 ),
                Select::make( 'journey_attempt_id' )
                      ->label( 'Journey Attempt' )
                      ->options( JourneyAttempt::all()->pluck( 'name', 'id' ) )
                      ->required()
                      ->searchable(),
                Map::make( 'location' )
                   ->label( 'Location' )
                   ->columnSpanFull()
                   ->afterStateUpdated( function ( Forms\Get $get, Forms\Set $set, ?string $old, ?array $state ): void {
                       $set( 'latitude', $state['lat'] );
                       $set( 'longitude', $state['lng'] );
                   } )
                   ->afterStateHydrated( function ( $state, $record, Forms\Set $set ): void {
                       $set( 'location', [ 'lat' => $record ? $record->latitude : '', 'lng' => $record ? $record->longitude : '' ] );
                   } )
                   ->liveLocation()
                   ->showMarker()
                   ->markerColor( "#22c55eff" )
                   ->showFullscreenControl()
                   ->showZoomControl()
                   ->draggable()
                   ->tilesUrl( "http://tile.openstreetmap.de/{z}/{x}/{y}.png" )
                   ->zoom( 15 )
                   ->extraTileControl( [] )
                   ->extraControl( [
                       'zoomDelta' => 1,
                       'zoomSnap'  => 2,
                   ] ),
            ] );
    }

    public static function table( Table $table ): Table {
        return $table
            ->columns( [
                Tables\Columns\TextColumn::make( 'journeyAttempt.name' )
                                         ->sortable(),
                Tables\Columns\TextColumn::make( 'name' )
                                         ->searchable(),
                Tables\Columns\TextColumn::make( 'latitude' )
                                         ->numeric()
                                         ->sortable(),
                Tables\Columns\TextColumn::make( 'longitude' )
                                         ->numeric()
                                         ->sortable(),
                Tables\Columns\TextColumn::make( 'deleted_at' )
                                         ->dateTime()
                                         ->sortable()
                                         ->toggleable( isToggledHiddenByDefault: true ),
                Tables\Columns\TextColumn::make( 'created_at' )
                                         ->dateTime()
                                         ->sortable()
                                         ->toggleable( isToggledHiddenByDefault: true ),
                Tables\Columns\TextColumn::make( 'updated_at' )
                                         ->dateTime()
                                         ->sortable()
                                         ->toggleable( isToggledHiddenByDefault: true ),
            ] )
            ->filters( [
                TrashedFilter::make(),
            ] )
            ->actions( [
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ] )
            ->bulkActions( [
                Tables\Actions\BulkActionGroup::make( [
                    Tables\Actions\DeleteBulkAction::make(),
                ] ),
            ] )
            ->modifyQueryUsing( fn( Builder $query ) => $query->withoutGlobalScopes( [
                SoftDeletingScope::class,
            ] ) );
    }

    public static function getRelations(): array {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder {
        if ( auth()->user()->hasRole( 'admin' ) ) {
            return parent::getEloquentQuery();
        }

        return parent::getEloquentQuery()->where( 'user_id', auth()->user()->id );
    }

    public static function getPages(): array {
        return [
            'index'  => Pages\ListWaypoints::route( '/' ),
            'create' => Pages\CreateWaypoint::route( '/create' ),
            'edit'   => Pages\EditWaypoint::route( '/{record}/edit' ),
        ];
    }
}
