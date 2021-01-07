<?php

namespace App\Http\Controllers\APi_Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Lead_Event_Api;

class EventController extends Controller
{
    function events() {
        return response()->json(Lead_Event_Api::get(), 200 );
    }

    public function eventById( $id )
 {
        return response()->json(Lead_Event_Api::where(['cust_id' => $id] )->get(), 200 );
        //200 is response code
    }

    public function eventSave( Request $request )//now we can create object
    {
        $event = Lead_Event_Api::create( $request->all() );
        return response()->json( $event, 201 );

    }

    public function eventUpdate( Request $request, Lead_Event_Api $event )
 {
        $event->update( $request->all() );
        return response()->json( $event, 200 );
    }

    public function eventDelete( Request $request, Lead_Event_Api $event )
    {
        $event->delete();
        return response()->json( null, 204 );
    }
}
