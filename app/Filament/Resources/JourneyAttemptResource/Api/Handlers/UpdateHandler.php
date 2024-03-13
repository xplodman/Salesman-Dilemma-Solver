<?php
namespace App\Filament\Resources\JourneyAttemptResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\JourneyAttemptResource;

class UpdateHandler extends Handlers {
    public static string | null $uri = '/{id}';
    public static string | null $resource = JourneyAttemptResource::class;

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    public function handler(Request $request)
    {
        $id = $request->route('id');

        $model = static::getModel()::where( 'user_id', auth()->user()->id )->find($id);

        if (!$model) return static::sendNotFoundResponse();

        $model->fill($request->only(['name']));

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Update Resource");
    }
}
