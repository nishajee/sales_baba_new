<?php

namespace App\Http\Controllers\Api_Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Userdetail_api;
use Validator;

class user extends Controller
{
   function users()
   {
      return response()->json(Userdetail_api::where('users_role', 3)->paginate(20), 200);
      // return response()->json(Userdetail_api::get(),200);
   }
   public function usersById($id)
   {
      return response()->json(Userdetail_api::find($id), 200); //200 is response code
   }

   public function usersSave(Request $request) //now we can create object
   {
      $imageName="";
      if (@$request->file('profile_img') != '') {
         request()->validate([
             'profile_img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
         ]);
         $imageName = time() . '.' . request()->profile_img->getClientOriginalExtension();
         request()->profile_img->move(public_path('images/profile_pic'), $imageName);
     }
      if(@$request->user_id!="")
      {
         $users =Userdetail_api::find($request->user_id);
         $validator = Validator::make($request->all(), [
              'username' => 'required|string',
              'email' => 'required|string|email|max:100',
              'password' => 'required|min:6',
              'phone' => 'required',
              'designation' => 'required',
              'gender' => 'required',
              'pincode' => 'required',
              'address' => 'required',
          ]);
      }else
      {
         $users =new Userdetail_api();
         $validator = Validator::make($request->all(), [
              'username' => 'required|string|unique:users',
              'email' => 'required|string|email|max:100|unique:users',
              'password' => 'required|min:6',
              'phone' => 'required',
              'designation' => 'required',
              'gender' => 'required',
              'pincode' => 'required',
              'address' => 'required',
          ]);
      }
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
      $users->users_role=3;
      $users->org_id=1;
      $users->users_type=2;
      $users->username=$request->username;
      $users->name=$request->name;
      $users->password=$request->password;
      $users->email=$request->email;
      $users->phone=$request->phone;
      $users->designation=$request->designation;
      $users->gender=$request->gender;
      $users->profile_img=$imageName;
      $users->city=$request->city;
      $users->state=$request->state;
      $users->address=$request->address;
      $users->address2=$request->address2;
      $users->pincode=$request->pincode;
      $users->country=$request->country;
      $users->status=1;
      $users->save();
      return response()->json($users, 200); //200 is response code
   }
   public function usersUpdate(Request $request, Userdetail_api $users)
   {
      $users->update($request->all());
      return response()->json($users, 200);
   }
   public function usersDelete(Request $request, Userdetail_api $users)
   {
      $users->delete();
      return response()->json(null, 204);
   }
}
