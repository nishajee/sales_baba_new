<?php

namespace App\Http\Controllers\APi_Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LeadEmail_APi;
class EmialController extends Controller
{
    function emails() {
        return response()->json(LeadEmail_APi::get(), 200 );
    }

    public function emailById( $id )
 {
        return response()->json(LeadEmail_APi::where(['cust_id' => $id] )->get(), 200 );
        //200 is response code
    }

    public function emailSave( Request $request )//now we can create object
    {
        $email = LeadEmail_APi::create( $request->all() );
        return response()->json( $email, 201 );

    }

    public function emailUpdate( Request $request, LeadEmail_APi $email )
 {
        $email->update( $request->all() );
        return response()->json( $email, 200 );
    }

    public function emailDelete( Request $request, LeadEmail_APi $email )
    {
        $email->delete();
        return response()->json( null, 204 );
    }
}
