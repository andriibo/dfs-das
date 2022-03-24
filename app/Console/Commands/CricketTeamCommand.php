<?php

namespace App\Console\Commands;

use App\Enums\LeagueSportIdEnum;
use App\Mappers\CricketPlayerMapper;
use App\Mappers\CricketTeamMapper;
use App\Mappers\CricketTeamPlayerMapper;
use App\Models\CricketPlayer;
use App\Models\League;
use App\Services\CricketGoalserveService;
use App\Services\CricketPlayerService;
use App\Services\CricketTeamPlayerService;
use App\Services\CricketTeamService;
use App\Services\LeagueService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CricketTeamCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cricket:team';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get teams for sport Cricket from Goalserve';

    /**
     * Execute the console command.
     */
    public function handle(LeagueService $leagueService)
    {
        $this->info(Carbon::now() . ": Command {$this->signature} started");
        $leagues = $leagueService->getListBySportId(LeagueSportIdEnum::cricket);
        foreach ($leagues as $league) {
            $this->parseCricketTeams($league);
        }
        $this->info(Carbon::now() . ": Command {$this->signature} finished");
    }

    public function parseCricketTeam(array $data, int $leagueId): void
    {
        $cricketTeamService = resolve(CricketTeamService::class);
        $cricketTeamMapper = new CricketTeamMapper();
        $cricketTeamDto = $cricketTeamMapper->map($data, $leagueId);
        $cricketTeam = $cricketTeamService->storeCricketTeam($cricketTeamDto);

        if (!$cricketTeam) {
            return;
        }

        foreach ($data['player'] as $player) {
            $cricketPlayer = $this->parseCricketPlayer($player);
            if (!$cricketPlayer) {
                continue;
            }

            $this->attachPlayerToTeam($cricketPlayer, $cricketTeam->id, $player['role']);
        }
    }

    public function parseCricketPlayer(array $data): CricketPlayer
    {
        $cricketPlayerService = resolve(CricketPlayerService::class);
        $cricketPlayerMapper = new CricketPlayerMapper();
        $cricketPlayerDto = $cricketPlayerMapper->map([
            'id' => $data['name'],
            'name' => $data['id'],
        ]);

        return $cricketPlayerService->storeCricketPlayer($cricketPlayerDto);
    }

    private function parseCricketTeams(League $league): void
    {
        $cricketGoalserveService = resolve(CricketGoalserveService::class);

        try {
            $leagueId = $league->params['league_id'];
            $teams = $cricketGoalserveService->getGoalserveCricketTeams($leagueId);
            foreach ($teams as $team) {
                $this->parseCricketTeam($team, $league->id);
            }
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());
        }
    }

    private function attachPlayerToTeam(CricketPlayer $cricketPlayer, int $teamId, string $role): void
    {
        $cricketTeamPlayerMapper = new CricketTeamPlayerMapper();
        $cricketTeamPlayerService = resolve(CricketTeamPlayerService::class);

        $cricketTeamPlayerDto = $cricketTeamPlayerMapper->map([
            'player_id' => $cricketPlayer->id,
            'team_id' => $teamId,
            'role' => $role,
        ]);

        $cricketTeamPlayerService->storeCricketTeamPlayer($cricketTeamPlayerDto);
    }
}
