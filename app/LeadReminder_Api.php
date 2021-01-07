<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadReminder_Api extends Model
{
    public $timestamp=false;
    public $table='leads_reminders';
    // protected $fillable = ['cust_id', 'reminder_name',
    //                        'reminder_date', 'reminder_time',
    //                         'reminderstatus', 'reminder_description', 
    //                         'created_at', 'updated_at'];
}
