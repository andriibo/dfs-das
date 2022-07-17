<?php

namespace App\Http\Resources;

use App\Helpers\ActionPointHelper;
use App\Services\GetContestActionPointService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class GameLogResource extends JsonResource
{
    private static int $contestId;

    public function toArray($request): array
    {
        /* @var $getContestActionPointService GetContestActionPointService */
        $getContestActionPointService = resolve(GetContestActionPointService::class);
        $actionPoint = $getContestActionPointService->handle(self::$contestId, $this->action_point_id);

        return [
            'playerId' => $this->unit_id,
            'playerName' => $this->unit->player->getFullName(),
            'score' => ActionPointHelper::getScore($this->value, $actionPoint->values, $this->unit->position),
            'message' => $actionPoint->game_log_template,
        ];
    }

    public static function customCollection($resource, $contestId): AnonymousResourceCollection
    {
        self::$contestId = $contestId;

        return parent::collection($resource);
    }
}
