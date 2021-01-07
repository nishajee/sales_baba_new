<?php

namespace App\Http\Controllers\Api_Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Leaddetail_api;
use JWTAuth;
use App\User;
use App\LeadReminder_Api;
use App\leads_activity;
use DB;
use Validator;
use Auth;
use App\Imports\LeadsImport;
use Maatwebsite\Excel\Facades\Excel;
use Session;
use App\Countries;
use App\state;
use App\cities;

class LeadsController extends Controller
{

    public function importleads(Request $request)
    {

        $contacts = [];
        if ($request->file('uploadFile')) {
            $contacts_excel_array = Excel::toArray(new LeadsImport, request()->file('uploadFile'));
            
            $counts_contacts_excel_array = count($contacts_excel_array[0]);
            
            /* import count limitation validation */
            if ($counts_contacts_excel_array > 200) {
                return response()->json(['status' => false, 'message' => "No.s of record could not be greater than 200."], 200);
            }
            $total_record = $noOfFails = $noOfSuccess = 0;
            $ErrorTxt = "";
            $match_array = array();
            
            if ($counts_contacts_excel_array != 0) {
                foreach ($contacts_excel_array[0] as $key => $value) {
                    $current_index_fail_count = 0;
                    // duplicate email validation 
                    $contacts_email = Leaddetail_api::where('email', strtolower($value['email']))->count();
                    // duplicate phone no validation 
                    $contacts_phone = Leaddetail_api::where('mobile', $value['phone_no'])->count();
                    // phone no validation 
                    $filtered_phone_number = filter_var($value['phone_no'], FILTER_SANITIZE_NUMBER_INT);
                    $is_phone_validate = str_replace("-", "", $filtered_phone_number);
                    /* phone number numeric validation */
                    if (preg_match('/[a-zA-Z]/', $value['phone_no'])) {
                        $match_array[$key]['phone_no_message'] = "On row " . ($key + 2) . " Phone No. : " . $value['phone_no'] . ", is a Invalid.\n";
                        $noOfFails++;
                        $current_index_fail_count++;
                    }
                    // small phone no validation (if phone no less then 10 then condition is true)
                    if (strlen($is_phone_validate) < 10) {
                        $match_array[$key]['phone_no_message'] = "On row " . ($key + 2) . " Phone No.: " . $value['phone_no'] . ", is a Small .\n";
                        $noOfFails++;
                        $current_index_fail_count++;
                    }
                    // long phone no validation (if phone no greater then 10 then condition is true)
                    if (strlen($is_phone_validate) > 10) {
                        $match_array[$key]['phone_no_message'] = "On row " . ($key + 2) . " Phone No.: " . $value['phone_no'] . ", is a Lenght Long .\n";
                        $noOfFails++;
                        $current_index_fail_count++;
                    }
                    // if phone no not start with 6,7,8,9 then phone no is invalide validation 
                    if (!preg_match('/[6789]/', $value['phone_no'])) {
                        $match_array[$key]['phone_no_message'] = "On row " . ($key + 2) . " Phone No. : " . $value['phone_no'] . ", is a invalid.\n";
                        $noOfFails++;
                        $current_index_fail_count++;
                    }
                    // if phone no is exist in database then validation is work 
                    if ($contacts_phone > 0) {
                        $match_array[$key]['phone_no_message'] = "On row " . ($key + 2) . " phone_no. : " . $value['phone_no'] . ", is a Already Exist in our database.\n";
                        $noOfFails++;
                        $current_index_fail_count++;
                    }
                    $match_array[$key]['phone_no'] = $value['phone_no'];
                    
                    // if email wrong validation 
                    if (!filter_var($value['email'], FILTER_VALIDATE_EMAIL)) {
                        $match_array[$key]['email_message'] = "On row " . ($key + 2) . " Email ID: " . $value['email'] . ", is a invalid.\n";
                        $noOfFails++;
                        $current_index_fail_count++;
                    }
                    // if email is already exist in database then this validation is work 
                    else if ($contacts_email > 0) {
                        
                        $match_array[$key]['email_message'] = "On row " . ($key + 2) . " Email ID. : " . $value['email'] . ", is a Already Exist in our database.\n";
                        $noOfFails++;
                        $current_index_fail_count++;
                    } else {
                        $match_array[$key]['email'] = $value['email'];
                    }
                    // country validation start 
                    if (!countries::where('country_name', 'LIKE', '' . trim($value['country']) . '')->exists()) {
                        $match_array[$key]['country_message'] = "On row " . ($key + 2) . " Country name. : " . $value['country'] . ", is not available in our database.\n";
                        $noOfFails++;
                        $current_index_fail_count++;
                        // return $ErrorTxt .= "On row ".($key+2). " Country name. : ".$value['country'].", Wrong Spelling.\n";
                    } else {
                        $match_array[$key]['country'] = $value['country'];
                    }
                    // state validation start 
                    if (!state::where('state_name', 'LIKE', '' . trim($value['state']) . '')->exists()) {

                        $match_array[$key]['state_message'] = "On row " . ($key + 2) . " State name. : " . $value['state'] . ", is not available in our database .\n";
                        $noOfFails++;
                        $current_index_fail_count++;
                        //    return $ErrorTxt .= "On row ".($key+2). " State name. : ".$value['state'].", Wrong Spelling.\n";
                    } else {
                        $match_array[$key]['state'] = $value['state'];
                    }
                    // city validation start 
                    if (!cities::where('city_name', 'LIKE', '' . trim($value['city']) . '')->exists()) {
                        
                        $match_array[$key]['city_message'] = "On row " . ($key + 2) . " City name. : " . $value['city'] . ", is not available in our database.\n";
                        $noOfFails++;
                        $current_index_fail_count++;
                    } else {
                        $match_array[$key]['city'] = $value['city'];
                    }
                    
                    if ($current_index_fail_count > 0) {
                        continue;
                    } else {
                        // Database Insertion start 
                        $contacts = new Leaddetail_api();
                        $contacts->first_name = $value['name'];
                        $contacts->owner_id = Auth::user()->id;
                        $contacts->company_name =  $value['company_name'];
                        $contacts->country = countries::where('country_name', 'LIKE', '' . trim($value['country']) . '')->value('country_id');
                        $contacts->state_province = state::where('state_name', 'LIKE', '' . trim($value['state']) . '')->value('state_id');
                        $contacts->city_distt = cities::where('city_name', 'LIKE', '' . trim($value['city']) . '')->value('city_id');
                        $contacts->mobile =  $value['phone_no'];
                        $contacts->email =  preg_replace('/\s+/', '', trim(strtolower($value['email'])));
                        $contacts->org_id = $request->org_id;
                        $contacts->org_id = Auth::user()->org_id;
                        $contacts->status = 2; 
                        $contacts->created_by = $request->user_id;
                        // $contacts->updated_by = $request->user_id;
                        
                        $contacts->save();
                        $noOfSuccess++;
                    }
                }
            } else {
                return response()->json(['status' => true, 'message' => "you cannot import empty excel sheet", "data" => $contacts], 200);
            }
        } else {
            return response()->json(['status' => false, 'message' => "Please Upload Excel File"], 200);
        }
        
        $filename = "public/format/error/leads-errorLog".Auth::user()->name."".date('d-m-Y').Time().".txt";  // error file name /
        
        $myfile = fopen($filename, "w"); // open error file name by using fopen function /
        $txt = "Sales BaBa\n";
        $txt .= "----------------------------------------------------------------------------------------------------------------------------------\n";
        $txt .= "DATE: " . date('d/m/Y h:i A') . "\n";
        $txt .= "TOTAL  LEADS RECORD  COUNT: " . $total_record . "\n";
        $txt .= "TOTAL New Leads COUNT: " . $noOfSuccess . "\n";
        $txt .= "TOTAL Old Leads COUNT: " . $noOfFails . "\n";

        // $txt .=  $match_array['email_message'];
        // $txt .= $match_array['phone_no_message'];
        if ($match_array != "") {
            $txt .= "Leads Already Exist in List" . "\n";

            foreach ($match_array as $key_match => $value_match) {
                if (isset($value_match['email_message']) || isset($value_match['phone_no_message']) || isset($value_match['country_message']) || isset($value_match['state_message']) || isset($value_match['city_message'])) {
                    $txt .= $value_match['email_message'] ?? '';
                    $txt .= $value_match['phone_no_message'] ?? '';
                    $txt .= $value_match['country_message'] ?? '';
                    $txt .= $value_match['state_message'] ?? '';
                    $txt .= $value_match['city_message'] ?? '';
                } else {
                    return response()->json(['status' => false, 'message' => "Please Fill Record"], 200);
                }
            }


            $txt .= "----------------------------------------------------------------------------------------------------------------------------------\n";
            if ($noOfFails == 0) {
                $txt .= "No Error Found";
            } else {
                $txt .= $ErrorTxt;
            }
        }
        // return $myfile;
        fwrite($myfile, $txt);
        // exit;
        fclose($myfile); //close file
        if (file_get_contents($filename) == null) //if error file does not exit ant data then popup message success
        {
            return response()->json(['status' => true, 'message' => "Import has been Done"], 200);
        } else {
            $this->saveFile($filename, file_get_contents($filename));
            if ($noOfFails != 0) {
                $summary = 'import_summary' . $txt;
                $date = 'date' . date('d/m/Y h:i A');
                $t_record = 'total_record' . $total_record;
                $Success = 'noOfSuccess' . $noOfSuccess;
                $Fails = 'noOfFails' . $noOfFails;
                return response()->json(['status' => true, 'message' => "please Check Error Log", "data" => $filename], 200);
            }
            // return ('success');
        }
        return response()->json(['status' => true, 'message' => "Excel Import Success"], 200);
    }


