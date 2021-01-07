<?php

namespace App\Http\Controllers\APi_Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Busyresponse_Api;
class BusyResponseController extends Controller
{
    function Busyresponse_details() {
        return response()->json(Busyresponse_Api::get(), 200 );
    }

    public function BusyresponseById( $id )
 {
        return response()->json(Busyresponse_Api::where(['lead_id' => $id] )->get(), 200 );
        //200 is response code
    }

    public function BusyresponseSave( Request $request )//now we can create object
    {
        $bresp = Busyresponse_Api::create( $request->all() );
        return response()->json( $bresp, 201 );

    }

    public function BusyresponseUpdate( Request $request, Busyresponse_Api $bresp )
 {
        $bresp->update( $request->all() );
        return response()->json( $resp, 200 );
    }

    public function BusyresponseDelete( Request $request, Busyresponse_Api $bresp )
    {
        $bresp->delete();
        return response()->json( null, 204 );
    }
}
