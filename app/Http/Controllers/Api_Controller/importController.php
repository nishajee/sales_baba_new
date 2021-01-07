<?php

namespace App\Http\Controllers\Api_Controller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class importController extends Controller
{
    public function bulkMovieUpload(Request $request)
{
   if ($request->hasFile('excel_file')) {
      $data = $request->file('excel_file');
      $data->move(('path'), $data->getClientOriginalName());
      //You can choose to validate file type. e.g csv,xls,xlsx.
    //   $file_url -> (('path'), $data->getClientOriginalName());
      Excel::import(new MovieImport, $file_url);
      return back()->with('success', 'Uploaded Successfully!');
   } else {
     return back()->with('error', "File is required");
   }
}
}
