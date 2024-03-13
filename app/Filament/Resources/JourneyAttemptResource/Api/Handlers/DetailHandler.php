<?php

namespace App\Filament\Resources\JourneyAttemptResource\Api\Handlers;

use Illuminate\Http\Request;
use App\Filament\Resources\JourneyAttemptResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = JourneyAttemptResource::class;


    public function handler(Request $request)
    {
        $id = $request->route('id');

        $model = static::getEloquentQuery();

        $query = QueryBuilder::for(
            $model->where( 'user_id', auth()->user()->id )->where(static::getKeyName(), $id)
        )
            ->first();

        if (!$query) return static::sendNotFoundResponse();

        $transformer = static::getApiTransformer();

        return new $transformer($query);
    }
}
