<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Auth;
use Exception;
use App\Models\DprMap;
use App\Models\User;
use App\Models\Notification;
use App\Models\DprImport;
use App\Models\DprManage;
use App\Models\DprLog;
use App\Models\DprConfig;
use App\Models\ItemDescriptionMaster;
use App\Models\AppSetting;
use Excel;
use PDF;
use Storage;
use App\Imports\DprReportImport;
use App\Imports\DprSelectSheet;
use Illuminate\Support\Facades\File;
use App\Exports\ReportExport;
use DB;
use App\Exports\CustomExport;

class PdfViewController extends Controller
{

    public function loadPdf(Request $request) {
        try {

            $date = '2024-06-25';
            $query = ItemDescriptionMaster::leftJoin('dpr_imports', function($join) use ($request, $date) {
                $join->on('item_description_masters.title', '=', 'dpr_imports.item_desc');
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
                $query = $query->where('dpr_imports.item_desc',$request->item_desc);
            }
            if(!empty($request->dpr_config_id))
            {
                $query = $query->where('dpr_config_id',$request->dpr_config_id);
            }
            else
            {
                if(!empty(auth()->user()->dpr_config_ids))
                {
                    $dpr_config_ids = explode(',',auth()->user()->dpr_config_ids);
                    $query->whereIn('dpr_imports.dpr_config_id',$dpr_config_ids);
                }
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
                ->where('dpr_imports.item_desc',$value->title)
                 ->where('projects.status','1')
                //->whereMonth('dpr_imports.data_date',date('m',strtotime($date)))
                //->whereYear('dpr_imports.data_date',date('Y',strtotime($date)))
                ->groupBy('dpr_configs.project_id')
                ->orderBy('projects.orderno', 'ASC');
               
               
                 $findLatestData= NULL;
               
                if(count($alldpr->get()) <=0){
                    $findLatestData =DprImport::where('dpr_imports.item_desc',$value->title)
  
                    ->whereDate('dpr_imports.data_date','<=',$date)
                    ->orderBy('data_date','DESC')->first();
                
                }
                if(!empty($findLatestData))
                {
                    $alldpr->orWhere('dpr_imports.id',$findLatestData->id);
                }
                if(!empty(auth()->user()->dpr_config_ids))
                {
                    $dpr_config_ids = explode(',',auth()->user()->dpr_config_ids);
                    $alldpr = $alldpr->whereIn('dpr_imports.dpr_config_id',$dpr_config_ids);
                }
                if(!empty(auth()->user()->project_ids))
                {
                    $project_ids = explode(',',auth()->user()->project_ids);
                    $alldpr = $alldpr->whereIn('dpr_configs.project_id',$project_ids);
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
                    ->where('dpr_imports.item_desc', $value->title)
                    ->where('dpr_configs.project_id', $dpr->project_id)
                    ->whereDate('dpr_imports.data_date', '<=', $date)
                    ->whereHas('project',function($q){
                          $q->where('status', 1);
                        })
                    ->groupBy('dpr_configs.vendor_id')
                    ->orderBy('dpr_imports.data_date','DESC');
                   

                    $latestDataWithProject= NULL;
                
                    /*if(count($allData->get()) <=0){
                        $latestDataWithProject= DprImport::join('dpr_configs','dpr_imports.dpr_config_id','dpr_configs.id')
                        ->select('dpr_configs.*','dpr_imports.*', 'dpr_imports.id as id','dpr_configs.id as dpr_config_id')
                        ->where('dpr_imports.item_desc',$value->title)
                        ->where('dpr_configs.project_id',$dpr->project_id)
                        //->whereMonth('dpr_imports.data_date',date('m',strtotime($date)))
                        //->whereYear('dpr_imports.data_date',date('Y',strtotime($date)))
                        ->whereDate('dpr_imports.data_date','<=',$date)
                        ->orderBy('data_date','DESC')->first();
                    
                    
                        $is_dpr_submit = false;
                    }
                    if(!empty($latestDataWithProject))
                    {
                        if(date('Y-m',strtotime($date)) != date('Y-m',strtotime($latestDataWithProject->data_date))){
                            $is_this_month_submit = false;
                        }
                        $allData->orWhere('dpr_imports.id',$latestDataWithProject->id);
                    }
                        */
                    if(!empty(auth()->user()->dpr_config_ids))
                    {
                        $dpr_config_ids = explode(',',auth()->user()->dpr_config_ids);
                        $allData->whereIn('dpr_imports.dpr_config_id',$dpr_config_ids);
                    }
                    if(!empty(auth()->user()->project_ids))
                    {
                        $project_ids = explode(',',auth()->user()->project_ids);
                        $allData = $allData->whereIn('dpr_configs.project_id',$project_ids);
                    }
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

            

            $type ='pdf';
            $FileName = date('Y-m-d', strtotime($date)).'.pdf';
            $date = $request->date;
            //return view('pdfview',compact('dprList', 'date', 'type'));
            $pdf = PDF::loadView('pdfview',compact('dprList', 'date', 'type'));
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
            

            return redirect($callApi);
            return response(prepareResult(false,$callApi, trans('translate.Download')),config('httpcodes.success'));

          


        } catch (Exception $e) {
            \Log::error($e);
            return response(prepareResult(true, $e->getMessage(),trans('translate.something_went_wrong')), config('httpcodes.internal_server_error'));
        }
    }
    public function loadExcel(Request $request) {
        try {
         $json ='{
    "error": false,
    "data": {
        "sheet_name": "Overall Progress",
        "Drawing Release-OrderNo#3": [
            {
                "cell_value": "F",
                "row_position": 6,
                "row_new_position": null,
                "item_desc": {
                    "id": 1,
                    "title": "Civil RCC"
                }
            },
            {
                "cell_value": "F",
                "row_position": 16,
                "row_new_position": null,
                "item_desc": {
                    "id": 2,
                    "title": "Structure Fabrication"
                }
            },
            {
                "cell_value": "F",
                "row_position": 17,
                "row_new_position": null,
                "item_desc": {
                    "id": 3,
                    "title": "Structure Erection"
                }
            },
            {
                "cell_value": "F",
                "row_position": 52,
                "row_new_position": null,
                "item_desc": {
                    "id": 7,
                    "title": "Equipment Erection"
                }
            },
            {
                "cell_value": "F",
                "row_position": 71,
                "row_new_position": null,
                "item_desc": {
                    "id": 8,
                    "title": "Mechanical Piping"
                }
            },
            {
                "cell_value": "F",
                "row_position": 63,
                "row_new_position": null,
                "item_desc": {
                    "id": 9,
                    "title": "Refractory"
                }
            },
            {
                "cell_value": "F",
                "row_position": 80,
                "row_new_position": null,
                "item_desc": {
                    "id": 10,
                    "title": "Cable Laying"
                }
            },
            {
                "cell_value": "F",
                "row_position": 79,
                "row_new_position": null,
                "item_desc": {
                    "id": 11,
                    "title": "Cable Tray Erection"
                }
            },
            {
                "cell_value": "F",
                "row_position": 81,
                "row_new_position": null,
                "item_desc": {
                    "id": 12,
                    "title": "Panel Erection"
                }
            },
            {
                "cell_value": "F",
                "row_position": 82,
                "row_new_position": null,
                "item_desc": {
                    "id": 13,
                    "title": "Transformer Erection"
                }
            },
            {
                "cell_value": "F",
                "row_position": 1,
                "row_new_position": null,
                "item_desc": {
                    "id": 1,
                    "title": "Civil RCC"
                }
            }
        ],
        "Work Done Till Date-OrderNo#4": [
            {
                "cell_value": "L",
                "row_position": 6,
                "row_new_position": null,
                "item_desc": {
                    "id": 1,
                    "title": "Civil RCC"
                }
            },
            {
                "cell_value": "L",
                "row_position": 16,
                "row_new_position": null,
                "item_desc": {
                    "id": 2,
                    "title": "Structure Fabrication"
                }
            },
            {
                "cell_value": "L",
                "row_position": 17,
                "row_new_position": null,
                "item_desc": {
                    "id": 3,
                    "title": "Structure Erection"
                }
            },
            {
                "cell_value": "L",
                "row_position": 52,
                "row_new_position": null,
                "item_desc": {
                    "id": 7,
                    "title": "Equipment Erection"
                }
            },
            {
                "cell_value": "L",
                "row_position": 71,
                "row_new_position": null,
                "item_desc": {
                    "id": 8,
                    "title": "Mechanical Piping"
                }
            },
            {
                "cell_value": "L",
                "row_position": 63,
                "row_new_position": null,
                "item_desc": {
                    "id": 9,
                    "title": "Refractory"
                }
            },
            {
                "cell_value": "L",
                "row_position": 80,
                "row_new_position": null,
                "item_desc": {
                    "id": 10,
                    "title": "Cable Laying"
                }
            },
            {
                "cell_value": "L",
                "row_position": 79,
                "row_new_position": null,
                "item_desc": {
                    "id": 11,
                    "title": "Cable Tray Erection"
                }
            },
            {
                "cell_value": "L",
                "row_position": 81,
                "row_new_position": null,
                "item_desc": {
                    "id": 12,
                    "title": "Panel Erection"
                }
            },
            {
                "cell_value": "L",
                "row_position": 82,
                "row_new_position": null,
                "item_desc": {
                    "id": 13,
                    "title": "Transformer Erection"
                }
            },
            {
                "cell_value": "L",
                "row_position": 1,
                "row_new_position": null,
                "item_desc": {
                    "id": 1,
                    "title": "Civil RCC"
                }
            }
        ],
        "Plan FTM-OrderNo#5": [
            {
                "cell_value": "H",
                "row_position": 6,
                "row_new_position": null,
                "item_desc": {
                    "id": 1,
                    "title": "Civil RCC"
                }
            },
            {
                "cell_value": "H",
                "row_position": 16,
                "row_new_position": null,
                "item_desc": {
                    "id": 2,
                    "title": "Structure Fabrication"
                }
            },
            {
                "cell_value": "H",
                "row_position": 17,
                "row_new_position": null,
                "item_desc": {
                    "id": 3,
                    "title": "Structure Erection"
                }
            },
            {
                "cell_value": "H",
                "row_position": 52,
                "row_new_position": null,
                "item_desc": {
                    "id": 7,
                    "title": "Equipment Erection"
                }
            },
            {
                "cell_value": "H",
                "row_position": 71,
                "row_new_position": null,
                "item_desc": {
                    "id": 8,
                    "title": "Mechanical Piping"
                }
            },
            {
                "cell_value": "H",
                "row_position": 63,
                "row_new_position": null,
                "item_desc": {
                    "id": 9,
                    "title": "Refractory"
                }
            },
            {
                "cell_value": "H",
                "row_position": 80,
                "row_new_position": null,
                "item_desc": {
                    "id": 10,
                    "title": "Cable Laying"
                }
            },
            {
                "cell_value": "H",
                "row_position": 79,
                "row_new_position": null,
                "item_desc": {
                    "id": 11,
                    "title": "Cable Tray Erection"
                }
            },
            {
                "cell_value": "H",
                "row_position": 81,
                "row_new_position": null,
                "item_desc": {
                    "id": 12,
                    "title": "Panel Erection"
                }
            },
            {
                "cell_value": "H",
                "row_position": 82,
                "row_new_position": null,
                "item_desc": {
                    "id": 13,
                    "title": "Transformer Erection"
                }
            },
            {
                "cell_value": "H",
                "row_position": 1,
                "row_new_position": null,
                "item_desc": {
                    "id": 1,
                    "title": "Civil RCC"
                }
            }
        ],
        "Achieved FTM-OrderNo#6": [
            {
                "cell_value": "I",
                "row_position": 6,
                "row_new_position": null,
                "item_desc": {
                    "id": 1,
                    "title": "Civil RCC"
                }
            },
            {
                "cell_value": "I",
                "row_position": 16,
                "row_new_position": null,
                "item_desc": {
                    "id": 2,
                    "title": "Structure Fabrication"
                }
            },
            {
                "cell_value": "I",
                "row_position": 17,
                "row_new_position": null,
                "item_desc": {
                    "id": 3,
                    "title": "Structure Erection"
                }
            },
            {
                "cell_value": "I",
                "row_position": 52,
                "row_new_position": null,
                "item_desc": {
                    "id": 7,
                    "title": "Equipment Erection"
                }
            },
            {
                "cell_value": "I",
                "row_position": 71,
                "row_new_position": null,
                "item_desc": {
                    "id": 8,
                    "title": "Mechanical Piping"
                }
            },
            {
                "cell_value": "I",
                "row_position": 63,
                "row_new_position": null,
                "item_desc": {
                    "id": 9,
                    "title": "Refractory"
                }
            },
            {
                "cell_value": "I",
                "row_position": 6,
                "row_new_position": null,
                "item_desc": {
                    "id": 10,
                    "title": "Cable Laying"
                }
            },
            {
                "cell_value": "I",
                "row_position": 79,
                "row_new_position": null,
                "item_desc": {
                    "id": 11,
                    "title": "Cable Tray Erection"
                }
            },
            {
                "cell_value": "I",
                "row_position": 81,
                "row_new_position": null,
                "item_desc": {
                    "id": 12,
                    "title": "Panel Erection"
                }
            },
            {
                "cell_value": "I",
                "row_position": 82,
                "row_new_position": null,
                "item_desc": {
                    "id": 13,
                    "title": "Transformer Erection"
                }
            },
            {
                "cell_value": "I",
                "row_position": 1,
                "row_new_position": null,
                "item_desc": {
                    "id": 1,
                    "title": "Civil RCC"
                }
            }
        ],
        "Achieved FTD-OrderNo#7": [
            {
                "cell_value": "J",
                "row_position": 6,
                "row_new_position": null,
                "item_desc": {
                    "id": 1,
                    "title": "Civil RCC"
                }
            },
            {
                "cell_value": "J",
                "row_position": 16,
                "row_new_position": null,
                "item_desc": {
                    "id": 2,
                    "title": "Structure Fabrication"
                }
            },
            {
                "cell_value": "J",
                "row_position": 17,
                "row_new_position": null,
                "item_desc": {
                    "id": 3,
                    "title": "Structure Erection"
                }
            },
            {
                "cell_value": "J",
                "row_position": 52,
                "row_new_position": null,
                "item_desc": {
                    "id": 7,
                    "title": "Equipment Erection"
                }
            },
            {
                "cell_value": "J",
                "row_position": 71,
                "row_new_position": null,
                "item_desc": {
                    "id": 8,
                    "title": "Mechanical Piping"
                }
            },
            {
                "cell_value": "J",
                "row_position": 63,
                "row_new_position": null,
                "item_desc": {
                    "id": 9,
                    "title": "Refractory"
                }
            },
            {
                "cell_value": "J",
                "row_position": 80,
                "row_new_position": null,
                "item_desc": {
                    "id": 10,
                    "title": "Cable Laying"
                }
            },
            {
                "cell_value": "J",
                "row_position": 79,
                "row_new_position": null,
                "item_desc": {
                    "id": 11,
                    "title": "Cable Tray Erection"
                }
            },
            {
                "cell_value": "J",
                "row_position": 81,
                "row_new_position": null,
                "item_desc": {
                    "id": 12,
                    "title": "Panel Erection"
                }
            },
            {
                "cell_value": "J",
                "row_position": 82,
                "row_new_position": null,
                "item_desc": {
                    "id": 13,
                    "title": "Transformer Erection"
                }
            },
            {
                "cell_value": "J",
                "row_position": 1,
                "row_new_position": null,
                "item_desc": {
                    "id": 1,
                    "title": "Civil RCC"
                }
            }
        ],
        "Data date-OrderNo#1": [
            {
                "cell_value": "C",
                "row_position": 1,
                "row_new_position": null,
                "item_desc": {
                    "id": 1,
                    "title": "Civil RCC"
                }
            },
            {
                "cell_value": "C",
                "row_position": 1,
                "row_new_position": null,
                "item_desc": {
                    "id": 2,
                    "title": "Structure Fabrication"
                }
            },
            {
                "cell_value": "C",
                "row_position": 1,
                "row_new_position": null,
                "item_desc": {
                    "id": 3,
                    "title": "Structure Erection"
                }
            },
            {
                "cell_value": "C",
                "row_position": 1,
                "row_new_position": null,
                "item_desc": {
                    "id": 7,
                    "title": "Equipment Erection"
                }
            },
            {
                "cell_value": "C",
                "row_position": 1,
                "row_new_position": null,
                "item_desc": {
                    "id": 8,
                    "title": "Mechanical Piping"
                }
            },
            {
                "cell_value": "C",
                "row_position": 1,
                "row_new_position": null,
                "item_desc": {
                    "id": 9,
                    "title": "Refractory"
                }
            },
            {
                "cell_value": "C",
                "row_position": 1,
                "row_new_position": null,
                "item_desc": {
                    "id": 10,
                    "title": "Cable Laying"
                }
            },
            {
                "cell_value": "C",
                "row_position": 1,
                "row_new_position": null,
                "item_desc": {
                    "id": 11,
                    "title": "Cable Tray Erection"
                }
            },
            {
                "cell_value": "C",
                "row_position": 1,
                "row_new_position": null,
                "item_desc": {
                    "id": 12,
                    "title": "Panel Erection"
                }
            },
            {
                "cell_value": "C",
                "row_position": 1,
                "row_new_position": null,
                "item_desc": {
                    "id": 13,
                    "title": "Transformer Erection"
                }
            },
            {
                "cell_value": "C",
                "row_position": 1,
                "row_new_position": null,
                "item_desc": {
                    "id": 1,
                    "title": "Civil RCC"
                }
            }
        ],
        "Scope-OrderNo#2": [
            {
                "cell_value": "E",
                "row_position": 6,
                "row_new_position": null,
                "item_desc": {
                    "id": 1,
                    "title": "Civil RCC"
                }
            },
            {
                "cell_value": "E",
                "row_position": 16,
                "row_new_position": null,
                "item_desc": {
                    "id": 2,
                    "title": "Structure Fabrication"
                }
            },
            {
                "cell_value": "E",
                "row_position": 17,
                "row_new_position": null,
                "item_desc": {
                    "id": 3,
                    "title": "Structure Erection"
                }
            },
            {
                "cell_value": "E",
                "row_position": 52,
                "row_new_position": null,
                "item_desc": {
                    "id": 7,
                    "title": "Equipment Erection"
                }
            },
            {
                "cell_value": "E",
                "row_position": 71,
                "row_new_position": null,
                "item_desc": {
                    "id": 8,
                    "title": "Mechanical Piping"
                }
            },
            {
                "cell_value": "E",
                "row_position": 63,
                "row_new_position": null,
                "item_desc": {
                    "id": 9,
                    "title": "Refractory"
                }
            },
            {
                "cell_value": "E",
                "row_position": 80,
                "row_new_position": null,
                "item_desc": {
                    "id": 10,
                    "title": "Cable Laying"
                }
            },
            {
                "cell_value": "E",
                "row_position": 79,
                "row_new_position": null,
                "item_desc": {
                    "id": 11,
                    "title": "Cable Tray Erection"
                }
            },
            {
                "cell_value": "E",
                "row_position": 81,
                "row_new_position": null,
                "item_desc": {
                    "id": 12,
                    "title": "Panel Erection"
                }
            },
            {
                "cell_value": "E",
                "row_position": 82,
                "row_new_position": null,
                "item_desc": {
                    "id": 13,
                    "title": "Transformer Erection"
                }
            },
            {
                "cell_value": "E",
                "row_position": 1,
                "row_new_position": null,
                "item_desc": {
                    "id": 1,
                    "title": "Civil RCC"
                }
            }
        ]
    },
    "message": "DPR mapping date successfully fetched."
}';
$josn_aaray = json_decode($json,true);
dd($josn_aaray);

        } catch (Exception $e) {
            \Log::error($e);
            return response(prepareResult(true, $e->getMessage(),trans('translate.something_went_wrong')), config('httpcodes.internal_server_error'));
        }

    }
}
