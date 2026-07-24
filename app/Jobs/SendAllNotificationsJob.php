<?php

namespace App\Jobs;

use App\Http\Controllers\Traits\NotificationTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAllNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, NotificationTrait, Queueable, SerializesModels;

    protected $valuesArray;

    protected $userId;

    protected $templateId;

    protected $data;

    protected $vendorId;

    /**
     * Create a new job instance.
     */
    public function __construct($valuesArray, $userId, $templateId, $data = ['key' => 'value'], $vendorId = 0)
    {
        $this->valuesArray = $valuesArray;
        $this->userId = $userId;
        $this->templateId = $templateId;
        $this->data = $data;
        $this->vendorId = $vendorId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->_sendAllNotificationsNow(
            $this->valuesArray,
            $this->userId,
            $this->templateId,
            $this->data,
            $this->vendorId
        );
    }
}
