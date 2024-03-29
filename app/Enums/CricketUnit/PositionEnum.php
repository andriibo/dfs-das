<?php

namespace App\Enums\CricketUnit;

use ArchTech\Enums\Values;

enum PositionEnum: string
{
    use Values;

    case bowler = 'Bowler';

    case batsman = 'Batsman';

    case battingAllrounder = 'Batting Allrounder';

    case wicketkeeperBatsman = 'Wicketkeeper Batsman';

    case bowlingAllrounder = 'Bowling Allrounder';
}
