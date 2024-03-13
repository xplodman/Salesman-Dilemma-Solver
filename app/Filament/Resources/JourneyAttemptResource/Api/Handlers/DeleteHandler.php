<?php
namespace App\Filament\Resources\JourneyAttemptResource\Api\Handlers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\JourneyAttemptResource;

class DeleteHandler extends Handlers {
    public static string | null $uri = '/{id}';
    public static string | null $resource = JourneyAttemptResource::class;

    public static function getMethod()
    {
        return Handlers::DELETE;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    public function handler(Request $request)
    {
        $id = $request->route('id');
        $model = static::getModel()::where( 'user_id', auth()->user()->id )->find($id);

        if (!$model) return static::sendNotFoundResponse();

        $model->delete();

        return static::sendSuccessResponse($model, "Successfully Delete Resource");
    }
}