    public function saveFile($filename, $filecontent)
    {
        if (strlen($filename) > 0) {
            $folderPath = 'public/format/error';
            if (!file_exists($folderPath)) {
                mkdir($folderPath);
            }
            $file = @fopen($folderPath . DIRECTORY_SEPARATOR . $filename, "w");
            if ($file != false) {
                fwrite($file, $filecontent);
                fclose($file);
                return 1;
            }
            return -2;
        }
        return -1;
    }

    // function all_leads()
    // {
    //     ini_set('memory_limit', '-1');
    //     $lead_details = Leaddetail_api::whereStatus(2)->whereOrg_id(Auth::user()->org_id)->paginate(25);
    //     foreach ($lead_details as $key => $value) {
    //         $lead_details[$key]->lead_owner = User::where('id', $value->lead_owner)->value('username');
    //     }
    //     return response()->json($lead_details, 200);
    // }

    function all_leads(Request $request)
    {
        ini_set('memory_limit', '-1');
        $advSearchIndex = $request->advSearchIndex;
        $state_province = $request->state_province;
        $city_distt = $request->city_distt;
        $lead_status = $request->lead_status;
        // if($request->users_role=="5"){
        //     $lead_details = Leaddetail_api::whereStatus(2)->whereOrg_id(Auth::user()->org_id)->where('owner_id',$request->team_head_id);
        // }
        $lead_details = Leaddetail_api::whereStatus(2)->whereOrg_id(Auth::user()->org_id)->where('owner_id',$request->user_id);
        if (!empty($advSearchIndex)) {
            $lead_details = $lead_details->where(function ($q) use ($advSearchIndex) {
                $q->where('first_name', 'like', "%$advSearchIndex%");
                $q->orWhere('last_name', 'like', "%$advSearchIndex%");
                $q->orWhere('email', 'like', "%$advSearchIndex%");
                $q->orWhere('company_name', 'like', "%$advSearchIndex%");
                $q->orWhere('address1', 'like', "%$advSearchIndex%");
                // $q->orWhere('state_province', 'like', "%$advSearchIndex%");
            });
        }
        if (!empty($state_province)) {
            $lead_details = $lead_details->where(function ($q) use ($state_province) {
                $q->orWhere('state_province', $state_province);
            });
        }
        if (!empty($lead_status)) {
            $lead_details = $lead_details->where(function ($q) use ($lead_status) {
                $q->orWhere('lead_status', $lead_status);
            });
        }
        if (!empty($city_distt)) {
            $lead_details = $lead_details->where(function ($q) use ($city_distt) {
                $q->orWhere('city_distt', $city_distt);
            });
        }

        $lead_details = $lead_details->paginate(20);
        foreach ($lead_details as $key => $value) {
            $lead_details[$key]->date_created =  date('d/m/Y', strtotime($value->created_at));
            $lead_details[$key]->owner_id = User::where('id', $value->owner_id)->value('username');
            $lead_details[$key]->lead_owner = User::where('id', $value->lead_owner)->value('username');
            $lead_details[$key]->city_distt = cities::where('city_id', $value->city_distt)->value('city_name');
            $lead_details[$key]->state_province = state::where('state_id', $value->state_province)->value('state_name');
            $lead_details[$key]->country = countries::where('country_id', $value->country)->value('country_name');
        }
        return response()->json($lead_details, 200);
    }

