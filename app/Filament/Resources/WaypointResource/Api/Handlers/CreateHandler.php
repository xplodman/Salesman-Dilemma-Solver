<?php

namespace App\Filament\Resources\WaypointResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\WaypointResource;
use Illuminate\Support\Facades\Validator;

class CreateHandler extends Handlers
{
    public static string | null $uri = '/';
    public static string | null $resource = WaypointResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel()
    {
        return static::$resource::getModel();
    }

    public function handler(Request $request)
    {
        $currentUserId = auth()->user()->id;
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'journey_attempt_id' => 'required|exists:journey_attempts,id,user_id,' . $currentUserId,
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toJson();

            return static::sendNotFoundResponse($errors);
        }

        $model = new (static::getModel());

        $model->fill($request->only([ 'name', 'latitude', 'longitude',  'journey_attempt_id']));
        $model->user_id = $currentUserId;
        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}
