<?php

namespace App\Filament\Resources\JourneyAttemptResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\JourneyAttemptResource;
use Illuminate\Routing\Router;

class JourneyAttemptApiService extends ApiService
{
    protected static string | null $resource = JourneyAttemptResource::class;

    public static function handlers(): array
    {
        return [
            Handlers\CreateHandler::class,
            Handlers\UpdateHandler::class,
            Handlers\DeleteHandler::class,
            Handlers\PaginationHandler::class,
            Handlers\DetailHandler::class
        ];
    }
}
