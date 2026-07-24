<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailNotificationMapping extends Model
{
    protected $table = 'email_notification_mappings';

    protected $primaryKey = null;

    public $incrementing = false;

    protected $fillable = [
        'email_type_id',
        'email_sms_notification_id',
        'module',
    ];

    public $timestamps = false;

    public function emailType()
    {
        return $this->belongsTo(EmailType::class, 'email_type_id');
    }

    public function emailSmsNotification()
    {
        return $this->belongsTo(EmailSmsNotification::class, 'email_sms_notification_id');
    }
}
