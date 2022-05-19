<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Service\API\Drawsource\Pusher;

class Pushservice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tries = 3;

    protected $lottery_ident;
    protected $issue;
    protected $code;
    protected $event;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($lottery_ident, $issue, $code, $event)
    {
        $this->lottery_ident = $lottery_ident;
        $this->issue = $issue;
        $this->code = $code;
        $this->event = $event;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $pusher = new Pusher($this->lottery_ident, $this->issue, $this->code, $this->event);
        $pusher->push2clients();
    }
}
