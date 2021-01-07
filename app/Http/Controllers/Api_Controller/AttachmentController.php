<?php

namespace App\Http\Controllers\APi_Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LeadAttachment_APi;

class AttachmentController extends Controller
{
    function attachments() {
        return response()->json(LeadAttachment_APi::get(), 200 );
    }

    public function attachmentById( $id )
 {
    return response()->json(LeadAttachment_APi::where(['cust_id' => $id] )->get(), 200 );
        // return response()->json(LeadAttachment_APi::find( $id ), 200 );
        //200 is response code
    }

    public function attachmentSave(Request $request )//now we can create object
    {
        $attachment = LeadAttachment_APi::create( $request->all() );
        return response()->json($attachment, 201 );

    }

    public function attachmentUpdate(Request $request, LeadAttachment_APi $attachment )
 {
        $attachment->update($request->all() );
        return response()->json($attachment, 200 );
    }

    public function attachmentDelete(Request $request, LeadAttachment_APi $attachment )
    {
        $attachment->delete();
        return response()->json( null, 204 );
    }
}
