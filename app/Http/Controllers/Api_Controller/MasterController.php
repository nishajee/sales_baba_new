<?php

namespace App\Http\Controllers\APi_Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\org;
use Validator;
use App\lead_status;
use App\call_response;
use App\Call_Stage;
use App\designation;
use Auth;

class MasterController extends Controller
{
    function org()
    {
        return response()->json(org::get(), 200);
    }

    public function orgById()
    {
        return response()->json($user, 200);
        // $user = org::get();
        // return response()->json($user, 200 );
        //200 is response code
    }

    public function orgSave(Request $request) //now we can create object
    {
        $attachment = org::create($request->all());
        return response()->json($attachment, 201);
    }

    public function orgUpdate(Request $request, org $attachment)
    {
        $attachment->update($request->all());
        return response()->json($attachment, 200);
    }

    public function orgDelete(Request $request, org $attachment)
    {
        $attachment->delete();
        return response()->json(null, 204);
    }
    public function list_call_response()
    {
        $call_response = call_response::where('org_id', Auth::user()->org_id)->where('status', 1)->get();
        return response()->json(["data"=>$call_response,'status'=>true], 200);
    }
    public function add_call_response(Request $request)
    {
        // $request->header( 'Authorization' );
        // return response()->json(['data'=>Auth::user()], 200);
        $validator = Validator::make($request->all(), [
      'response_name' => 'required',
      
    ]);
    if ($validator->fails()) {
        return response()->json(['status'=>false,"message"=>$validator->errors()], 200);
    }
        if (@$request->response_id != "") {
            $call_response = call_response::find($request->response_id);
        } else {
            $call_response = new call_response();
            $call_response->created_by = Auth::user()->id;
        }
        $call_response->org_id =$request->org_id;
        $call_response->response_name = $request->response_name;
        $call_response->status = 1;
        $call_response->updated_by = Auth::user()->id;
        $call_response->updated_at = date('Y-m-d H:i:s');
        $call_response->save();
        return response()->json(['data' => $call_response, 'status'=>true,"message" => "Call Response Added"],200);
    }
    public function fetch_call_response($id)
    {
        $call_response_details = call_response::where('id', $id)->first();
        return response()->json(['data'=>$call_response_details,'status'=>true],200);
    }
    public function delete_call_response($id)
    {
        if (call_response::where('id', $id)->first()) {
            $call_response_details = call_response::where('id', $id)->update(['status' => 0]);
            return response()->json(["status" => true, "message" => "call Response Deleted"],200);
        } else {
            return response()->json(["status" => false, "message" => "Something Went Wrong Please try again"],501);
        }
    }
    public function listCallStage()
    {
        $call_stage = Call_Stage::where('org_id', Auth::user()->org_id)->get();
        return response()->json(["data" => $call_stage, 'status' => true],200);
    }
    public function addCallStage(Request $request)
    {
        $validator = Validator::make($request->all(), [
      'call_stage' => 'required',
      
    ]);
    if ($validator->fails()) {
        return response()->json(['status'=>false,"message"=>$validator->errors()], 200);
    }
        if ($request->Call_Stage_id != "") {
            $call_stage = Call_Stage::find($request->Call_Stage_id);
        } else {
            $call_stage = new Call_Stage();
            $call_stage->created_by = Auth::user()->id;
        }
        $call_stage->org_id = Auth::user()->org_id;
        $call_stage->call_stage = $request->call_stage;
        $call_stage->status = 1;
        $call_stage->updated_by = Auth::user()->id;
        $call_stage->updated_at = date('Y-m-d H:i:s');
        $call_stage->save();
        return response()->json(['data' => $call_stage, "message" => "Call Stage Added",'status'=>true],200);
    }
    public function fetchCallStage($id)
    {
        $call_stage = Call_Stage::whereId($id)->first();
        return response()->json($call_stage,200);
    }
    public  function deleteCallStage($id)
    {
        if (Call_Stage::whereId($id)->first()) {
            $call_stage = Call_Stage::whereId($id)->first();
            $call_stage->status = 0;
            $call_stage->save();
            return response()->json(['status' => true, "message" => "Call Stage Deleted"],200);
        } else {
            return response()->json(["status" => false, "message" => "Something Went Wrong Please try again"],200);
        }
    }
    public function listAllLeadStatus()
    {
        $status_details=lead_status::where('org_id',Auth::user()->org_id)->where('status',1)->get();
        return response()->json(['status'=>true,"data"=>$status_details],200);
    }
    public function addLeadStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_status' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=>false,"message"=>$validator->errors()], 200);
        }
        if($request->status_id!="")
        {
            $status_details=lead_status::find($request->status_id);
            $message="Lead Status edited";
        }else
        {
            $status_details=new lead_status();
            $message="Lead Status Added";
        }
        $status_details->lead_status=$request->lead_status;
        $status_details->status=1;
        $status_details->org_id=Auth::user()->org_id;
        $status_details->created_by=Auth::user()->id;
        $status_details->updated_by=$request->user_id;
        $status_details->save();
        return response()->json(['status'=>true,'data'=>$status_details,'message'=>$message]);
    }
    public function fetchLeadStatusById($id)
    {
        if($id)
        {
            $status_details=lead_status::find($id);
            return response()->json(['status'=>true,'data'=>$status_details],200);
        }
        return response()->json(['status'=>false,'message'=>"SomeThing Went Wrong Please Try Again"],200);
    }
    public function DeleteLeadStatus($id)
    {
        if($id!="")
        {
            $status_details=lead_status::find($id);
            $status_details->status=2;
            $status_details->save();
            return response()->json(['status'=>true,"message"=>"Delete Status Deleted"],200);
        }
        return response()->json(['status'=>false,'message'=>"SomeThing Went Wrong Please Try Again"],200);

    }



    public function listDesignation(Request $request)
    {
        
        $designation=designation::where('org_id', $request->org_id)->get();
        
        foreach($designation as $key =>$value)
        {
            $designation[$key]->created_at=date('d/m/Y',strtotime($designation[$key]->created_at)) ?? "";
        }
        return response()->json(['status'=>true,'data'=>$designation],200);
    }

    public function listDesignationForDataMiner_Telecaller(Request $request)
    {
        
      
        $designation_dataMiner = designation::whereIn('id',[4,5])->where('org_id', $request->org_id)->get();
        return  $designation_dataMiner;
        return response()->json(['status'=>true,'data'=>$designation_dataMiner],200);
    }
    public function addDesignation(Request $request)
    {
        $designation=designation::where('designation',trim($request->designation))->where('org_id',$request->org_id)->first();
        if($designation!="")
        {
            return response()->json(['status'=>false,'message'=>"designation Already Exist"],200); 
        }
        $designation= new designation();
        $designation->designation=$request->designation;
        $designation->org_id=$request->org_id;
        $designation->designation=$request->designation;
        $designation->updated_at=date('Y-m-d H:i:s');
        $designation->created_by=Auth::user()->id;
        $designation->status=1;
        $designation->updated_by=Auth::user()->id;
        $designation->save();
        return response()->json(['status'=>true,'data'=>$designation,'message'=>"designation Added"],200);
    }

    public function fetchDesignationById($designationid)
    {
        $designation= designation::find($designationid);
        return response()->json(['status'=>true,'data'=>$designation],200);
    }

    
    public function deleteDesignationById($designationid)
    {
        $designation= designation::find($designationid);
        $designation->status=0;
        $designation->save();
        return response()->json(['status'=>true,"message"=>"designation Deleted"],200);
    }
}
