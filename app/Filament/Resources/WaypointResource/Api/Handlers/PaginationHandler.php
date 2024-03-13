<?php
namespace App\Filament\Resources\WaypointResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filament\Resources\WaypointResource;

class PaginationHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = WaypointResource::class;

    // Which fields can be selected from the database through the query string
    public static array $allowedFields = [
        'name'
    ];

    // Which fields can be used to sort the results through the query string
    public static array $allowedSorts = [
        'name',
        'created_at'
    ];

    // Which fields can be used to filter the results through the query string
    public static array $allowedFilters = [
        'name',
    ];

    public function handler()
    {
        $model = static::getEloquentQuery();

        $query = QueryBuilder::for( $model )
                             ->allowedFields( self::$allowedFields ?? [] )
                             ->allowedSorts( self::$allowedSorts ?? [] )
                             ->allowedFilters( self::$allowedFilters ?? [] )
                             ->where( 'user_id', auth()->user()->id )
                             ->paginate( request()->query( 'per_page' ) )
                             ->appends( request()->query() );

        return static::getApiTransformer()::collection($query);
    }
}
