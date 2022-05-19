<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Service\API\PushThing\PushThing as PushThingApi;

class PushThing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tries = 3;

    protected $type;
    protected $data;
    protected $extend;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($type, $data, $extend = null)
    {
        $this->type = $type;
        $this->data = $data;
        $this->extend = $extend;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $PushThingApi = new PushThingApi();
        $PushThingApi->push2clients($this->type, $this->data, $this->extend);
    }
}
