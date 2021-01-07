<?php

namespace App\Http\Controllers\Api_Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Leaddetail_api;
use JWTAuth;
use App\User;
use App\contacts_reminders;
use App\contacts_activity;
use DB;
use Validator;
use Auth;
use App\Imports\ContactsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Countries;
use App\state;
use App\cities;


class ContactController extends Controller
{
    public function importcontacts(Request $request)
    {

        $contacts = [];
        if ($request->file('uploadFile')) {
            $contacts_excel_array = Excel::toArray(new ContactsImport, request()->file('uploadFile'));
            //return $contacts_excel_array;

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
                        $match_array[$key]['phone_no_message'] = "On row " . ($key + 2) . " Phone No. : " . $value['phone_no'] . ", is a String\Invalid.\n";
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
                        $match_array[$key]['phone_no_message'] = "On row " . ($key + 2) . " Phone No.: " . $value['phone_no'] . ", is a Long .\n";
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
                        $contacts->company_name =  $value['company_name'];
                        $contacts->country = countries::where('country_name', 'LIKE', '' . trim($value['country']) . '')->value('country_id');
                        $contacts->state_province = state::where('state_name', 'LIKE', '' . trim($value['state']) . '')->value('state_id');
                        $contacts->city_distt = cities::where('city_name', 'LIKE', '' . trim($value['city']) . '')->value('city_id');
                        $contacts->mobile =  $value['phone_no'];
                        $contacts->email =  preg_replace('/\s+/', '', trim(strtolower($value['email'])));
                        $contacts->org_id = $request->org_id;
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

        $filename = "public/format/error/contac-errorLog.txt";  // error file name /
        $myfile = fopen($filename, "w"); // open error file name by using fopen function /
        $txt = "Sales BaBa\n";
        $txt .= "----------------------------------------------------------------------------------------------------------------------------------\n";
        $txt .= "DATE: " . date('d/m/Y h:i A') . "\n";
        $txt .= "TOTAL  Contacts RECORD  COUNT: " . $total_record . "\n";
        $txt .= "TOTAL Success Contacts COUNT: " . $noOfSuccess . "\n";
        $txt .= "TOTAL Fails Contacts COUNT: " . $noOfFails . "\n";

        // $txt .=  $match_array['email_message'];
        // $txt .= $match_array['phone_no_message'];
        if ($match_array != "") {
            $txt .= "Contacts Already Exist in List" . "\n";

            foreach ($match_array as $key_match => $value_match) {
                if (isset($value_match['email_message']) || isset($value_match['phone_no_message']) || isset($value_match['country_message']) || isset($value_match['state_message']) || isset($value_match['city_message'])) {
                    $txt .= $value_match['email_message'] ?? '';
                    $txt .= $value_match['phone_no_message'] ?? '';
                    $txt .= $value_match['country_message'] ?? '';
                    $txt .= $value_match['state_message'] ?? '';
                    $txt .= $value_match['city_message'] ?? '';
                }
            }
            $txt .= "----------------------------------------------------------------------------------------------------------------------------------\n";
            if ($noOfFails == 0) {
                $txt .= "No Error Found";
            } else {
                $txt .= $ErrorTxt;
            }
        }
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
                return response()->json(['status' => true, 'message' => "Contact  data has error Please Check Error Log"], 200);
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
}
