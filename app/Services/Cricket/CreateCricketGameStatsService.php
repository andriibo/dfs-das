<?php

namespace App\Services\Cricket;

use App\Enums\CricketGameSchedule\HasFinalBoxEnum;
use App\Helpers\CricketGameScheduleHelper;
use App\Mappers\CricketGameStatsMapper;
use App\Models\Cricket\CricketGameSchedule;
use App\Repositories\Cricket\CricketGameScheduleRepository;
use Illuminate\Support\Facades\Log;

class CreateCricketGameStatsService
{
    public function __construct(
        private readonly CricketGoalserveService $cricketGoalserveService,
        private readonly CricketGameStatsMapper $cricketGameStatsMapper,
        private readonly CricketGameStatsService $cricketGameStatsService,
        private readonly CreateCricketUnitStatsService $createCricketUnitStatsService,
        private readonly CricketGameScheduleRepository $cricketGameScheduleRepository
    ) {
    }

    public function handle(CricketGameSchedule $cricketGameSchedule): void
    {
        try {
            $leagueFeedId = $cricketGameSchedule->league->params['league_id'];
            $gameDate = $this->getGameDate($cricketGameSchedule);
            $formattedDate = $this->getFormattedDate($gameDate);
            $data = $this->cricketGoalserveService->getGoalserveGameStats($formattedDate, $leagueFeedId, $cricketGameSchedule->feed_id);
            if (empty($data)) {
                Log::channel('stderr')->error("No data for date {$formattedDate} and feed_id {$cricketGameSchedule->feed_id}");

                return;
            }

            $cricketGameStatsDto = $this->cricketGameStatsMapper->map($data, $cricketGameSchedule->id);
            $cricketGameStats = $this->cricketGameStatsService->storeCricketGameStats($cricketGameStatsDto);
            $this->createCricketUnitStatsService->handle($cricketGameStats);
            if (!$cricketGameSchedule->hasFinalBox() && CricketGameScheduleHelper::isStatusLive($cricketGameSchedule->status)) {
                $cricketGameSchedule->has_final_box = HasFinalBoxEnum::yes;
                $cricketGameSchedule->save();
            }
        } catch (\Throwable $exception) {
            Log::channel('stderr')->error($exception->getMessage());
        }
    }

    private function getFormattedDate(string $dateTime): string
    {
        $dateTime = new \DateTime($dateTime);

        return $dateTime->format('d.m.Y');
    }

    private function getGameDate(CricketGameSchedule $cricketGameSchedule): string
    {
        if ($cricketGameSchedule->is_fake) {
            $notFakeCricketGameSchedule = $this->cricketGameScheduleRepository->getNotFakeByFeedId($cricketGameSchedule->feed_id);

            return $notFakeCricketGameSchedule->game_date;
        }

        return $cricketGameSchedule->game_date;
    }
}
