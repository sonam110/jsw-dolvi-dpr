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
use Mail;
use Carbon\Carbon;
use App\Mail\sendReportToEmail;
use PDF;
use DB;
use App\Models\SendMailData;
use App\Models\User;
class DprReportMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "mail:dpr-report";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Command description";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = date('Y-m-d', strtotime(date('Y-m-d') . ' -1 day'));
        \Log::channel('automation_emails_log')->info('Dpr Report Mails:'.$date);

        $deleteOldMails = SendMailData::whereDate('created_at',$date)->delete();

        $countTodayMails = SendMailData::whereDate('created_at', date('Y-m-d'))->first();
        if(!$countTodayMails){
            $allProjectWiseUserEmails = \DB::table('projects')->select('dpr_report_emails')->whereNotNull('dpr_report_emails')->get();

            $emails =[];

            foreach ($allProjectWiseUserEmails as $key => $email) {
                $expodedEmails = explode(',', $email->dpr_report_emails);

                foreach ($expodedEmails as $key => $value) { 
                    $checkActiveUserExist  = checkActiveUserExist($value);
                    if($checkActiveUserExist == true){
                        $emails[] = $value;
                    }
                    
                }
            }
            $uniqueEmails = array_unique($emails);
            foreach ($uniqueEmails as $key => $semail) {
                $saveMail  = new SendMailData;
                $saveMail->email = $semail;
                $saveMail->save();
            }

        }
        $allEmails  = SendMailData::where('status','0')->orderBy('id','ASC')->get();
        

        foreach ($allEmails as $key => $dpr_email) 
        {
            $reCheckMailStatus = SendMailData::where('email',$dpr_email->email)->where('status','0')->whereDate('created_at',date('Y-m-d'))->first();
            if($reCheckMailStatus)
            {
                SendMailData::where('email',$dpr_email->email)->update(['status'=>'2']); //process
                $getAssigneProjects = \DB::table('projects')
                ->whereRaw("FIND_IN_SET('".$dpr_email->email."', dpr_report_emails)")
                ->where('status', '1')
                ->pluck('id');
                if(count($getAssigneProjects) > 0){
                    $getAssigneProjectName = \DB::table('projects')
                    ->whereRaw("FIND_IN_SET('".$dpr_email->email."', dpr_report_emails)")
                    ->where('status', '1')
                    ->pluck('name')->toArray();
                   
                    $packageName = implode(', ', $getAssigneProjectName);

                    $findUserItemDesc = userDetail($dpr_email->email);

                    $item_desc_ids = (!empty($findUserItemDesc)) ? explode(',',$findUserItemDesc->dpr_item_desc_ids) : NULL;
                    if(!empty($item_desc_ids)){
                        $itemDescriptions = ItemDescriptionMaster::whereIn('id',$item_desc_ids)->orderBy('orderno','ASC')->get();
                    } else{
                        $itemDescriptions = ItemDescriptionMaster::orderBy('orderno','ASC')->get();

                    }
                

                    $dprList =[];
                    $itemData =[];
                    foreach ($itemDescriptions as $value) 
                    {
                        // Decode the JSON data from the column into a PHP array
                        $alldpr = DprImport:: select('dpr_configs.*','dpr_imports.*', 'dpr_imports.id as id','dpr_configs.id as dpr_config_id','projects.id as project_id','projects.orderno as orderno')
                        ->join('dpr_configs','dpr_imports.dpr_config_id','dpr_configs.id')
                        ->join('projects','dpr_configs.project_id','projects.id')
                        ->whereIn('dpr_configs.project_id',$getAssigneProjects)
                        ->where('dpr_imports.item_desc_id',$value->id)
                        ->where('projects.status','1')
                        ->groupBy('dpr_configs.project_id')
                        ->orderBy('projects.orderno', 'ASC');
                        $findLatestData= NULL;
               
                        if(count($alldpr->get()) <=0){
                            $findLatestData =DprImport::select('dpr_configs.*','dpr_imports.*', 'dpr_imports.id as id','dpr_configs.id as dpr_config_id')
                        ->join('dpr_configs','dpr_imports.dpr_config_id','dpr_configs.id')
                        ->whereIn('dpr_configs.project_id',$getAssigneProjects)
                        ->where('dpr_imports.item_desc_id',$value->id)
                        ->whereDate('dpr_imports.data_date','<=',$date)
                        ->orderBy('data_date','DESC')->first();
                        
                        }
                        
                        if(!empty($findLatestData))
                        {
                            $alldpr->OrWhere('dpr_imports.id',$findLatestData->id);
                        } 
                       
                        $alldpr = $alldpr->get();

                        
                        foreach ($alldpr as $key => $dpr) {

                            $allData = DprConfig::select('dpr_configs.*', 'dpr_imports.*','dpr_imports.sheet_json_data as sheet_json_data',DB::raw("MAX(dpr_imports.data_date) as data_date"),DB::raw("MAX(dpr_imports.id) as id"), 'dpr_configs.id as dpr_config_id', 'dpr_configs.vendor_id as vendor_id')
                            ->join('dpr_imports', 'dpr_configs.id', 'dpr_imports.dpr_config_id')
                            ->where('dpr_imports.item_desc_id', $value->id)
                            ->where('dpr_configs.project_id', $dpr->project_id)
                            ->whereDate('dpr_imports.data_date', '<=', $date)
                            ->whereHas('project',function($q){
                                  $q->where('status', 1);
                                })
                            ->groupBy('dpr_configs.vendor_id')
                            ->orderBy('dpr_imports.data_date','DESC');
                            

                            
                            $allData = $allData->get();


                            $mergedArray = [];
                            $extaraData = [];
                            $is_dpr_submit = true;
                            $is_this_month_submit = true;
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

                    /* ---Mail function -----------------*/
                    $FileName = date("Y-m-d", strtotime($date)) . ".pdf";
                    $date = $date;
                    $type="pdfmail";
                    $packageName = $packageName;

                    $pdf = PDF::loadView("pdfview", compact("dprList", "date",'type','packageName'));
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
                    $email = $dpr_email->email;
                    SendMailData::where('email',$dpr_email->email)->update(['status'=>'0']); //reinit
                    if (env('IS_MAIL_ENABLE', false) == true) {
                        $recevier = Mail::to($email)->send(new sendReportToEmail($content));
                        if ($recevier) {
                            $updateMail = SendMailData::where('email',$email)->update(['status'=>'1']);
                            \Log::channel('automation_emails_log')->info($email);
                        }
                    }
                    sleep(1);
                }
            }

        }
        
    }
}