    function getAllContactsList(Request $request)
    {
        
        ini_set('memory_limit', '-1');
        $advSearchIndex = $request->advSearchIndex;
        $state_province = $request->state_province;
        $city_distt = $request->city_distt;
        $lead_status = $request->lead_status;
    
        $lead_details = Leaddetail_api::whereStatus(1)->whereOrg_id(Auth::user()->org_id)->where('owner_id',$request->user_id);;
        if (!empty($advSearchIndex)) {
            $lead_details = $lead_details->where(function ($q) use ($advSearchIndex) {
                $q->where('first_name', 'like', "%$advSearchIndex%");
                $q->orWhere('last_name', 'like', "%$advSearchIndex%");
                $q->orWhere('email', 'like', "%$advSearchIndex%");
                $q->orWhere('company_name', 'like', "%$advSearchIndex%");
                $q->orWhere('address1', 'like', "%$advSearchIndex%");
                // $q->orWhere('state_province', 'like', "%$advSearchIndex%");
            });
        }
        if (!empty($state_province)) {
            $lead_details = $lead_details->where(function ($q) use ($state_province) {
                $q->orWhere('state_province', $state_province);
            });
        }
        if (!empty($lead_status)) {
            $lead_details = $lead_details->where(function ($q) use ($lead_status) {
                $q->orWhere('lead_status', $lead_status);
            });
        }
        $lead_details = $lead_details->paginate(20);
        foreach ($lead_details as $key => $value) {
            $lead_details[$key]->date_created =  date('d/m/Y', strtotime($value->created_at));
            $lead_details[$key]->owner_id = User::where('id', $value->owner_id)->value('username');
            $lead_details[$key]->lead_owner = User::where('id', $value->lead_owner)->value('username');
            $lead_details[$key]->city_distt = cities::where('city_id', $value->city_distt)->value('city_name');
            $lead_details[$key]->state_province = state::where('state_id', $value->state_province)->value('state_name');
            $lead_details[$key]->country = countries::where('country_id', $value->country)->value('country_name');
        }
        return response()->json($lead_details, 200);
        
    }
    function FetchTelecallerDataByOwnerId()
    {
        ini_set('memory_limit', '-1');
        $contacts_list = Leaddetail_api::whereStatus(2)->whereOrg_id(Auth::user()->org_id)->paginate(1);
        foreach ($contacts_list as $key => $value) {
            $contacts_list[$key]->lead_owner = User::where('id', $value->lead_owner)->value('username');
            $contacts_list[$key]->date_created = date('d/m/Y',strtotime($value->created_at));
        }
        return response()->json($contacts_list, 200);
    }
    function leads()
    {
        $user = JWTAuth::user();
        $lead_details = Leaddetail_api::whereStatus(2)->whereOrg_id(Auth::user()->org_id)->paginate(20);
        foreach ($lead_details as $key => $value) {
            $lead_details[$key]->date_created =  date('d/m/Y', strtotime($value->created_at));
            $lead_details[$key]->owner_id = User::where('id', $value->owner_id)->value('username');
            $lead_details[$key]->lead_owner = User::where('id', $value->lead_owner)->value('username');
            $lead_details[$key]->city_distt = cities::where('city_id', $value->city_distt)->value('city_name');
            $lead_details[$key]->state_province = state::where('state_id', $value->state_province)->value('state_name');
            $lead_details[$key]->country = countries::where('country_id', $value->country)->value('country_name');
        }
        return response()->json($lead_details, 200, compact('user'));
        // return response()->json( Leaddetail_api::paginate(5), 200 );
        // return response()->json( Leaddetail_api::get(), 200 );
    }
    function searchByLeadStatus(Request $request)
    {
        ini_set('memory_limit', '-1');
        $lead_details = Leaddetail_api::whereOrg_id(Auth::user()->org_id);


        if ($request->lead_status) {
            $lead_details = $lead_details->where('lead_status', $request->lead_status);
        }
        if ($request->lead_owner) {
            $lead_details = $lead_details->where('lead_owner', $request->lead_owner);
        }

        $lead_details = $lead_details->paginate(20);
        // var_dump($request->post('lead_status'));
        // return response()->json( Leaddetail_api::where(['lead_status' =>post('lead_status')] )->get(), 200  );
        // return response()->json(['status' => true, 'data' => $lead_details], 200);
        return response()->json($lead_details, 200);
    }

