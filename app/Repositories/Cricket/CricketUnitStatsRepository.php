<?php

namespace App\Repositories\Cricket;

use App\Enums\CricketGameSchedule\IsFakeEnum;
use App\Models\Cricket\CricketUnitStats;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;

class CricketUnitStatsRepository
{
    /**
     * @return Collection|CricketUnitStats[]
     */
    public function getList(): Collection
    {
        return CricketUnitStats::query()
            ->whereNotNull('game_schedule_id')
            ->get()
        ;
    }

    public function updateOrCreate(array $attributes, array $values = []): CricketUnitStats
    {
        return CricketUnitStats::updateOrCreate($attributes, $values);
    }

    /**
     * @return Collection|CricketUnitStats[]
     */
    public function getRealGameUnitStatsByUnitId(int $unitId): Collection
    {
        return CricketUnitStats::query()
            ->where(['unit_id' => $unitId])
            ->havingNotNull('game_schedule_id')
            ->join('cricket_game_schedule', function (JoinClause $join) {
                $join
                    ->on('cricket_unit_stats.game_schedule_id', '=', 'cricket_game_schedule.id')
                    ->where('is_fake', IsFakeEnum::no)
                ;
            })
            ->get()
        ;
    }

    public function getUnitStats(int $gameScheduleId, int $unitId, int $teamId): ?CricketUnitStats
    {
        return CricketUnitStats::query()
            ->where(['game_schedule_id' => $gameScheduleId])
            ->where(['unit_id' => $unitId])
            ->where(['team_id' => $teamId])
            ->first()
            ;
    }

    /**
     * @return Collection|CricketUnitStats[]
     */
    public function getByParams(int $unitId, array $gameScheduleIds): Collection
    {
        return CricketUnitStats::query()
            ->where('unit_id', $unitId)
            ->whereIn('game_schedule_id', $gameScheduleIds)
            ->get()
            ;
    }
}
