<?php

namespace App\Imports;

use App\Movies;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MoviesImport implements ToCollection, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Movies|null
     */
    public function collection(Collection $rows)
    {
        $errorMsg = "";
        DB::beginTransaction();
        
        $i = 1;
        foreach ($rows as $row)
        {
            if ($row['title'] != null)
            {
                $data['title'] = $row['title'];
            }
            else
            {
                $errMsg = "Title is empty on SN $i";
                break;
            }
            //You can validate other values using same steps.
            $data['synopsis'] = $row['synopsis'];
            $data['release_date'] = $row['release_date'];
            if (!Movies::create($data))
            {
                $errMsg = "Error while creating movies";
                break;
            }
            $i++;
        }
        if (!empty($errorMsg))
        {
            DB::rollBack();
            // Rollback in case there is error
            return redirect()->back()->with('error', $errorMsg);
        }
        else
        {
            DB::commit();
            // Commit to database
            return redirect()->back()
                ->with('success', 'Uploaded Successfully');
        }
    }
}