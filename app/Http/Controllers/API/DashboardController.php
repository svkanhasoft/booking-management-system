<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
    public $successStatus = 200;

    public function dashboard(Request $request, $year)
    {


        $objUser = new User();
        $data['monthly_details'] = $objUser->getDashboard('monthly_details', $year);
        $data['yearly_details'] = $objUser->getDashboard('yearly_details');

        $data['today'] = $objUser->getDashboard('today');
        $data['week'] = $objUser->getDashboard('week');
        $data['month'] = $objUser->getDashboard('month');
        $data['year'] = $objUser->getDashboard('year');
        $data['total_user'] = $objUser->getDashboard('total_user');
        $data['block_user'] = $objUser->getDashboard('block_user');

        return response()->json(['message' => 'Dashboard detaild get successfully', 'status' => true, 'data' => $data], $this->successStatus);
    }
    
    /**
     * [DOWNLOAD CSV FOR DASHBOARD]
     *
     * @param   Request  $request  [$request description]
     * @param   [INTEGER]   $year     [$year description]
     *
     * @return  [array json]             [return description]
     */
    public function dashboardCSV(Request $request, $year)
    {

        $objUser = new User();
        $data['monthly_details'] = $objUser->getDashboardCSVData('monthly_details', $year);
        $data['yearly_details'] = $objUser->getDashboardCSVData('yearly_details');
 
        $data['today'] = $objUser->getDashboardCSVData('today');
        $data['week'] = $objUser->getDashboardCSVData('week');
        $data['month'] = $objUser->getDashboardCSVData('month');
        $data['year'] = $objUser->getDashboardCSVData('year');
        $data['total_user'] = $objUser->getDashboardCSVData('total_user',$year);
        $data['block_user'] = $objUser->getDashboardCSVData('block_user',$year);
     
        $fileName =  $year.'-report.csv';
        try {

            if (count($data) > 0) {
                $headers = array(
                    "Content-type"        => "text/csv",
                    "Content-Disposition" => "attachment; filename=$fileName",
                    "Pragma"              => "no-cache",
                    "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                    "Expires"             => "0"
                );
                $row = [];
                $columns = ['Total User','Total Block User','January','February','March','April','May','June','July ','August','September','October','November','December'];
                $file = fopen($fileName, 'w');
                $yearColumn = [];
                $yearValue = [];
                foreach($data['yearly_details'] as $key => $vals){
                    $row['Year '.$vals[0]]  = $vals[1];
                    $yearColumn[$key] ='Year '. $vals[0];
                }
                $columns = array_merge($yearColumn, $columns);
               
                fputcsv($file, $columns);
                $row['Total User'] = $data['total_user'];
                $row['Total Block User'] = $data['block_user'];
        
                foreach ($data['monthly_details'] as $val) {
                    $row[$val[0]]  = $val[1];
                }
                fputcsv($file, $row);
                fclose($file);
                $filePath = url('/' . $fileName);
                rename(public_path() . '/' . $fileName, public_path() . '/uploads/org_csv/' . $fileName);
                $filePath = public_path() . '/uploads/org_csv/' . $fileName;
                $filePath =  url('/') . '/uploads/org_csv/' . $fileName;

                return response()->json(['status' => true, 'message' => 'Report Generated Successfully. ', 'data' => $filePath], $this->successStatus);
            } else {
                return response()->json(['message' => 'Sorry, Report could not be Generated!', 'status' => false], $this->successStatus);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'status' => false], $this->successStatus);
        }
    }
}
