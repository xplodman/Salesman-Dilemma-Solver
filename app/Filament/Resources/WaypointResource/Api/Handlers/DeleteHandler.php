<?php

namespace App\Filament\Resources\WaypointResource\Api\Handlers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\WaypointResource;

class DeleteHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = WaypointResource::class;

    public static function getMethod()
    {
        return Handlers::DELETE;
    }

    public static function getModel()
    {
        return static::$resource::getModel();
    }

    public function handler(Request $request)
    {
        $id = $request->route('id');
        $currentUserId = auth()->user()->id;

        $validator = Validator::make(['id' => $id], [
            'id' => 'required|exists:waypoints,id,user_id,' . $currentUserId,
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toJson();

            return static::sendNotFoundResponse($errors);
        }

        $model = static::getModel()::where('user_id', $currentUserId)->find($id);

        if (!$model) {
            return static::sendNotFoundResponse();
        }

        $model->delete();

        return static::sendSuccessResponse($model, "Successfully Delete Resource");
    }
}