    function searchByLeadOwner(Request $request)
    {
        ini_set('memory_limit', '-1');
        // var_dump($request->post('lead_owner'));
        return Leaddetail_api::where(['lead_owner' => $request->post('lead_owner')])->whereOrg_id(Auth::user()->org_id)->paginate(25);

        //     // return response()->json( Leaddetail_api::where(['lead_status' =>post('lead_status')] )->get(), 200  );
        //     // return response()->json( Leaddetail_api::get(), 200 );
    }



    public function leadById($id)
    {
        $lead_details = Leaddetail_api::find($id);
        Session::put('telecaller_lead_id', $id);
        
        $lead_details->lead_owner = User::where('id', $lead_details->lead_owner)->value('username') ?? "";
        $lead_details->city_distt = cities::where('city_id', $lead_details->city_distt)->value('city_name') ?? "";
        $lead_details->state_province = state::where('state_id', $lead_details->state_province)->value('state_name') ?? "";
        $lead_details->country = countries::where('country_id', $lead_details->country)->value('country_name') ?? "";
        $lead_details->date_created = date('d/m/Y',strtotime($lead_details->created_at));
        return response()->json($lead_details, 200);
        //200 is response code
    }

    public function leadSave(Request $request) //now we can create object
    {
        

        $user_id= $request->user_id;
        $team_head_id= $request->team_head_id;
        $users_role= $request->users_role;
        $lead = $request->all();
        $lead['created_by'] = $request->user_id;
        if($users_role =='5'){
        $lead['owner_id'] = $request->team_head_id;
           }
           else{
            $lead['owner_id'] =  $request->user_id;
           }
           Leaddetail_api::create($lead);
        //    $lead->save();
        return response()->json(['status' => true, 'message' => "Lead Details Added", "data" => $lead], 201);
    }

