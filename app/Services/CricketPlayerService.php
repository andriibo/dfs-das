<?php

namespace App\Services;

use App\Dto\CricketPlayerDto;
use App\Models\CricketPlayer;
use App\Repositories\CricketPlayerRepository;

class CricketPlayerService
{
    public function __construct(
        private readonly CricketPlayerRepository $cricketPlayerRepository
    ) {
    }

    public function storeCricketPlayer(CricketPlayerDto $cricketPlayerDto): CricketPlayer
    {
        return $this->cricketPlayerRepository->updateOrCreate([
            'feed_id' => $cricketPlayerDto->feedId,
            'feed_type' => $cricketPlayerDto->feedType->name,
            'sport' => $cricketPlayerDto->sport->name,
        ], [
            'first_name' => $cricketPlayerDto->firstName,
            'last_name' => $cricketPlayerDto->lastName,
            'sport_id' => $cricketPlayerDto->sport->name,
            'injury_status' => $cricketPlayerDto->injuryStatus->name,
            'salary' => $cricketPlayerDto->salary,
            'auto_salary' => $cricketPlayerDto->autoSalary,
            'total_fantasy_points' => $cricketPlayerDto->totalFantasyPoints,
            'total_fantasy_points_per_game' => $cricketPlayerDto->totalFantasyPointsPerGame,
        ]);
    }
}
