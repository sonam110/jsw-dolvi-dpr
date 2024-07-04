<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Vendor;
use App\Models\WorkPackage;
use App\Models\Project;
use App\Models\AppSetting;
use App\Models\DprImport;
use App\Models\ItemDescriptionMaster;
use Validator;
use Exception;
use App\Models\DprMap;
use App\Models\Notification;
use App\Models\DprManage;
use App\Models\DprLog;
use App\Models\DprConfig;
use Excel;
use PDF;
use Storage;
use App\Imports\DprReportImport;
use App\Imports\DprSelectSheet;
use Illuminate\Support\Facades\File;
use App\Exports\ReportExport;
use DB;
use Mail;
use App\Exports\CustomExport;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use Carbon\Carbon;
use App\Mail\sendReportToEmail;

class FrontController extends Controller
{
    public  function dashboard($access_key)
    {
        
        
        $auth_key = AppSetting::first();
        if($access_key ==  $auth_key->access_key){
            $projects =  Project::orderby('id','asc')->whereIn('status',['1','2'])->get();
            $vendors =  Vendor::orderby('id','desc')->get();
            $itemDescs =  ItemDescriptionMaster::orderby('id','asc')->get();
            $data['userCount'] = User::count();
            $data['totalProject'] = Project::whereIn('status',['1','2'])->count();
            $data['totalVendor'] = Vendor::count();
            $data['TotalWorkPackage'] = ItemDescriptionMaster::count();
            $data['dprUploads'] = DprImport::whereDate('created_at',date('Y-m-d'))->groupBy('random_no')->get()->count();
            return view('dashboard',compact('projects','vendors','itemDescs','data'));
        } else{
            return 'You are not authorized to access this page.';
        }
    }
    public  function dprReport($access_key)
    {
       
        if($access_key==''){
            return 'You are not authorized to access this page.';
        }
       
        $auth_key = AppSetting::first();
        if($access_key ==  $auth_key->access_key){
            $projects =  Project::orderby('id','asc')->whereIn('status',['1','2'])->get();
            $vendors =  Vendor::orderby('id','desc')->get();
            $itemDescs =  ItemDescriptionMaster::orderby('id','asc')->get();
            return view('dpr-report',compact('projects','vendors','itemDescs'));
        } else{
            return 'You are not authorized to access this page.';
        }
    }
    public  function getDprReport(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(),[ 
                'date'     => 'required',
            ]);
            if ($validator->fails()) {
                return response(prepareResult(true, $validator->messages(),$validator->messages()->first()), config('httpcodes.bad_request'));
            }
            $output = [];
            $data = [];
            $date = date('Y-m-d',strtotime($request->date));
            
            $query = ItemDescriptionMaster::leftJoin('dpr_imports', function($join) use ($request, $date) {
                $join->on('item_description_masters.id', '=', 'dpr_imports.item_desc_id');
                //->where('dpr_imports.data_date', '=', $date);
            })
            ->leftJoin('dpr_configs', function($join) use ($request) {
                $join->on('dpr_configs.id', '=', 'dpr_imports.dpr_config_id');
            })
            ->select('item_description_masters.*','dpr_configs.*', 'dpr_imports.*', 'item_description_masters.id as item_desc_id', 'dpr_imports.id as id','dpr_configs.id as dpr_config_id')
            ->groupBy('item_description_masters.title')
            ->orderBy('item_description_masters.orderno','ASC');

            if(!empty($request->item_desc))
            {
                $query = $query->where('dpr_imports.item_desc_id',$request->item_desc);
            }

            
            if(!empty($request->project_id))
            {
                $query = $query->where('dpr_configs.project_id',$request->project_id);
            }
            if(!empty($request->vender_id))
            {
                $query = $query->where('dpr_configs.vendor_id',$request->vender_id);
            }
            

            $query = $query->get();
            $dprList =[];
            $itemData =[];
    
