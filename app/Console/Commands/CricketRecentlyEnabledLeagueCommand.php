<?php

namespace App\Console\Commands;

use App\Enums\LeagueRecentlyEnabledEnum;
use App\Enums\SportIdEnum;
use App\Repositories\LeagueRepository;
use App\Services\Cricket\CreateCricketGameSchedulesService;
use App\Services\Cricket\CreateCricketGameStatsService;
use App\Services\Cricket\CreateCricketTeamsPlayersUnitsService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CricketRecentlyEnabledLeagueCommand extends Command
{
    protected $signature = 'cricket:recently-enabled-league';

    protected $description = 'Run recently enabled leagues';

    public function handle(
        LeagueRepository $leagueRepository,
        CreateCricketTeamsPlayersUnitsService $createCricketTeamsPlayersUnitsService,
        CreateCricketGameSchedulesService $createCricketGameSchedulesService,
        CreateCricketGameStatsService $createCricketGameStatsService
    ) {
        $this->info(Carbon::now() . ": Command {$this->signature} started");
        $leagues = $leagueRepository->getRecentlyEnabledListBySportId(SportIdEnum::cricket);
        foreach ($leagues as $league) {
            $createCricketTeamsPlayersUnitsService->handle($league);
            $createCricketGameSchedulesService->handle($league);
            $createCricketGameStatsService->handle($league);
            $league->recently_enabled = LeagueRecentlyEnabledEnum::no;
            $league->save();
        }
        $this->info(Carbon::now() . ": Command {$this->signature} finished");
    }
}
