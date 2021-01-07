<?php

namespace App\Http\Controllers\Api_Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Lead_callApi;
class Call_leadController extends Controller
{
    function Leadcalls() {
        return response()->json( Lead_callApi::get(), 200 );
    }

    public function CallById( $id )
 {
        return response()->json(Lead_callApi::where(['cust_id' => $id] )->get(), 200 );
        //200 is response code
    }

    public function CallSave( Request $request )//now we can create object
    {
        $call = Lead_callApi::create( $request->all() );
        return response()->json( $call, 201 );

    }

    public function CallUpdate( Request $request, Lead_callApi $call )
 {
        $call->update( $request->all() );
        return response()->json( $call, 200 );
    }

    public function CallDelete( Request $request, Lead_callApi $call )
    {
        $call->delete();
        return response()->json( null, 204 );
    }
}
