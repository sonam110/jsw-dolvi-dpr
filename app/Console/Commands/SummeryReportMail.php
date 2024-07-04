<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Storage;
use File;
use App\Models\DprImport;
use App\Models\DprManage;
use App\Models\DprLog;
use App\Models\WorkPackage;
use App\Models\DprConfig;
use App\Models\AppSetting;
use App\Models\ItemDescriptionMaster;
use Mail;
use Carbon\Carbon;
use App\Mail\sendReportToEmail;
use PDF;
class SummeryReportMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:summery-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
            $currentDate = new \DateTime();
            $startDate = $currentDate->modify('-1 day');
            $endDate = new \DateTime();
            // Increment the end date by one day to include it in the range
            $endDate->modify('+1 day');

            $interval = new \DateInterval('P1D'); // 1 day interval
            $dateRange = new \DatePeriod($startDate, $interval, $endDate);
            $mergedArray  =[];
            $summeryReporr =[];
            $itemData =[];
            foreach ($dateRange as $date) {
                $date = $date->format('Y-m-d');
                $dprUploads = \DB::table('dpr_imports')->join('dpr_configs', function($join) use ($date) {
                    $join->on('dpr_configs.id', '=', 'dpr_imports.dpr_config_id');
                })
                ->join('dpr_manages', function($join) use ($date) {
                    $join->on('dpr_configs.id', '=', 'dpr_manages.dpr_config_id');
                })
                ->join('work_packages', function($join) use ($date) {
                    $join->on('dpr_configs.work_pack_id', '=', 'work_packages.id');
                })
                ->join('dpr_logs', function($join) use ($date) {
                    $join->on('dpr_configs.id', '=', 'dpr_logs.dpr_config_id')
                        ->on('dpr_imports.id', '=', 'dpr_logs.dpr_import_id');
                })
                ->select(array('dpr_configs.*','dpr_manages.*','work_packages.id as work_pack_id','work_packages.name as work_pack_name','dpr_logs.import_file'))
                ->whereDate('dpr_imports.data_date',$date)
                ->groupBy('dpr_imports.random_no')
                ->groupBy('dpr_logs.random_no');
               
                $dprUploads = $dprUploads->get();
                $date_a = date('d.m.Y',strtotime($date));
                $org_date = date('Y-m-d',strtotime($date));
                foreach ($dprUploads as $key => $upload) {
                    if(env('APP_ENV', 'local')==='production')
                    {
                        $original_csv =secure_url('api/file-access/import/'.@$upload->import_file);
                    }
                    else
                    {
                        $original_csv =url('api/file-access/import/'.@$upload->import_file);
                    }
                   
                    $work_date_name = (!empty($upload)) ? ''.@$upload->work_pack_name.' '.$date_a.'' :'Not Submitted';
                    $link =  '<a href="'.$original_csv.'">'.$work_date_name.'</a>';
                    $name_link = (!empty($upload)) ? $link :'Not Submitted';
                    $dpr_link = (!empty($upload)) ? $original_csv : NULL;
                    $itemData[] = [
                        "date" => $org_date,
                        "name" =>  $work_date_name,
                        "link" =>  $name_link

                    ];
                   
                }
                $summeryReporr[]= [
                    "item_data" =>$itemData,
                    "date"=>$date,
                  
                ];
                $itemData = [];

                
                
        }

        if(count($summeryReporr) >0){
            $FileName = date("Y-m-d") . ".pdf";
            $date = $date;
            $pdf = PDF::loadView("summeryview", compact("summeryReporr"));
            $FilePath = "pdf/" . $FileName;
            \Storage::disk("excel_uploads")->put(
                $FilePath,
                $pdf->output(),
                "public"
            );

            if (env("APP_ENV", "local") === "production") {
                $callApi = secure_url("api/file-access/" . $FilePath);
            } else {
                $callApi = url("api/file-access/" . $FilePath);
            }
            $path = Storage::path("public/" . $FilePath);
            $mime = "application/pdf";
            $content = [
                "FileName" => $FileName,
                "FilePath" => $path,
                "mime" => $mime,
            ];
            $email = "sonam.patel@nrt.co.in";
            $recevier = Mail::to($email)->send(new sendReportToEmail($content));
        }
        \Log::info($summeryReporr);
    }
}