    public function leadUpdate(Request $request, Leaddetail_api $lead)
    {

        $lead->update($request->all());

        return response()->json(['status' => true, 'message' => "Leade Details Updated", "data" => $lead], 200);
    }

    public function leadDelete(Request $request, Leaddetail_api $lead)
    {
        $lead->delete();
        return response()->json(null, 204);
    }
    public function updateContactToLead(Request $request)
    {
        $id = $request->contact_id;
        $lead_update = Leaddetail_api::whereId($id)->first();
        if (@$lead_update) {
            $lead_update->status = 2;
            //   $lead_update->updated_by=1;//Auth User Id
            //   $lead_update->updated_at=date('Y-m-d H:i::s');//Updated Time 
            //   $lead_update->is_lead=1;
            $lead_update->save();
            return response()->json(['status' => true, "message" => "This Contact Details is Upgrade In Leads"], 200);
        } else {
            return response()->json(["status" => false, "message" => "Something Went Wrong Please try again"], 200);
        }
    }
    public function listContact()
    {
        $contact_list = Leaddetail_api::whereOrg_id(Auth::user()->org_id)->whereStatus(1)->get();
        return response()->json($contact_list);
    }

    public function AssignLeadToSalePerson(Request $request)
    {
        $lead_id = $request->lead_id;

        $sales_people_id = $request->sales_people_id;
        if ($lead_id == "" || $sales_people_id == "") {
            return response()->json(['status' => false, 'message' => "Please Select Lead And Sales People"], 501);
        }
        if ($lead_id != "") {
            if (is_array($lead_id)) {
                $lead_id_array = $lead_id;
                if (count($lead_id_array) > 0) {
                    foreach ($lead_id_array as $key_lead => $value_lead) {
                        DB::Table('leads')->where('id', $value_lead)->update(['lead_owner' => $sales_people_id,'owner_id'=>$sales_people_id]);
                    }
                }
            } else {
                $lead_id_array = explode(",", $lead_id);
                if (is_array($lead_id_array)) {
                    foreach ($lead_id_array as $key_lead => $value_lead) {
                        DB::Table('leads')->where('id', $value_lead)->update(['lead_owner' => $sales_people_id,'owner_id'=>$sales_people_id]);
                    }
                } else {
                    DB::Table('leads')->where('id', $lead_id_array)->update(['lead_owner' => $sales_people_id,'owner_id'=>$sales_people_id]);
                }
            }
        }
        return response()->json(['status' => true, 'message' => "Leads is Assign Done"], 200);
    }
    public function AssignContactsToSalePerson(Request $request)
    {
        $contact_id = $request->contact_id;
        $sales_people_id = $request->sales_people_id;
        if ($contact_id == "" || $sales_people_id == "") {
            return response()->json(['status' => false, 'message' => "Please Select Contact And Sales People"], 501);
        }
        if ($contact_id != "") {
            $contact_id_array = $contact_id;
            if (is_array($contact_id)) {
                foreach ($contact_id_array as $key_lead => $value_lead) {
                    DB::Table('leads')->where('id', $value_lead)->update(['lead_owner' => $sales_people_id,'owner_id'=>$sales_people_id]);
                }
            } else {

                $contact_id_array = explode(",", $contact_id);
                if (is_array($contact_id_array)) {
                    foreach ($contact_id_array as $key_lead => $value_lead) {
                        DB::Table('leads')->where('id', $value_lead)->update(['lead_owner' => $sales_people_id,'owner_id'=>$sales_people_id]);
                    }
                } else {
                    DB::Table('leads')->where('id', $contact_id_array)->update(['lead_owner' => $sales_people_id,'owner_id'=>$sales_people_id]);
                }
            }
        }
        return response()->json(['status' => true, 'message' => "Contacts is Assign Done"], 200);
    }
    public function getUserDetail()
    {
        $user_details = Auth::user();
        if ($user_details != "") {
            $user_details->profile_img = "https://www.paatham.in/sales_baba/public/images/profile_pic/" . $user_details->profile_img ?? "";
            return response()->json(['status' => true, 'data' => $user_details], 200);
        } else {
            return response()->json(['status' => false], 200);
        }
    }
    public function addAllLeadActivity(Request $request)
    {
        $lead_id = Session::get('telecaller_lead_id');
        return $request->lead_id;

        $validator = Validator::make($request->all(), [
            'activity_type' => 'required',
            'activity_name' => 'required',
            'call_stage_id' => 'required',
            'response_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, "message" => $validator->errors()], 200);
        }
        $leads_activity = new leads_activity();
        $leads_activity->lead_id = $lead_id;
        $leads_activity->org_id = $request->org_id;
        $leads_activity->activity_type = $request->activity_type;
        $leads_activity->activity_name = $request->activity_name;
        $leads_activity->call_duration = $request->call_duration ?? "";
        $leads_activity->call_stage_id = $request->call_stage_id;
        $leads_activity->response_id = $request->response_id;
        $leads_activity->comment = $request->comment;
        $leads_activity->status = 1;
        $leads_activity->created_by = $request->user_id;
        $leads_activity->updated_by = $request->user_id;
        $leads_activity->updated_at = date('Y-m-d H:i:s');
        $leads_activity->save();
        return response()->json(['status' => true, 'data' => $leads_activity, 'message' => 'Your Lead Activity is Added'], 200);
    }
    public function addLeadsReminder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'reminder_type' => 'required',
            'reminder_date' => 'required',
            'reminder_time' => 'required',
            'comment' => 'required',
            'lead_id' => 'required',
            'user_id' => 'required',
            'org_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, "message" => $validator->errors()], 200);
        }
        $leads_reminders = new LeadReminder_Api();
        $leads_reminders->reminder_type = "notification";
        $leads_reminders->reminder_date = date('Y-m-d', strtotime($request->reminder_date));
        $leads_reminders->reminder_time = $request->reminder_time;
        $leads_reminders->comment = $request->comment;
        // $leads_reminders->updated_time = $request->updated_time;

