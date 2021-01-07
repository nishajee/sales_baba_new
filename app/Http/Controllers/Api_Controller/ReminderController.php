<?php

namespace App\Http\Controllers\APi_Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LeadReminder_Api;

class ReminderController extends Controller
{
    function reminders() {
        return response()->json(LeadReminder_Api::get(), 200 );
    }

    public function reminderById( $id )
 {
        return response()->json(LeadReminder_Api::where(['cust_id' => $id] )->get(), 200 );
        //200 is response code
    }

    public function reminderSave(Request $request )//now we can create object
    {
        $reminder = LeadReminder_Api::create( $request->all() );
        return response()->json($reminder, 201 );

    }

    public function reminderUpdate(Request $request, LeadReminder_Api $reminder )
 {
        $reminder->update($request->all() );
        return response()->json($reminder, 200 );
    }

    public function reminderDelete(Request $request, LeadReminder_Api $reminder )
    {
        $reminder->delete();
        return response()->json( null, 204 );
    }
}
