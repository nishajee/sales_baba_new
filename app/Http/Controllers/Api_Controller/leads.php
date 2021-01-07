<?php

namespace App\Http\Controllers\Api_Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Leaddetail_api;
use JWTAuth;
use App\User;
use App\leads_reminders;
use App\leads_activity;
use DB;
use Validator;
class leads extends Controller
{

  function all_leads()
  {
    ini_set('memory_limit', '-1');
    $lead_details = Leaddetail_api::whereStatus(2)->whereOrg_id(6)->paginate(25);
    foreach ($lead_details as $key => $value) {
      $lead_details[$key]->lead_owner = User::where('id', $value->lead_owner)->value('username');
    }
    return response()->json($lead_details, 200);
  }
  function getAllContactsList()
  {
          ini_set('memory_limit', '-1');
    $contacts_list = Leaddetail_api::whereStatus(1)->whereOrg_id(6)->paginate(25);
    foreach ($contacts_list as $key => $value) {
      $contacts_list[$key]->lead_owner = User::where('id', $value->lead_owner)->value('username');
    }
        return response()->json($contacts_list, 200);
  }
  function leads()
  {
    $user = JWTAuth::user();
    $lead_details =Leaddetail_api::whereStatus(2)->whereOrg_id(6)->paginate(25);
    foreach ($lead_details as $key => $value) {
      $lead_details[$key]->lead_owner = User::where('id', $value->lead_owner)->value('username');
    }
    return response()->json($lead_details, 200, compact('user'));
    // return response()->json( Leaddetail_api::paginate(5), 200 );
    // return response()->json( Leaddetail_api::get(), 200 );
  }
  function searchByLeadStatus(Request $request)
  {
          ini_set('memory_limit', '-1');
    $lead_details=Leaddetail_api::where(['lead_status' => $request->post('lead_status')])->whereOrg_id(6)->paginate(25);
    // var_dump($request->post('lead_status'));
    // return response()->json( Leaddetail_api::where(['lead_status' =>post('lead_status')] )->get(), 200  );
    return response()->json( ['status'=>true, 'data'=>$lead_details], 200 );
  }

  function searchByLeadOwner(Request $request)
  {
          ini_set('memory_limit', '-1');
    // var_dump($request->post('lead_owner'));
    return Leaddetail_api::where(['lead_owner' => $request->post('lead_owner')])->whereOrg_id(6)->paginate(25);

    //     // return response()->json( Leaddetail_api::where(['lead_status' =>post('lead_status')] )->get(), 200  );
    //     // return response()->json( Leaddetail_api::get(), 200 );
  }



  public function leadById($id)
  {
    return response()->json(Leaddetail_api::find($id), 200);
    //200 is response code
  }

  public function leadSave(Request $request) //now we can create object
  {

    $lead = Leaddetail_api::create($request->all());
    return response()->json(['status'=>true,'message'=>"Lead Details Added","data"=>$lead], 201);
  }

  public function leadUpdate(Request $request, Leaddetail_api $lead)
  {

    $lead->update($request->all());

    return response()->json(['status'=>true,'message'=>"Leade Details Updated","data"=>$lead], 200);
  }

