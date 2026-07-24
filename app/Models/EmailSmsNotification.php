<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailSmsNotification extends Model
{
    use HasFactory;

    public $table = 'email_sms_notification';

    protected $fillable = [
        'id',
        'temp_id',
        'subject',
        'body',
        'link_text',
        'lang',
        'lang_id',
        'sms',
        'push_notification',
        'emailsent',
        'smssent',
        'pushsent',
        'status',
        'vendorsubject',
        'vendorbody',
        'vendorpush_notification',
        'vendoremailsent',
        'vendorsmssent',
        'vendorpushsent',
        'vendorsms',
        'adminsubject',
        'adminbody',
        'adminpush_notification',
        'adminemailsent',
        'adminsmssent',
        'adminpushsent',
        'adminsms',
        'created_at',
        'updated_at',
    ];

    public function notificationMapping()
    {
        return $this->hasOne(EmailNotificationMapping::class, 'email_sms_notification_id');
    }
}
