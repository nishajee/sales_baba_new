<?php

namespace App\Http\Controllers\Api_Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LeadTask_APi;
class TaskController extends Controller
{
    function LeadTask() {
        return response()->json( LeadTask_APi::get(), 200 );
    }

    public function TaskById( $id )
 {
        return response()->json(LeadTask_APi::where(['cust_id' => $id] )->get(), 200 );
        //200 is response code
    }

    public function TaskSave( Request $request )//now we can create object
    {
        $task = LeadTask_APi::create( $request->all() );
        return response()->json( $task, 201 );

    }

    public function TaskUpdate( Request $request, LeadTask_APi $task )
 {
        $task->update( $request->all() );
        return response()->json( $task, 200 );
    }

    public function TaskDelete( Request $request, LeadTask_APi $call )
    {
        $task->delete();
        return response()->json( null, 204 );
    }
}
