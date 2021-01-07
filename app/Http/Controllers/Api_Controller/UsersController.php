<?php

namespace App\Http\Controllers\Api_Controller;

use App\designation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Userdetail_api;

use Validator;
use Auth;
use Hash;
use DB;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;

class UsersController extends Controller
{
    function users()
    {
        $user_details = Userdetail_api::where('users_role', 3)->where('org_id', Auth::user()->org_id)->paginate(20);
        foreach ($user_details as $key_user => $value_user) {
            $user_details[$key_user]->profile_img = "https://www.paatham.in/sales_baba/public/images/profile_pic/" . $value_user->profile_img ?? "";
            $user_details[$key_user]->date_created = date('d/m/Y',strtotime($value_user->created_at)) ?? "";
        }
        return response()->json($user_details, 200);
        // return response()->json(Userdetail_api::get(),200);
    }
    function list_sales_people()
    {
        $user_details = Userdetail_api::where('users_role', 3)->where('org_id', Auth::user()->org_id)->orderBy('id', 'DESC')->get();
        foreach ($user_details as $key_user => $value_user) {
            $user_details[$key_user]->profile_img = "https://www.paatham.in/sales_baba/public/images/profile_pic/" . $value_user->profile_img ?? "";
            $user_details[$key_user]->date_created = date('d/m/Y',strtotime($value_user->created_at)) ?? "";
        }
        return response()->json($user_details, 200);
        // return response()->json(Userdetail_api::get(),200);
    }
    public function usersById($id)
    {
        $user_details = Userdetail_api::find($id);
        if ($user_details->profile_img != "") {
            $user_details->profile_img = "https://www.paatham.in/sales_baba/public/images/profile_pic/" . $user_details->profile_img ?? "";
        } else {
            $user_details->profile_img = "";
        }
        $user_details->date_created = date('d/m/Y',strtotime($user_details->created_at)) ?? "";
        return response()->json($user_details, 200); //200 is response code
    }