        $leads_reminders->lead_id = $request->lead_id;
        $updated_time = date('Y-m-d H:i:s', strtotime($request->reminder_date . $request->reminder_time));
        $linux_time = strtotime($updated_time) - $request->reminder_before_time * 60;

        $leads_reminders->updated_time = date('Y-m-d H:i:s', $linux_time);
        $leads_reminders->org_id = $request->org_id;
        $leads_reminders->status = 1;
        $leads_reminders->created_by = $request->user_id;
        $leads_reminders->reminder_before_time = $request->reminder_before_time;
        $leads_reminders->updated_by = $request->user_id;
        $leads_reminders->updated_at = date('Y-m-d');
        $leads_reminders->save();
        return response()->json(['status' => true, 'data' => $leads_reminders, 'message' => 'Your task is Schdule For Next ' . date('d/m/Y', strtotime($request->reminder_date))], 200);
    }

    public function FetechLeadActivity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, "message" => $validator->errors()], 200);
        }
        $fetch_Activity_details = leads_activity::where('lead_id', $request->lead_id)->where('org_id', Auth::user()->org_id)->paginate(20);
        return response()->json(['status' => true, 'data' => $fetch_Activity_details], 200);
    }
    public function sendRemiderMail(Request $request)
    {
        $data = array();
        Mail::send('emails.reminder_email', ['data' => $data], function ($message) use ($data) {
            $message->to("amit2019.itscient@gmail.com");
            $message->from("amit2019.itscient@gmail.com", "Amit");
        });
        return " Done";
    }

    public function GetAllDashboarData(Request $request)
    {
        $toReturn = [];
        $toReturn['TotalLead'] = Leaddetail_api::whereStatus(2)->whereOrg_id($request->org_id)->count() ?? 0;
        $toReturn['TotalContact'] = Leaddetail_api::whereStatus(1)->whereOrg_id($request->org_id)->count() ?? 0;
        $toReturn['TotalDemo'] = Leaddetail_api::whereStatus(4)->whereOrg_id($request->org_id)->count() ?? 0;
        $toReturn['Closure'] = Leaddetail_api::whereStatus(5)->whereOrg_id($request->org_id)->count() ?? 0;
        return response()->json(['status' => true, 'data' => $toReturn], 200);
    }

    public function NotificationReminder()
    {
        // $current_date = date('2020-08-14 15:16:00');
        $current_date = date('Y-m-d H:i:s');
        $getNotificationTime = LeadReminder_Api::where('updated_time', $current_date)->get();
        return $getNotificationTime;
    }

    // //for notification table
    // public function


}
