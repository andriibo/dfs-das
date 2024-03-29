<?php

namespace App\Services\Cricket;

use App\Events\NotifyInSlackEvent;
use App\Mappers\CricketGameScheduleMapper;
use App\Models\League;

class CreateCricketGameSchedulesService
{
    public function __construct(
        private readonly CricketGoalserveService $cricketGoalserveService,
        private readonly CricketGameScheduleMapper $cricketGameScheduleMapper,
        private readonly ConfirmCricketGameScheduleService $confirmCricketGameScheduleService,
        private readonly CreateCricketGameScheduleService $createCricketGameScheduleService,
        private readonly UpdateCricketFakeGameSchedulesService $updateCricketFakeGameSchedulesService
    ) {
    }

    public function handle(League $league): void
    {
        try {
            $leagueId = $league->params['league_id'];
            $matches = $this->cricketGoalserveService->getGoalserveCricketMatches($leagueId);
            foreach ($matches as $match) {
                $cricketGameScheduleDto = $this->cricketGameScheduleMapper->map($match, $league->id);
                $cricketGameSchedule = $this->createCricketGameScheduleService->handle($cricketGameScheduleDto);
                $this->updateCricketFakeGameSchedulesService->handle($cricketGameScheduleDto);
                $this->confirmCricketGameScheduleService->handle($cricketGameSchedule);
            }
        } catch (\Throwable $exception) {
            event(new NotifyInSlackEvent($exception));
        }
    }
}
