<?php

namespace App\Dto;

use App\Enums\CricketFeedTypeEnum;
use App\Enums\CricketGameScheduleStatusEnum;
use App\Enums\CricketGameScheduleTypeEnum;

class CricketGameScheduleDto
{
    public ?int $id;
    public int $feedId;
    public int $leagueId;
    public int $homeTeamId;
    public int $awayTeamId;
    public string $gameDate;
    public bool $hasFinalBox;
    public ?int $isDataConfirmed;
    public string $homeTeamScore;
    public string $awayTeamScore;
    public bool $isFake;
    public bool $isSalaryAvailable;
    public CricketFeedTypeEnum $feedType;
    public ?CricketGameScheduleStatusEnum $status;
    public CricketGameScheduleTypeEnum $type;
}
