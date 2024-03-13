<?php
namespace App\Filament\Resources\JourneyAttemptResource\Api\Handlers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\JourneyAttemptResource;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = JourneyAttemptResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    public function handler(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toJson();

            return static::sendNotFoundResponse( $errors );
        }

        $model = new (static::getModel());

        $model->fill($request->only(['name']));
        $model->user_id = auth()->user()->id;
        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}
