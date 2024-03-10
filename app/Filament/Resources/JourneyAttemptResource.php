<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JourneyAttemptResource\Pages;
use App\Filament\Resources\JourneyAttemptResource\RelationManagers;
use App\Models\JourneyAttempt;
use App\Models\User;
use App\Models\Waypoint;
use App\Services\JourneyRouteCalculatorService;
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

class JourneyAttemptResource extends Resource {
    protected static ?string $model = JourneyAttempt::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 2;


    public static function form( Form $form ): Form {
        return $form
            ->schema( [
                TextInput::make( 'name' )
                         ->required()
                         ->maxLength( 255 ),
                Select::make( 'start_waypoint_id' )
                      ->label( 'Start Waypoint' )
                      ->options( Waypoint::all()->pluck( 'name', 'id' ) )
                      ->default( 'start_waypoint_id' )
                      ->nullable()
                      ->visibleOn( 'edit' )
                      ->searchable(),
            ] );
    }

    public static function table( Table $table ): Table {
        return $table
            ->columns( [
                Tables\Columns\TextColumn::make( 'name' )->searchable(),
                Tables\Columns\TextColumn::make( 'user.name' )->searchable()->sortable(),
                Tables\Columns\TextColumn::make( 'startWaypoint.name' )
                                         ->label( 'Start Waypoint' )
                                         ->searchable()
                                         ->wrap()
                                         ->sortable(),
                Tables\Columns\IconColumn::make( 'calculated' )->boolean(),
                Tables\Columns\TextColumn::make( 'nearest_route' )
                                         ->disabledClick()
                                         ->wrap()
                                         ->label( 'Sorted Waypoints' )
                                         ->formatStateUsing( function ( string $state, JourneyAttempt $journeyAttempt ): string {
                                             if ( empty( $journeyAttempt->nearest_route ) ) {
                                                 return '';
                                             }

                                             $googleMapsInfo = ( new JourneyRouteCalculatorService() )->generateGoogleMapsLink( $journeyAttempt->nearest_route );

                                             return $googleMapsInfo['text'] . $googleMapsInfo['link'];
                                         } )
                                         ->html(),
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
            RelationManagers\WaypointsRelationManager::class,
        ];
    }

    public static function getPages(): array {
        return [
            'index'  => Pages\ListJourneyAttempts::route( '/' ),
            'create' => Pages\CreateJourneyAttempt::route( '/create' ),
            'edit'   => Pages\EditJourneyAttempt::route( '/{record}/edit' ),
        ];
    }
}
