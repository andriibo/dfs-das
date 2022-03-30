<?php

namespace App\Listeners;

use App\Events\CricketGameScheduleSavedEvent;
use Symfony\Component\Console\Output\ConsoleOutput;

class CricketGameScheduleSavedListener
{
    public function handle(CricketGameScheduleSavedEvent $cricketGameScheduleSavedEvent)
    {
        $consoleOutput = new ConsoleOutput();
        $consoleOutput->writeln("<info>Game Schedule: {$cricketGameScheduleSavedEvent->cricketGameSchedule->game_date}, Info added!</info>");
    }
}