            // Loop through each record
            //return $query;
            foreach ($query as $value) {
                // Decode the JSON data from the column into a PHP array
                $alldpr = DprImport::select('dpr_configs.*','dpr_imports.*', 'dpr_imports.id as id','dpr_configs.id as dpr_config_id','projects.id as project_id','projects.orderno as orderno')
                ->join('dpr_configs','dpr_imports.dpr_config_id','dpr_configs.id')
                ->join('projects','dpr_configs.project_id','projects.id')
                //->where('dpr_imports.data_date',$date)
                ->where('dpr_imports.item_desc_id',$value->item_desc_id)
                ->where('projects.status','1')
                //->whereMonth('dpr_imports.data_date',date('m',strtotime($date)))
                //->whereYear('dpr_imports.data_date',date('Y',strtotime($date)))
                ->groupBy('dpr_configs.project_id')
                ->orderBy('projects.orderno', 'ASC');
                
              
               
                 $findLatestData= NULL;
               
                if(count($alldpr->get()) <=0){
                    $findLatestData =DprImport::where('dpr_imports.item_desc_id',$value->item_desc_id)
  
                    ->whereDate('dpr_imports.data_date','<=',$date)
                    ->orderBy('data_date','DESC')->first();
                
                }
                
                if(!empty($findLatestData))
                {
                    $alldpr->OrWhere('dpr_imports.id',$findLatestData->id);
                } 
               
                
                if(!empty($request->project_id))
                {
                    $alldpr = $alldpr->where('dpr_configs.project_id',$request->project_id);
                }
                if(!empty($request->vender_id))
                {
                    $alldpr = $alldpr->where('dpr_configs.vendor_id',$request->vender_id);
                }
        
                $alldpr = $alldpr->get();
                
                foreach ($alldpr as $key => $dpr) {
                    
                  $allData = DprConfig::select('dpr_configs.*', 'dpr_imports.*','dpr_imports.sheet_json_data as sheet_json_data',DB::raw("MAX(dpr_imports.data_date) as data_date"),DB::raw("MAX(dpr_imports.id) as id"), 'dpr_configs.id as dpr_config_id', 'dpr_configs.vendor_id as vendor_id')
                    ->join('dpr_imports', 'dpr_configs.id', 'dpr_imports.dpr_config_id')
                    ->where('dpr_imports.item_desc_id', $value->item_desc_id)
                    ->where('dpr_configs.project_id', $dpr->project_id)
                    ->whereDate('dpr_imports.data_date', '<=', $date)
                    ->whereHas('project',function($q){
                          $q->where('status', 1);
                        })
                    ->groupBy('dpr_configs.vendor_id')
                    ->orderBy('dpr_imports.data_date','DESC');
                    
                    if(!empty($request->project_id))
                    {
                        $allData = $allData->where('dpr_configs.project_id',$request->project_id);
                    }
                    if(!empty($request->vender_id))
                    {
                        $allData = $allData->where('dpr_configs.vendor_id',$request->vender_id);
                    }
                    
                    $allData = $allData->get();
                    
                    $mergedArray = [];
                    $extaraData = [];
                    $is_dpr_submit = true;
                    $is_this_month_submit = true;
                    $id = $dpr->id;
                    $ids = [];
                    $data_date_array = [];
                  
                    foreach ($allData as $key => $data) {
                       $getData = \DB::table('dpr_imports')->where('id',$data->id)->first();
                       
                        if(date('Y-m-d',strtotime($date)) != date('Y-m-d',strtotime(@$getData->data_date))){
                            $is_dpr_submit = false;
                        }
                        if(date('Y-m',strtotime($date)) != date('Y-m',strtotime(@$getData->data_date))){
                            $is_this_month_submit = false;
                        }
                        $jsonData = json_decode(@$getData->sheet_json_data, true);
                        $extaraData['vendor_name'] = @$data->vendor->name;
                        $extaraData['project_name'] = @$data->Project->name;
                        $extaraData['project_status'] = @$data->Project->status;
                        $extaraData['file_name'] = @$dpr->dprManage->original_import_file;
                        $extaraData['is_dpr_submit'] = $is_dpr_submit;
                        $extaraData['is_this_month_submit'] = $is_this_month_submit;
                      

                        if(env('APP_ENV', 'local')==='production')
                        {
                            $extaraData['original_csv'] =secure_url('api/file-access/import/'.@$dpr->dprManage->original_import_file);
                        }
                        else
                        {
                            $extaraData['original_csv'] =url('api/file-access/import/'.@$dpr->dprManage->original_import_file);
                        }

                        $mergeExtraCol = array_merge($jsonData, $extaraData);
                        $mergedArray[] = $mergeExtraCol;
                        $ids[] = $data->id;
                        $data_date_array[] = $data->data_date;
                          
                    }

                    $itemData[] =[
                        'id' =>$id,
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
                        'ids' => $ids,
                        'data_date_array' => $data_date_array,
                       
                        //'file_name' => @$dpr->dprManage->original_import_file,
                        //'original_csv' => url('user/import/'.@$dpr->dprManage->original_import_file.''),
                        
                    ];
                }

                $dprList[] =[
                    'date' => $date,
                    'work_item' => $value->title,
                    'unit_of_measure' => $value->unit_of_measure,
                    'item_data' => $itemData,

                ];
                $itemData = [];

            }

