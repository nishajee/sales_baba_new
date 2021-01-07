<?php

namespace App\Http\Controllers\Api_Controller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Response;
use App\Countries;
use App\cities;
use App\state;
use App\subscription;
use App\User;
use DB;
use App\Company;
use Hash;
use Auth;

class OrganizationController extends Controller
{
	public function store(Request $request)
	{
		// return "test";
		//  $validatedData = $this->validate($request,[
		//        'username' => 'required',
		//       //  'project_location' => 'required',
		//        'email' => 'required',
		//        'company_name' => 'required',
		//        'address' => 'required',
		//        'org_code' => 'required',
		//        'org_name' => 'required',
		//        'contact_no' => 'required',
		//        'city_id' => 'required',
		//         'state_id' =>'required',
		//        'country_id' =>'required',
		//        'zipcode' => 'required',
		//        'org_type' =>'required',
		//      ]);

		// if ($validator->fails()) {
		//     return response()->json($validator->errors(), 422);
		// }
		$messages = "";
		$imageName = "";
		if ( ($request->username == "") || ($request->org_name == "") || ($request->email == "") || ($request->address == "")) {
			return response()->json(['status' => false, 'message' => "Please Fill All Required Field"], 200);
		}
		if (@$request->file('photo') != '') {
			request()->validate([
				'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
			]);
			$imageName = time() . '.' . request()->photo->getClientOriginalExtension();
			request()->photo->move(public_path('images/company'), $imageName);
		}
		if (@$request->org_id != "") {

		} else {
			$company_exist = Company::where('org_name', trim($request->org_name))->orWhere('org_code', trim($request->org_code))->first();
			if ($company_exist != "") {
				return response()->json(['status' => false, 'message' => "This Organization name OR code is Already Exist "], 200);
			}
			$user_exit = User::where('username', trim($request->username))->orWhere('email', trim($request->email))->first();
			if (@$user_exit != "") {
				return response()->json(['status' => false, 'message' => "This username OR Email is Already Exist"], 200);
			}
		}
		if (@$request->org_id != "") {
			$orgData = Company::find($request->org_id);
			$messages = "Organization Details Updated ";
		} else {
			$orgData = new Company();
			$orgData->created_by =  1;
			$messages = " Organization Details Added ";
		}

		//  User Login Details Section
		if ($request->username != '') {
			if ($orgData->user_id != "") {
				$logindata = User::find($orgData->user_id);
			} else {
				$logindata = new User();
				$logindata->password = Hash::make($request->password);
				$logindata->created_by = 1;
			}
			$logindata->username = trim($request->username);
			$logindata->email = $request->email;
			$logindata->address = $request->address;
			$logindata->name = $request->org_name;
			$logindata->users_role = 2;
			$logindata->status = 1;
			$logindata->ip_address = "";
			$logindata->save();
		}

		// return " test";
		// Company Login Details
		$orgData->org_code = trim($request->org_code);
		$orgData->users_id = $logindata->id;
		$orgData->org_name = trim($request->org_name);
		$orgData->contact_no = $request->contact_no;
		$orgData->email =  $request->email;
		$orgData->address =  $request->address;
		$orgData->website =  $request->website;
		$orgData->city_id =  $request->city_id;
		$orgData->state_id =  $request->state_id;
		$orgData->country_id =  $request->country_id;
		$orgData->photo =  $imageName;
		$orgData->pincode = $request->pincode;
		$orgData->org_type =  $request->org_type;
		$orgData->pf_no =  $request->pf_no;
		$orgData->esic_no =  $request->esic_no;
		$orgData->tax_no =  $request->tax_no;
		$orgData->policy_no =  $request->policy_no;
		$orgData->gratuity_no =  $request->gratuity_no;
		$orgData->login_status =  $request->login_status;
		$orgData->status =  1;
		$orgData->ip_address =  $request->ip();
		$orgData->updated_at =  date('Y-m-d H:i:s');
		$orgData->modified_by =  1;
		$orgData->save();
		//update Company_id In User table;
		$logindata->org_id = $orgData->id;
		$logindata->save();
		//   Company::insert($orgData);
		return response()->json(['data' => $orgData, "status" => true, 'message' => $messages], 200);
	}
	public function list_org()
	{
		$orgdata = DB::table('org')->where('is_deleted', '=', 0)->whereStatus(1)->paginate(20);
		foreach ($orgdata as $key_org => $value_org) {
			$orgdata[$key_org]->city_id = cities::where('city_id', $value_org->city_id)->value('city_name');
			$orgdata[$key_org]->state_id = state::where('state_id', $value_org->state_id)->value('state_name');
			$orgdata[$key_org]->country_id = countries::where('country_id', $value_org->country_id)->value('country_name');
			$orgdata[$key_org]->user_name = User::where('id', $value_org->users_id)->value('username');
			$orgdata[$key_org]->photo = "https://www.paatham.in/sales_baba/public/images/company/" . $value_org->photo;
		}
		return response()->json($orgdata, 200);
	}
	public function fetch_org_data($id)
	{
		$cities = cities::orderBy('city_name', 'ASC')->get();
		$countries = countries::where('status', '=', 1)->get();
		$state = state::orderBy('state_name', 'ASC')->get();
		$data = Company::where('id', $id)->first();
		// $udata = User::where('id', $data->users_id)->first();
		@$data->username = User::where('id', $data->users_id)->value('username');
		@$data->user_id = User::where('id', $data->users_id)->value('id');
		
// 		@$data->created_at = date('d/m/Y',strtotime(@$data->created_at));
		if ($data) {
			return Response::json($data, 200);
		}
	}
	public function delete_org_data($id)
	{
		$data = Company::where('id', $id)->first();
		if ($data != "") {
			$data->is_deleted = 1;
			$data->is_deleted = 1;
			$data->save();
			return response()->json(["status" => true, "message" => "Organization Details Deleted"], 200);
		} else {
			return response()->json(["status" => false, "message" => "Something Went Wrong Please try again"], 200);
		}
	}
	public function get_country()
	{
		$countries = countries::where('status', '=', 1)->get();
		return Response::json($countries, 200);
	}
	public function getStateUsingCountry($country_id)
	{
		$state = state::orderBy('state_name', 'ASC')->where('country_id', $country_id)->get();
		return Response::json($state, 200);
	}
	function getCitiesUsingState($state_id)
	{
		$cities = cities::orderBy('city_name', 'ASC')->where('state_id', $state_id)->get();
		return Response::json($cities, 200);
	}

	
}
