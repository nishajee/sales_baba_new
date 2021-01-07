<?php

namespace App\Http\Controllers\APi_Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LeadNote_APi;

class NoteController extends Controller
{
    function notes() {
        return response()->json(LeadNote_APi::get(), 200 );
    }

    public function noteById( $id )
 {
    return response()->json(LeadNote_APi::where(['cust_id' => $id] )->get(), 200 );
        // return response()->json(LeadNote_APi::find( $id ), 200 );
        //200 is response code
    }

    public function noteSave(Request $request )//now we can create object
    {
        $note = LeadNote_APi::create( $request->all() );
        return response()->json($note, 201 );

    }

    public function noteUpdate(Request $request, LeadNote_APi $note )
 {
        $note->update($request->all() );
        return response()->json($note, 200 );
    }

    public function noteDelete(Request $request, LeadNote_APi $note )
    {
        $reminder->delete();
        return response()->json( null, 204 );
    }
}