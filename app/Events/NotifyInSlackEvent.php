<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class NotifyInSlackEvent
{
    use Dispatchable;

    public function __construct(public \Throwable $exception)
    {
    }
}
