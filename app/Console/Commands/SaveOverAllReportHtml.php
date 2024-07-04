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
use App\Models\Project;
use App\Models\ItemDescriptionMaster;
use App\Models\OverallReportMail;
use Mail;
use Carbon\Carbon;
use App\Mail\sendReportToEmail;
use PDF;
use DB;
class SaveOverAllReportHtml extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'save:overall-report-html';

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
        $date = date('Y-m-d', strtotime(date('Y-m-d') . ' -1 day'));
        $itemDescriptions = ItemDescriptionMaster::orderBy('orderno','ASC')->get();
        $dprList =[];
        $itemData =[];
        foreach ($itemDescriptions as $value) {
            // Decode the JSON data from the column into a PHP array
             $alldpr = DprImport:: select('dpr_configs.*','dpr_imports.*', 'dpr_imports.id as id','dpr_configs.id as dpr_config_id','projects.id as project_id','projects.orderno as orderno')
            ->join('dpr_configs','dpr_imports.dpr_config_id','dpr_configs.id')
            ->join('projects','dpr_configs.project_id','projects.id')
            ->where('dpr_imports.item_desc',$value->title)
            ->where('projects.status','1')
            ->groupBy('dpr_configs.project_id')
            ->orderBy('projects.orderno', 'ASC');
            
            
            $alldpr = $alldpr->get();
            
            foreach ($alldpr as $key => $dpr) {

             $allData = DprConfig::select('dpr_configs.*', 'dpr_imports.*','dpr_imports.sheet_json_data as sheet_json_data',DB::raw("MAX(dpr_imports.data_date) as data_date"),DB::raw("MAX(dpr_imports.id) as id"), 'dpr_configs.id as dpr_config_id', 'dpr_configs.vendor_id as vendor_id')
                ->join('dpr_imports', 'dpr_configs.id', 'dpr_imports.dpr_config_id')
                ->where('dpr_imports.item_desc', $value->title)
                ->where('dpr_configs.project_id', $dpr->project_id)
                ->whereDate('dpr_imports.data_date', '<=', $date)
                ->whereHas('project',function($q){
                      $q->where('status', 1);
                    })
                ->groupBy('dpr_configs.vendor_id')
                ->orderBy('dpr_imports.data_date','DESC');
                    
                   
                $is_dpr_submit = true;
                $is_this_month_submit = true;
                
                $allData = $allData->get();

                $mergedArray = [];
                $extaraData = [];
                foreach ($allData as $key => $data) {
                    $getData = \DB::table('dpr_imports')->where('id',$data->id)->first();
                       
                    if(date('Y-m-d',strtotime($date)) != date('Y-m-d',strtotime(@$getData->data_date))){
                        $is_dpr_submit = false;
                    }
                    if(date('Y-m',strtotime($date)) != date('Y-m',strtotime(@$getData->data_date))){
                        $is_this_month_submit = false;
                    }
                    $jsonData = json_decode(@$getData->sheet_json_data, true);
                    $extaraData["vendor_name"] = @$data->vendor->name;
                    $extaraData["project_name"] = @$data->Project->name;
                    $extaraData["project_status"] = @$data->Project->status;
                    $extaraData["file_name"] = @$dpr->dprManage
                        ->original_import_file;
                    $extaraData['is_dpr_submit'] = $is_dpr_submit;
                    $extaraData['is_this_month_submit'] = $is_this_month_submit;


                    if (env("APP_ENV", "local") === "production") {
                        $extaraData["original_csv"] = secure_url(
                            "api/file-access/import/" .
                                @$dpr->dprManage->original_import_file
                        );
                    } else {
                        $extaraData["original_csv"] = url(
                            "api/file-access/import/" .
                                @$dpr->dprManage->original_import_file
                        );
                    }

                    $mergeExtraCol = array_merge($jsonData, $extaraData);
                    $mergedArray[] = $mergeExtraCol;
                }

                $itemData[] = [
                    'id' => @$dpr->id,
                    'project_name' => @$dpr->dprConfig->Project->name,
                    'project_status' => @$dpr->dprConfig->Project->status,
                    'sheet_name' => @$dpr->dprConfig->sheet_name,
                    'profile_name' => @$dpr->dprConfig->profile_name,
                    'vendor_name' => @$dpr->dprConfig->vendor->name,
                    'work_item' => $dpr->item_desc,
                    'color_code' => $dpr->color_code,
                    'dpr_color' => $dpr->dpr_color,
                    'data' => $mergedArray,
                    'dpr_config_id' => $dpr->dpr_config_id,
                    'data_date' => $date,
                    'is_dpr_submit' => $is_dpr_submit,
                     'is_this_month_submit' => $is_this_month_submit,
                        //'file_name' => @$dpr->dprManage->original_import_
                    //'file_name' => @$dpr->dprManage->original_import_file,
                    //'original_csv' => url('user/import/'.@$dpr->dprManage->original_import_file.''),
                ];
            }

            $dprList[] = [
                "date" => $date,
                "work_item" => $value->title,
                "unit_of_measure" => $value->unit_of_measure,
                "item_data" => $itemData,
                "dpr_report_emails" => $value->dpr_report_emails,
            ];
            $itemData = [];
        }
       
        /* ---Save html function -----------------*/
        $type = "html";
        $FileName = 'dpr-report.html';
        $directoryPath = 'html';
        $originalFilePath = 'html/' . $FileName;
        // Check if the directory exists
        if (!Storage::disk('excel_uploads')->exists($directoryPath)) {
            // Create the directory if it doesn't exist
            Storage::disk('excel_uploads')->makeDirectory($directoryPath);
        }

        // Check if the old file already exists
        if (Storage::disk('excel_uploads')->exists($originalFilePath)) {
            $count = 1;
            $newFileName = 'dpr-report_' . $count . '.html';
            $newFilePath = 'html/' . $newFileName;
            // Check if the new filename already exists
            while (Storage::disk('excel_uploads')->exists($newFilePath)) {
                $count++;
                $newFileName = 'dpr-report_' . $count . '.html';
                $newFilePath = 'html/' . $newFileName;
            }
            // Rename the old file
            Storage::disk('excel_uploads')->move($originalFilePath, $newFilePath);
        }

        // Generate the content for the new HTML file
        $html = view('pdfview', compact('dprList', 'date', 'type'))->render();

        // Save the new HTML file
        \Storage::disk('excel_uploads')->put($originalFilePath, $html, 'public');

        // Generate the file access URL
        if (env('APP_ENV', 'local') === 'production') {
            $callApi = secure_url('api/file-access/' . $originalFilePath);
        } else {
            $callApi = url('api/file-access/' . $originalFilePath);
        }

        $path = Storage::path('public/' . $originalFilePath);


        
    }
}