  public function leadDelete(Request $request, Leaddetail_api $lead)
  {
    $lead->delete();
    return response()->json(null, 204);
  }
  public function updateContactToLead(Request $request)
  {
      $id=$request->contact_id;
    $lead_update=Leaddetail_api::whereId($id)->first();
    if(@leads_update)
    {
      $lead_update->status=2;
    //   $lead_update->updated_by=1;//Auth User Id
    //   $lead_update->updated_at=date('Y-m-d H:i::s');//Updated Time 
    //   $lead_update->is_lead=1;
      $lead_update->save();
      return response()->json(['status'=>true,"message"=>"This Contact Details is Upgrade In Leads"],200);
    }
    else
    {
      return response()->json(["status" => false, "message" => "Something Went Wrong Please try again"],200);
    }
  }
  public function listContact()
  {
    $contact_list=Leaddetail_api::whereOrg_id(1)->whereStatus(1)->get();
    return response()->json($contact_list);
  }
   public function AssignLeadToSalePerson(Request $request)
  {
    $lead_id=$request->lead_id;
    
    $sales_people_id=$request->sales_people_id;
    if($lead_id==""||$sales_people_id=="" )
    {
      return response()->json(['status'=>false,'message'=>"Please Select Lead And Sales People"],501);
    }
    if($lead_id!="")
    {
        if(is_array($lead_id))
      {
          $lead_id_array=$lead_id;
          if(count($lead_id_array)>0)
          {
            foreach($lead_id_array as $key_lead=>$value_lead)
            {
              DB::Table('leads')->where('id',$value_lead)->update(['owner_id'=>$sales_people_id]);
            }
          }
          
      }else
      {
          $lead_id_array=explode(",",$lead_id);
          if(is_array($lead_id_array))
          {
            foreach($lead_id_array as $key_lead=>$value_lead)
            {
              DB::Table('leads')->where('id',$value_lead)->update(['owner_id'=>$sales_people_id]);
            }
          }else
          {
            DB::Table('leads')->where('id',$lead_id_array)->update(['owner_id'=>$sales_people_id]);
          }
      }
    }
    return response()->json(['status'=>true,'message'=>"Leads is Assign Done"],200);
  }
    public function AssignContactsToSalePerson(Request $request)
    {
		$contact_id=$request->contact_id;
		$sales_people_id=$request->sales_people_id;
		if($contact_id==""||$sales_people_id=="" )
		{
			return response()->json(['status'=>false,'message'=>"Please Select Contact And Sales People"],501);
		}
		if($contact_id!="")
		{
			$contact_id_array=$contact_id;
			if(is_array($contact_id))
			{
				foreach($contact_id_array as $key_lead=>$value_lead)
				{
				DB::Table('leads')->where('id',$value_lead)->update(['owner_id'=>$sales_people_id]);
				}
			}
			else
			{
				
				$contact_id_array=explode(",",$contact_id);
				if(is_array($contact_id_array))
				{
					foreach($contact_id_array as $key_lead=>$value_lead)
					{
						DB::Table('leads')->where('id',$value_lead)->update(['owner_id'=>$sales_people_id]);
					}
				}else
				{
					DB::Table('leads')->where('id',$contact_id_array)->update(['owner_id'=>$sales_people_id]);
				}
			}
		}
      return response()->json(['status'=>true,'message'=>"Contacts is Assign Done"],200);
    }
    public function getUserDetail()
    {
        return response()->json(Auth::user(),200);
    }
    public function addAllLeadActivity(Request $request)
      {
        $validator = Validator::make($request->all(), [
          'activity_type' => 'required',
          'activity_name' => 'required',
          'call_stage_id' => 'required',
          'response_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=>false,"message"=>$validator->errors()], 200);
        }
        $leads_activity=new leads_activity();
        $leads_activity->lead_id=$request->lead_id;
        $leads_activity->activity_type=$request->activity_type;
        $leads_activity->activity_name=$request->activity_name;
        $leads_activity->call_duration=$request->call_duration ?? "";
        $leads_activity->call_stage_id=$request->call_stage_id;
        $leads_activity->response_id=$request->response_id;
        $leads_activity->comment=$request->comment;
        $leads_activity->status=1;
        $leads_activity->created_by=$request->user_id;
        $leads_activity->updated_by=$request->user_id;
        $leads_activity->updated_at=date('Y-m-d H:i:s');
        $leads_activity->save();
        return response()->json(['status'=>true,'data'=>$leads_activity,'messages'=>'Your Lead Activity is Added'],200);
      }
  public function addLeadsReminder(Request $request)
  {
      $validator = Validator::make($request->all(), [
        'reminder_type' => 'required',
        'reminder_date_time' => 'required',
        'reminder_comment' => 'required',
        'lead_id' => 'required',
    ]);
    if ($validator->fails()) {
        return response()->json(['status'=>false,"message"=>$validator->errors()], 200);
    }
    $leads_reminders=new leads_reminders();
    $leads_reminders->reminder_type=$request->reminder_type;
    $leads_reminders->reminder_date_time=date('Y-m-d',strtotime($request->reminder_date_time));
    $leads_reminders->reminder_before_time=$request->reminder_before_time;
    $leads_reminders->reminder_comment=$request->reminder_comment;
    $leads_reminders->lead_id=$request->lead_id;
    $leads_reminders->status=1;
    $leads_reminders->created_by=$request->user_id;
    $leads_reminders->updated_by=$request->user_id;
    $leads_reminders->updated_at=date('Y-m-d');
    $leads_reminders->save();
    return response()->json(['status'=>true,'data'=>$leads_reminders,'messages'=>'Your task is Schdule For Next '.$request->reminder_date_time],200);
  }
   public function FetechLeadActivity(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'lead_id' => 'required',
    ]);
    if ($validator->fails()) {
        return response()->json(['status'=>false,"message"=>$validator->errors()], 200);
    }
    $fetch_Activity_details=leads_activity::where('lead_id',$request->lead_id)->paginate(25);
    return response()->json(['status'=>true,'data'=>$fetch_Activity_details],200);
  }
  public function sendRemiderMail(Request $request)
  {
    $data=array();
    Mail::send('emails.reminder_email', ['data' => $data], function ($message) use ($data) {
      $message->to("amit2019.itscient@gmail.com");
      $message->from("amit2019.itscient@gmail.com","Amit");
  });
  return " Done";
  }


}
