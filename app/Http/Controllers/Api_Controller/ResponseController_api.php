<?php

namespace App\Http\Controllers\APi_Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Response_api;
class ResponseController_api extends Controller
{ 
	function response_details() {
        return response()->json(Response_api::get(), 200 );
    }

    public function responseById( $id )
 {
        return response()->json(Response_api::where(['lead_id' => $id] )->get(), 200 );
        //200 is response code
    }

    public function responseSave( Request $request )//now we can create object
    {
        $resp = Response_api::create( $request->all() );
        return response()->json( $resp, 201 );

    }

    public function responseUpdate( Request $request, Response_api $resp )
 {
        $resp->update( $request->all() );
        return response()->json( $resp, 200 );
    }

    public function responseDelete( Request $request, Response_api $resp )
    {
        $resp->delete();
        return response()->json( null, 204 );
    }
}
