<?php

namespace App\Services;

use App\Dto\CricketTeamDto;
use App\Models\CricketTeam;
use App\Repositories\CricketTeamRepository;
use Illuminate\Database\Eloquent\Collection;

class CricketTeamService
{
    public function __construct(
        private readonly CricketTeamRepository $cricketTeamRepository
    ) {
    }

    /**
     * @return Collection|CricketTeam[]
     */
    public function getCricketTeams(): Collection
    {
        return $this->cricketTeamRepository->getList();
    }

    public function getCricketTeamByFeedId(string $feedId): CricketTeam
    {
        return $this->cricketTeamRepository->getByFeedId($feedId);
    }

    public function storeCricketTeam(CricketTeamDto $cricketTeamDto): CricketTeam
    {
        return $this->cricketTeamRepository->updateOrCreate([
            'feed_id' => $cricketTeamDto->feedId,
            'league_id' => $cricketTeamDto->leagueId,
            'feed_type' => $cricketTeamDto->feedType->name,
        ], [
            'name' => $cricketTeamDto->name,
            'nickname' => $cricketTeamDto->nickname,
            'alias' => $cricketTeamDto->alias,
            'country_id' => $cricketTeamDto->countryId,
            'logo' => $cricketTeamDto->logo,
        ]);
    }
}