    public function usersSave(Request $request, $user_id = "") //now we can create object
    {
        $imageName = "";
        if (@$request->file('profile_img') != '') {
            request()->validate([
                'profile_img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $imageName = time() . '.' . request()->profile_img->getClientOriginalExtension();
            request()->profile_img->move(public_path('images/profile_pic'), $imageName);
        }
        if ($user_id != "") {
            $users = Userdetail_api::find($request->user_id);
            $validator = Validator::make($request->all(), [
                //    
                'phone' => 'required',
                'designation' => 'required',
                'gender' => 'required',
                //   'pincode' => 'required',
                //   'address' => 'required',
            ]);
            $message = "Sales People Edited";
        } else {
            $users = new Userdetail_api();
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|unique:users',
                'email' => 'required|string|email|max:100|unique:users',
                'password' => 'required',
                'phone' => 'required',
                'designation' => 'required',
                'gender' => 'required',
                //   'pincode' => 'required',
                //   'address' => 'required',
            ]);
            $message = "Users Successfully Added";
        }
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->toJson(), 'status' => false], 200);
        }
        $users->team_head_id = $request->team_head_id;
        $users->users_role = $request->users_role;
        $users->org_id = Auth::user()->org_id;
        $users->users_type = 2;
        $users->username = $request->username;
        $users->name = $request->name;
        $users->password = Hash::make($request->password);
        $users->email = $request->email;
        $users->phone = $request->phone;
        $users->designation = $request->designation;
        $users->gender = $request->gender;
        if ($imageName != "") {
            $users->profile_img = $imageName;
        }
        $users->city = $request->city;
        $users->state = $request->state;
        $users->address = $request->address;
        $users->address2 = $request->address2;
        $users->pincode = $request->pincode;
        $users->country = $request->country;
        $users->status = 1;
        $users->save();
        return response()->json(['data' => $users, 'status' => true, 'message' => $message], 200); //200 is response code
    }
    public function usersUpdate(Request $request, Userdetail_api $users)
    {
        $users->update($request->all());
        return response()->json(['data' => $users, 'status' => true], 200);
    }
    public function usersDelete(Request $request, Userdetail_api $users)
    {
        $users->delete();
        return response()->json(null, 204);
    }
    
    public function updateUserDetails(Request $request, $user_id)
    {
        $imageName = "";
        if (@$request->file('profile_img') != '') {
            request()->validate([
                'profile_img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $imageName = time() . '.' . request()->profile_img->getClientOriginalExtension();
            request()->profile_img->move(public_path('images/profile_pic'), $imageName);
        }
        $validator = Validator::make($request->all(), [

            'email' => 'required|string|email|max:100',
            'phone' => 'required',
            'designation' => 'required',
            'gender' => 'required',
            'pincode' => 'required',
            'address' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->toJson(), 'status' => false], 200);
        }
        $user_details = Userdetail_api::find($user_id);
        $user_details->name = $request->name;

        $user_details->email = $request->email;
        $user_details->designation = $request->designation;
        $user_details->phone = $request->phone;
        $user_details->profile_img = $imageName;
        $user_details->gender = $request->gender;
        $user_details->city = $request->city;
        $user_details->state = $request->state;
        $user_details->address = $request->address;
        $user_details->pincode = $request->pincode;
        $user_details->country = $request->country;
        $user_details->save();
        return response()->json(['status' => true, 'message' => "Profile Updated"], 200);
    }

    
    
    public function fetch_team_head(Request $request)
    {
       
         $user_details = Userdetail_api::whereIn('users.designation', [3])
         ->leftJoin('designation','designation.id','=','users.designation')
         ->select('users.*', 'designation.designation as designation_name')
         ->where('users.org_id', $request->org_id)
         ->where('users.status', 1)
         ->orderBy('users.id', 'DESC')
         ->paginate(25);
         foreach ($user_details as $key_user => $value_user) {
            if($user_details[$key_user]->profile_img!="")
            {
                $user_details[$key_user]->profile_img = "https://www.paatham.in/sales_baba/public/images/profile_pic/" . $value_user->profile_img ?? "";
            }else
            {
                $user_details[$key_user]->profile_img = "";
            }
            $user_details[$key_user]->date_created = date('d/m/Y',strtotime($value_user->profile_img)) ?? "";
        }
        return response()->json($user_details, 200);
    }

    public function fetch_team_member_by_team_head(Request $request)
    {
        $user_details = Userdetail_api::whereIn('team_head_id', [$request->user_id,0])->whereNotIn('users.users_role',[1,2,3])->where('users.org_id', $request->org_id)
        ->leftJoin('designation','designation.id','=','users.designation')
         ->select('users.*', 'designation.designation as designation_name','users.created_at as date_created ')
        ->orderBy('users.id', 'DESC')->get();
        foreach ($user_details as $key_user_team => $value_user) {
            if($user_details[$key_user_team]->profile_img!="")
            {
                $user_details[$key_user_team]->profile_img = "https://www.paatham.in/sales_baba/public/images/profile_pic/" . $value_user->profile_img ?? "";
            }else
            {
                $user_details[$key_user_team]->profile_img = "";
            }
            $user_details[$key_user_team]->date_created = date('d/m/Y',strtotime($value_user->date_created)) ?? "";

            // echo $user_details[$key_user_team]->created_at;
            
        }
        return response()->json($user_details, 200);
    }
    public function fetch_team_member_for_telecalling_assign(Request $request)
    {
        $user_details = Userdetail_api::whereIn('team_head_id', [$request->user_id,0])->where('users.users_role',4)->where('users.org_id', $request->org_id)
        ->leftJoin('designation','designation.id','=','users.designation')
        ->select('users.*', 'designation.designation as designation_name','users.created_at as date_created ')
        ->orderBy('users.id', 'DESC')->get();
        foreach ($user_details as $key_user_team => $value_user) {
            if($user_details[$key_user_team]->profile_img!="")
            {
                $user_details[$key_user_team]->profile_img = "https://www.paatham.in/sales_baba/public/images/profile_pic/" . $value_user->profile_img ?? "";
            }else
            {
                $user_details[$key_user_team]->profile_img = "";
            }
            $user_details[$key_user_team]->date_created = date('d/m/Y',strtotime($value_user->date_created)) ?? "";

            // echo $user_details[$key_user_team]->created_at;
            
        }
        return response()->json($user_details, 200);
    }
    public function fetch_team_head_delete(Request $request)
    {
        $data = Userdetail_api::where('id', $request->id)->first();
        if ($data != "") {
            $data->status =0;
            $data->save();
            return response()->json(["status" => true, "message" => "Details Deleted Sucessfully"], 200);
        }
        else {
			return response()->json(["status" => false, "message" => "Something Went Wrong Please try again"], 200);
		}
}

public function advance_search_for_admin(Request $request)
    {
        $advSearchIndex = $request->advSearchIndex;
        $designation = $request->designation;

       $user_details= Userdetail_api::where('org_id', $request->org_id)->where('status', 1);
        if (!empty($advSearchIndex)) {
           $user_details= $user_details->where(function ($q) use ($advSearchIndex) {
                $q->where('users.name', 'like', "%$advSearchIndex%");
              
                $q->orWhere('users.email', 'like', "%$advSearchIndex%");
                $q->orWhere('users.phone', 'like', "%$advSearchIndex%");
            });
        }
        if (!empty($designation)) {
           $user_details= $user_details->where('users.designation', $designation);
         }

        else{
       $user_details= Userdetail_api::whereIn('users.designation', [3, 4, 5])
         ->leftJoin('designation','designation.id','=','users.designation')
         ->select('users.*', 'designation.designation as designation_name','users.created_at as date_created','users.profile_img as profile_img')
         ->where('users.org_id', $request->org_id)
         ->where('users.status', 1)
         ->orderBy('users.id', 'DESC');
         foreach ($user_details as $key_user_team => $value_user) {
            if($user_details[$key_user_team]->profile_img!="")
            {
                $user_details[$key_user_team]->profile_img = "https://www.paatham.in/sales_baba/public/images/profile_pic/" . $value_user->profile_img ?? "";
            }else
            {
                $user_details[$key_user_team]->profile_img = "";
            }
            $user_details[$key_user_team]->date_created = date('d/m/Y',strtotime($value_user->date_created)) ?? "";

            // echo $user_details[$key_user_team]->created_at;
            
        }
        }
       $user_details= $user_details->paginate(20);
        return response()->json($data, 200);
        
    }
}