           return view('dpr-report-view',compact('dprList','date'));

        } catch (Exception $e) {
            \Log::error($e);
            return response(prepareResult(true, $e->getMessage(),trans('translate.something_went_wrong')), config('httpcodes.internal_server_error'));
        }
        
        
        
    }
    public function downloadDprReport(Request $request) {
        try {
             $validator = \Validator::make($request->all(),[ 
                'date'     => 'required',
                //'work_item'     => 'required',
            ]);
            if ($validator->fails()) {
                return response(prepareResult(true, $validator->messages(),$validator->messages()->first()), config('httpcodes.bad_request'));
            }
            $date = date('Y-m-d',strtotime($request->date));
            $query = ItemDescriptionMaster::leftJoin('dpr_imports', function($join) use ($request, $date) {
                $join->on('item_description_masters.id', '=', 'dpr_imports.item_desc_id');
                //->where('dpr_imports.data_date', '=', $date);
            })
            ->leftJoin('dpr_configs', function($join) use ($request) {
                $join->on('dpr_configs.id', '=', 'dpr_imports.dpr_config_id');
            })
            ->select('item_description_masters.*','dpr_configs.*', 'dpr_imports.*', 'item_description_masters.id as item_desc_id', 'dpr_imports.id as id','dpr_configs.id as dpr_config_id')
            ->groupBy('item_description_masters.title')
            ->orderBy('item_description_masters.orderno','ASC');

            if(!empty($request->item_desc))
            {
                $query = $query->where('dpr_imports.item_desc_id',$request->item_desc);
            }
            
            if(!empty($request->project_id))
            {
                $query = $query->where('dpr_configs.project_id',$request->project_id);
            }
            if(!empty($request->vender_id))
            {
                $query = $query->where('dpr_configs.vendor_id',$request->vender_id);
            }
            

            $appSetting = AppSetting::first();
            $query = $query->get();
            $dprList =[];
            $itemData =[];
            foreach ($query as $value) {
                // Decode the JSON data from the column into a PHP array
                $alldpr = DprImport::select('dpr_configs.*','dpr_imports.*', 'dpr_imports.id as id','dpr_configs.id as dpr_config_id','projects.id as project_id','projects.orderno as orderno')
                ->join('dpr_configs','dpr_imports.dpr_config_id','dpr_configs.id')
                ->join('projects','dpr_configs.project_id','projects.id')
                //->where('dpr_imports.data_date',$date)
                ->where('dpr_imports.item_desc_id',$value->item_desc_id)
                 ->where('projects.status','1')
                //->whereMonth('dpr_imports.data_date',date('m',strtotime($date)))
                //->whereYear('dpr_imports.data_date',date('Y',strtotime($date)))
                ->groupBy('dpr_configs.project_id')
                ->orderBy('projects.orderno', 'ASC');
               
               
                 $findLatestData= NULL;
               
                if(count($alldpr->get()) <=0){
                    $findLatestData =DprImport::where('dpr_imports.item_desc_id',$value->item_desc_id)
  
                    ->whereDate('dpr_imports.data_date','<=',$date)
                    ->orderBy('data_date','DESC')->first();
                
                }
                if(!empty($findLatestData))
                {
                    $alldpr->orWhere('dpr_imports.id',$findLatestData->id);
                }
                
                if(!empty($request->project_id))
                {
                    $alldpr = $alldpr->where('dpr_configs.project_id',$request->project_id);
                }
                if(!empty($request->vender_id))
                {
                    $alldpr = $alldpr->where('dpr_configs.vendor_id',$request->vender_id);
                }
                $alldpr = $alldpr->get();
           
                foreach ($alldpr as $key => $dpr) {
                    
                    $allData = DprConfig::select('dpr_configs.*', 'dpr_imports.*','dpr_imports.sheet_json_data as sheet_json_data',DB::raw("MAX(dpr_imports.data_date) as data_date"),DB::raw("MAX(dpr_imports.id) as id"), 'dpr_configs.id as dpr_config_id', 'dpr_configs.vendor_id as vendor_id')
                    ->join('dpr_imports', 'dpr_configs.id', 'dpr_imports.dpr_config_id')
                    ->where('dpr_imports.item_desc_id', $value->item_desc_id)
                    ->where('dpr_configs.project_id', $dpr->project_id)
                    ->whereDate('dpr_imports.data_date', '<=', $date)
                    ->whereHas('project',function($q){
                          $q->where('status', 1);
                        })
                    ->groupBy('dpr_configs.vendor_id')
                    ->orderBy('dpr_imports.data_date','DESC');
                   

                    $latestDataWithProject= NULL;
                   
                    
                    if(!empty($request->project_id))
                    {
                        $allData = $allData->where('dpr_configs.project_id',$request->project_id);
                    }
                    if(!empty($request->vender_id))
                    {
                        $allData = $allData->where('dpr_configs.vendor_id',$request->vender_id);
                    }
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
                        $extaraData['vendor_name'] = @$data->vendor->name;
                        $extaraData['project_name'] = @$data->Project->name;
                        $extaraData['project_status'] = @$data->Project->status;
                        $extaraData['file_name'] = @$dpr->dprManage->original_import_file;
                        $extaraData['is_dpr_submit'] = $is_dpr_submit;
                        $extaraData['is_this_month_submit'] = $is_this_month_submit;
                        
                        if(env('APP_ENV', 'local')==='production')
                        {
                            $extaraData['original_csv'] = secure_url('api/file-access/import/'.@$dpr->dprManage->original_import_file);
                        }
                        else
                        {
                            $extaraData['original_csv'] = url('api/file-access/import/'.@$dpr->dprManage->original_import_file);
                        }

                        

                        $mergeExtraCol = array_merge($jsonData, $extaraData);
                        $mergedArray[] = $mergeExtraCol;
                           
                          
                    }

                    $itemData[] =[
                        'id' => @$dpr->id,
                        'project_name' => @$dpr->dprConfig->Project->name,
                        'project_status' => @$dpr->dprConfig->Project->status,
                        'sheet_name' => @$dpr->dprConfig->sheet_name,
                        'profile_name' => @$dpr->dprConfig->profile_name,
                        'vendor_name' => @$dpr->dprConfig->vendor->name,
                        'work_item' => $dpr->item_desc,
                        'color_code' => $dpr->color_code,
                        'data' => $mergedArray,
                        'is_dpr_submit' => $is_dpr_submit,
                        'is_this_month_submit' => $is_this_month_submit,
                        //'file_name' => @$dpr->dprManage->original_import_file,
                       // 'original_csv' => url('user/import/'.@$dpr->dprManage->original_import_file.''),
                        
                    ];
                }

                $dprList[] =[
                    'date' => $date,
                    'work_item' => $value->title,
                    'unit_of_measure' => $value->unit_of_measure,
                    'item_data' => $itemData,

                ];
                $itemData = [];

            }
            if(!empty($request->project_id))
            {
                $projectDe = \DB::table('projects')
            ->where('id', $request->project_id)
            ->where('status', '1')
            ->first();
            $packageName = @$projectDe->name;
            } else{
                $packageName = 'All';
            }
            $type = (!empty($request->type)) ? $request->type :'html';
            if(count($dprList)>0){
                if($request->type == "excel"){
                    $FileName = date('Y-m-d', strtotime($request->date)).'.xlsx';
                    $FilePath = 'excel/'.$FileName;
                    $data =  [
                        'date' => $request->date,
                        'dprList' => $dprList,
                        'appSetting' => $appSetting,
                    ];

                    
                    $html = view('excelView',$data)->render();

                    Excel::store(new CustomExport($data),$FilePath, 'excel_uploads');

                    if(env('APP_ENV', 'local')==='production')
                    {
                        $callApi = secure_url('api/file-access/'.$FilePath);
                    }
                    else
                    {
                        $callApi = url('api/file-access/'.$FilePath);
                    }
                    $path = Storage::path('public/'.$FilePath);
                    
                    $mime = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
                   

                } elseif($request->type == "pdf"){
                    $FileName = date('Y-m-d', strtotime($request->date)).'.pdf';
                    $date = $request->date;
                    $pdf = PDF::loadView('pdfview',compact('dprList', 'date','type','packageName'));
                    $FilePath = 'pdf/' . $FileName;
                    \Storage::disk('excel_uploads')->put($FilePath, $pdf->output(), 'public');
                    
                    if(env('APP_ENV', 'local')==='production')
                    {
                        $callApi = secure_url('api/file-access/'.$FilePath);
                    }
                    else
                    {
                        $callApi = url('api/file-access/'.$FilePath);
                    }
                    $path = Storage::path('public/'.$FilePath);
                    $mime = "application/pdf";

                } else{
                    $FileName = date('Y-m-d', strtotime($request->date)).'.html';
                    $FilePath = 'pdf/' . $FileName;
                    $html = view('pdfview',compact('dprList', 'date','type','packageName'));
                    $html = $html->render();
                   \Storage::disk('excel_uploads')->put($FilePath, $html, 'public');
                    if(env('APP_ENV', 'local')==='production')
                    {
                        $callApi = secure_url('api/file-access/'.$FilePath);
                    }
                    else
                    {
                        $callApi = url('api/file-access/'.$FilePath);
                    }
                    $path = Storage::path('public/'.$FilePath);
                    $mime = "text/html";
                }
                
                if (env('IS_MAIL_ENABLE', false) == true && !empty($request->email)){
                    $allEmails = explode(",", $request->email);
                    $content = [
                        "FileName" => $FileName,
                        "FilePath" => $path,
                        "mime" => $mime,
                    ];
                    foreach ($allEmails as $key => $email) {
                        $recevier = Mail::to($email)->send(new sendReportToEmail($content));
                        
                    }
                   
                }
                return response(prepareResult(false,$callApi, trans('translate.Download')),config('httpcodes.success'));

            } else{
                return response(prepareResult(true,[],trans('translate.record_not_found')), config('httpcodes.bad_request'));
            }



        } catch (Exception $e) {
            \Log::error($e);
            return response(prepareResult(true, $e->getMessage(),trans('translate.something_went_wrong')), config('httpcodes.internal_server_error'));
        }
    }
    
    public function getUploadsGraph(Request $request)
    {
        try
        {
            $year = $request->year ? $request->year : date('Y');
            $data = [];
            if(!empty($request->month))
            {
                $arr =  [
                        "Jan"=>1,
                        "Feb"=>2,
                        "Mar"=>3,
                        "Apr"=>4,
                        "May"=>5,
                        "Jun"=>6,
                        "Jul"=>7,
                        "Aug"=>8,
                        "Sep"=>9,
                        "Oct"=>10,
                        "Nov"=>11,
                        "Dec"=>12
                    ];
                foreach ($arr as $key => $value) {
                    if($request->month == $key)
                    {
                        $first_date = date($year.'-'.$value.'-1');
                        $month = $value;
                    }
                }
                $last_date =  date("t", strtotime($first_date));
                for ($d=1; $d<=$last_date; $d++) {
                    $date = date($year.'-'.sprintf("%02d", $month).'-'.sprintf("%02d", $d));
                    $dprUploads = \DB::table('dpr_imports')->join('dpr_configs', function($join) use ($request) {
                        $join->on('dpr_configs.id', '=', 'dpr_imports.dpr_config_id');
                    })
                    ->whereDate('dpr_imports.data_date',$date)->groupBy('dpr_imports.random_no');
                    // ->whereYear('dpr_imports.data_date',$year);
                    
                    if(!empty($request->project_id))
                    {
                        $dprUploads = $dprUploads->where('dpr_configs.project_id', $request->project_id);
                    }
                    if(!empty($request->item_desc))
                    {
                        $dprUploads = $dprUploads->where('dpr_imports.item_desc_id', $request->item_desc);
                    }
                    if(!empty($request->work_pack_id))
                    {
                        $dprUploads = $dprUploads->where('dpr_configs.work_pack_id', $request->work_pack_id);
                    }
                    if(!empty($request->vendor_id))
                    {
                        $dprUploads = $dprUploads->where('dpr_configs.vendor_id', $request->vendor_id);
                    }
                    $dprUploads = count($dprUploads->get());
                    
                    $data['date'][] = date('d',strtotime($date));
                    $data['data'][] = $dprUploads;
                }
            }
            else{
                for ($m=1; $m<=12; $m++) {
                    $dprUploads = \DB::table('dpr_imports')->join('dpr_configs', function($join) use ($request) {
                        $join->on('dpr_configs.id', '=', 'dpr_imports.dpr_config_id');
                    })
                    ->whereMonth('dpr_imports.data_date',sprintf("%02d", $m))
                    ->whereYear('dpr_imports.data_date',$year)->groupBy('dpr_imports.random_no');

                    
                    if(!empty($request->project_id))
                    {
                        $dprUploads = $dprUploads->where('dpr_configs.project_id', $request->project_id);
                    }
                    if(!empty($request->item_desc))
                    {
                        $dprUploads = $dprUploads->where('dpr_imports.item_desc', $request->item_desc);
                    }
                    if(!empty($request->vendor_id))
                    {
                        $dprUploads = $dprUploads->where('dpr_configs.vendor_id', $request->vendor_id);
                    }
                    
                    $dprCount = count($dprUploads->get());
                    $date = date('Y-'.$m.'-d');
                    $data['months'][] = date('M',strtotime($date));
                    $data['data'][] = $dprCount;
                }
               
            }
            return response(prepareResult(false, $data, trans('translate.dpr_uploads_graph')), config('httpcodes.success'));
        } catch(Exception $exception) {
            return response(prepareResult(false, $exception->getMessage(),trans('translate.something_went_wrong')), config('httpcodes.internal_server_error'));   
        } 
    }
     public function getManpowerGraph(Request $request)
    {
        try
        {
            $year = $request->year ? $request->year : date('Y');
            $data = [];
            if(!empty($request->month))
            {
                $arr =  ["Jan"=>1,"Feb"=>2,"Mar"=>3,"Apr"=>4,"May"=>5,"Jun"=>6,"Jul"=>7,"Aug"=>8,"Sep"=>9,"Oct"=>10,"Nov"=>11,"Dec"=>12];
                foreach ($arr as $key => $value) {
                    if($request->month == $key)
                    {
                        $first_date = date($year.'-'.$value.'-1');
                        $month = $value;
                    }
                }
                $last_date =  date("t", strtotime($first_date));
                for ($d=1; $d<=$last_date; $d++) {

                    $date = date($year.'-'.sprintf("%02d", $month).'-'.sprintf("%02d", $d));
                   

                    $dprUploads = \DB::table('dpr_imports')->join('dpr_configs', function($join) use ($request) {
                        $join->on('dpr_configs.id', '=', 'dpr_imports.dpr_config_id');
                    })
                    ->whereDate('dpr_imports.data_date',$date)->groupBy('dpr_imports.random_no');
                    

                    if(!empty($request->project_id))
                    {
                        $dprUploads->where('dpr_configs.project_id', $request->project_id);
                    }
                   
                    if(!empty($request->item_desc))
                    {
                        $dprUploads = $dprUploads->where('dpr_imports.item_desc_id', $request->item_desc);
                    }
                    if(!empty($request->vendor_id))
                    {
                        $dprUploads->where('dpr_configs.vendor_id', $request->vendor_id);
                    }
                    $dprUploads = $dprUploads->get();

                    $manpower = 0;

                    if($dprUploads->count()>0)
                    {
                        foreach ($dprUploads as $skey => $dprUpload) 
                        {
                            if($dprUpload->sheet_json_data!=''){

                                $sheetArray = json_decode($dprUpload->sheet_json_data, true);
                                if(is_array($sheetArray))
                                {
                                    foreach ($sheetArray as $key => $value) 
                                    {
                                        if($key =='manpower'){
                                            $manpower += $value;

                                        }
                                        
                                    }
                                }
                            }
                        }
                    }
                    
                    $data['date'][] = date('d',strtotime($date));
                    $data['data'][] = $manpower;
                }
            }
            else{
                for ($m=1; $m<=12; $m++) {
                    $dprUploads = \DB::table('dpr_imports')->join('dpr_configs', function($join) use ($request) {
                        $join->on('dpr_configs.id', '=', 'dpr_imports.dpr_config_id');
                    })
                    ->whereMonth('dpr_imports.data_date',sprintf("%02d", $m))
                    ->whereYear('dpr_imports.data_date',$year)
                    ->groupBy('dpr_imports.random_no');
                    
                    if(!empty($request->project_id))
                    {
                        $dprUploads = $dprUploads->where('dpr_configs.project_id', $request->project_id);
                    }
                    if(!empty($request->item_desc))
                    {
                        $dprUploads = $dprUploads->where('dpr_imports.item_desc_id', $request->item_desc);
                    }
                    
                    if(!empty($request->vendor_id))
                    {
                        $dprUploads = $dprUploads->where('dpr_configs.vendor_id', $request->vendor_id);
                    }
                    $dprUploads = $dprUploads->get();
                    $manpower = 0;
                    if($dprUploads->count()>0)
                    {
                        foreach ($dprUploads as $skey => $dprUpload) 
                        {
                            if($dprUpload->sheet_json_data!=''){
                                $sheetArray = json_decode($dprUpload->sheet_json_data, true);
                                if(is_array($sheetArray))
                                {
                                    foreach ($sheetArray as $key => $value) 
                                    {
                                        if($key =='manpower'){
                                            $manpower += $value;

                                        }
                                    }
                                }
                            }
                        }
                    }
                    $date = date('Y-'.$m.'-d');
                    $data['months'][] = date('M',strtotime($date));
                    $data['data'][] = $manpower;
                }
            }
            return response(prepareResult(false, $data, trans('translate.manpower_graph')), config('httpcodes.success'));
        } catch(Exception $exception) {
            return response(prepareResult(false, $exception->getMessage(),trans('translate.something_went_wrong')), config('httpcodes.internal_server_error'));   
        } 
    }

     public  function summeryReport($access_key)
    {
        
        if($access_key==''){
            return 'You are not authorized to access this page.';
        }
       
        $auth_key = AppSetting::first();
        if($access_key ==  $auth_key->access_key){
            
            $vendors =  Vendor::orderby('id','desc')->get();
            $itemDescs =  ItemDescriptionMaster::orderby('id','asc')->get();
            return view('summery-report',compact('itemDescs','vendors'));
        } else{
            return 'You are not authorized to access this page.';
        }
    }

     public function getSummeryReport(Request $request)
    {
       
           try
        {

          $startDate = new \DateTime($request->from_date);
            $endDate = new \DateTime($request->to_date);

            // Increment the end date by one day to include it in the range
            $endDate->modify('+1 day');

            $interval = new \DateInterval('P1D'); // 1 day interval
            $dateRange = new \DatePeriod($startDate, $interval, $endDate);
            $data  =[];
            foreach ($dateRange as $date) {
                $date = $date->format('Y-m-d');

                $dprUploads = DB::table('dpr_imports')->join('dpr_configs', function($join) use ($request) {
                    $join->on('dpr_configs.id', '=', 'dpr_imports.dpr_config_id');
                })
                ->join('dpr_manages', function($join) use ($request) {
                    $join->on('dpr_configs.id', '=', 'dpr_manages.dpr_config_id');
                })
                ->join('item_description_masters', function($join) use ($request) {
                    $join->on('dpr_imports.item_desc_id', '=', 'item_description_masters.id');
                })
                ->join('dpr_logs', function($join) use ($request) {
                    $join->on('dpr_configs.id', '=', 'dpr_logs.dpr_config_id')
                        ->on('dpr_imports.id', '=', 'dpr_logs.dpr_import_id');
                })
                ->select(array('dpr_configs.*','dpr_manages.*','dpr_imports.*','item_description_masters.id as item_desc_id','item_description_masters.title as work_pack_name','dpr_logs.import_file','dpr_logs.original_import_file'))
                ->whereDate('dpr_imports.data_date',$date)
                ->groupBy('dpr_imports.random_no')
                ->groupBy('dpr_logs.random_no');
               
                if(!empty($request->vendor_id))
                {
                    $dprUploads = $dprUploads->where('dpr_configs.vendor_id', $request->vendor_id);
                }
                
                if(!empty($request->item_desc))
                {
                    $dprUploads = $dprUploads->where('dpr_imports.item_desc_id', $request->item_desc);
                }
                

                $dprUploads = $dprUploads->first();

                if(env('APP_ENV', 'local')==='production')
                {
                    $original_csv =secure_url('api/file-access/import/'.@$dprUploads->import_file);
                }
                else
                {
                    $original_csv =url('api/file-access/import/'.@$dprUploads->import_file);
                }
                $date_a = date('d.m.Y',strtotime($date));
                $org_date = date('Y-m-d',strtotime($date));
               
                if($request->type == 'delete'){

                    $work_date_name =  (!empty($dprUploads)) ? @$dprUploads->original_import_file :'Not Submitted';
                } else{
                    $work_date_name = (!empty($dprUploads)) ? @$dprUploads->work_pack_name.' '.$date_a :'Not Submitted';
                }
                $link =  '<a href="'.$original_csv.'">'.$work_date_name.'</a>';
                $name_link = (!empty($dprUploads)) ? $link :'Not Submitted';
 
                $dpr_link = (!empty($dprUploads)) ? $original_csv : NULL;
           
                if($request->type == 'log'){
                    $data[] = [
                        "date" => $org_date,
                        "name" =>  $work_date_name,
                        "link" =>  $dpr_link,
                        "type" =>  $request->type,

                    ];

                } 

                else{
                    if($dprUploads !=''){
                        $data[] = [
                            "date" => $org_date,
                            "name" =>  $work_date_name,
                            "link" =>  $dpr_link,
                            "type" =>  $request->type,

                        ];

                    }

                }
                

                }
               
                $type = $request->type;
                return view('summry-report-view',compact('data','type'));

        } catch(Exception $exception) {
            return response(prepareResult(false, $exception->getMessage(),trans('translate.something_went_wrong')), config('httpcodes.internal_server_error'));   
        } 
    }
}
