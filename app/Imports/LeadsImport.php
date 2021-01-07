<?php

namespace App\Imports;

use App\Leaddetail_api;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LeadsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // return new Leaddetail_api([
        //     'first_name' => $row[0],
        //     'company_name' => $row[1],
        //     'country' => $row[2],
        //     'state_province' => $row[3],
        //     'city_distt' => $row[4],
        //     'phone' => $row[5],
        //     'email' => $row[6],
        //     //   'website'=> $row[7],
        //     //   'n_o_employee'=> $row[8],
        //     //   'industry'=> $row[9],
            //   'linkedIn_id'=> $row[10],
            //   'linkedIn_url'=> $row[11],
            //   'lead_source'=> $row[12],
            //  'lead_status'=> $row[13],
            //  'lead_owner'=> $row[14],
            //  'annual_revenue'=> $row[15],
            //  'rating'=> $row[16],
            //  'address1'=> $row[17],
            //  'address2'=> $row[18],
            //  'head_quater'=> $row[19],
            //  'apperance_in_country'=> $row[20],
            //  'city_distt'=> $row[21],
            //  'country'=> $row[22],
            //  'state_province'=> $row[23],
            //  'pincode'=> $row[24],
            //  'description'=> $row[25],

      //  ]);
    }
}
