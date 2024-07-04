<?php

namespace App\Http\Controllers\API\Common;

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
use App\Models\WorkPackage;
use App\Models\DprConfig;
use App\Models\AppSetting;
use App\Models\ItemDescriptionMaster;
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
use App\Exports\SummeryExport;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use Carbon\Carbon;
use App\Mail\sendReportToEmail;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Exports\ExcelToCsvExport;
use App\Imports\ExcelToCsvImport;
use App\Services\SimpleXLSX;

class DprImportController extends Controller
{
	public function __construct()
    {
        //$this->middleware('permission:dpr-import',['only' => ['dprImportList','downloadPdf','workItemList','downloadExcel','store','dprImport']]);
    }
	/**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	//import dpr
	public function store(Request $request)
	{
		$validation = \Validator::make($request->all(), [
			'dpr_config_id'      => 'required',
		]);

		if ($validation->fails()) {
			return response(prepareResult(true, $validation->messages(), $validation->messages()->first()), config('httpcodes.bad_request'));
		}

		DB::beginTransaction();
		try
		{
			
			$data =[];
			$work_item =NULL;
			$data_date =NULL;
			$maxCount = 0; // Initialize with a negative value
            $maxMapValue = [];
            $posVal = [];
            $posValx = [];
			if(is_array($request->dpr_data) && count($request->dpr_data) >0 ){
				foreach ($request->dpr_data as $mapItem) {
	                $count = count(@$mapItem['dpr_value']);
	                if ($count > $maxCount) {
	                    $maxCount = $count;
	                    $maxMapValue = @$mapItem['dpr_value'];
	                }
	            }
             	$maxCount =  count($maxMapValue);
             	$final_array = [];
             	for ($i = 0;$i < $maxCount ;$i++) {
					foreach ($request->dpr_data as $k =>$val) {

						if(is_array($val['dpr_value']) && count($val['dpr_value']) >0 ){
							
							$value = (!empty(@$val['dpr_value'][$i]))? $val['dpr_value'][$i]['value'] : $val['dpr_value'][0]['value'];
							$item_desc =(!empty(@$val['dpr_value'][$i]))? $val['dpr_value'][$i]['item_desc'] : $val['dpr_value'][0]['item_desc'];
							if($val['name'] =='Work Item'){
								$work_item = $value;
							}
							if(strtolower($val['name']) ==strtolower('Data Date')){
								$data_date = date('Y-m-d',strtotime($value));
							}
							if(strtolower($val['name']) ==strtolower('Data Date')){
								$posVal = [];
							} else {
								if(is_numeric($value)){
									$posVal[$val['name']] =  $value;
								} else{
									$posVal[$val['name']] = $value;
								}
							   
							  
							}
							//$posVal['Item Description'] = $item_desc;

							$data = $posVal;
							
						}
						

					}
					$final_array[$item_desc]  = $data;

				}


			}
			if($data_date =='1970-01-01' ){
    				return response(prepareResult(true, [], 'Invalid Data Date ('.$data_date.') recieved.You can check your dpr mapping or excel file'), config('httpcodes.bad_request'));
			}
			$twoYearBefore = strtotime('-2 year', strtotime(date('Y-m-d')));
			if($twoYearBefore > strtotime($data_date)){
				return response(prepareResult(true, [], 'Data date ('.$data_date.') can not be less than two years'), config('httpcodes.bad_request'));
			}
			if(strtotime(date('Y-m-d')) < strtotime($data_date)){
				return response(prepareResult(true, [], 'Data date ('.$data_date.') can not be greather than today date'), config('httpcodes.bad_request'));
			}
			$is_validate = true;
			$message = "";
			if(is_array($final_array) && count($final_array) >0 ){
				foreach ($final_array as $key => $value) {
					$checkPreviousDayImport = \DB::table('dpr_imports')->where('dpr_config_id',$request->dpr_config_id)->where('item_desc',$key)->whereDate('data_date','<',$data_date)->whereMonth('data_date',date('m',strtotime($data_date)))->whereYear('data_date',date('Y',strtotime($data_date)))->orderBy('data_date','DESC')->first();
                    if(!empty($checkPreviousDayImport)){
    					$jsonData = (!empty($checkPreviousDayImport)) ? json_decode($checkPreviousDayImport->sheet_json_data, true) : [];
    					$work_done_till_date_old =NULL;
    					$plan_ftm_old =NULL;
    					$acheived_ftm_old =NULL;
    					$work_done_till_date =NULL;
    					$plan_ftm =NULL;
    					$acheived_ftm =NULL;
    					if(!empty(@$jsonData['Work Done Till Date']) || !empty(@$jsonData['Actuals till date'])){
    						$work_done_till_date_old = (!empty(@$jsonData['Work Done Till Date']))? @$jsonData['Work Done Till Date'] :@$jsonData['Actuals till date'];
    					}
    					if(!empty(@$jsonData['Plan FTM']) || !empty(@$jsonData['Plan for the Month'])){
    						$plan_ftm_old = (!empty(@$jsonData['Plan FTM']))? @$jsonData['Plan FTM'] :@$jsonData['Plan for the Month'];
    					}
    					if(!empty(@$jsonData['Achieved FTM']) ){
    						$acheived_ftm_old = @$jsonData['Achieved FTM'];
    					}
    					
    					if(!empty(@$value['Work Done Till Date']) || !empty(@$value['Actuals till date'])){
    						$work_done_till_date = (!empty(@$value['Work Done Till Date']))? @$value['Work Done Till Date'] :@$value['Actuals till date'];
    					}
    					if(!empty(@$value['Plan FTM']) || !empty(@$value['Plan for the Month'])){
    						$plan_ftm = (!empty(@$value['Plan FTM']))? @$value['Plan FTM'] :@$value['Plan for the Month'];
    					}
    					if(!empty(@$value['Achieved FTM']) ){
    						$acheived_ftm = @$value['Achieved FTM'];
    					}
    					if($plan_ftm != $plan_ftm_old){
    						$is_validate = false;
    						$message ="You can't upload file due to change in Plan FTM";
    					}
    					if($work_done_till_date_old < $work_done_till_date){
    						$is_validate = false;
    						$message = "You can't upload file due to change in work done till date";
    					}
    					if($acheived_ftm < $acheived_ftm){
    						$is_validate = false;
    						$message = "You can't upload file due to change in Acheived FTM";
    					}
                    }

				}
			}
				
			if($is_validate==false){
				return response(prepareResult(true,[],$message), config('httpcodes.bad_request'));
			}
			$random_no = \Str::random(15);
			$deleteOld = DprImport::where('data_date',$data_date)->where('dpr_config_id',$request->dpr_config_id)->delete();
		
			if(is_array($final_array) && count($final_array) >0 ){
				foreach ($final_array as $key => $value) {
					$scope = NULL;
					$work_done_till_date = NULL;
					$complete_per = NULL;
					$plan_ftm = NULL;
					$acheived_ftd = NULL;
					$acheived_ftm = NULL;
            
					if(!empty(@$value['Scope']) || !empty(@$value['Plan Scope'])){
						$scope = (!empty(@$value['Scope']))? @$value['Scope'] : @$value['Plan Scope'];
					}
					if(!empty(@$value['Work Done Till Date']) || !empty(@$value['Actuals till date'])){
						$work_done_till_date = (!empty(@$value['Work Done Till Date']))? @$value['Work Done Till Date'] :@$value['Actuals till date'];
					}
					if(!empty(@$value['Plan FTM']) || !empty(@$value['Plan for the Month'])){
						$plan_ftm = (!empty(@$value['Plan FTM']))? @$value['Plan FTM'] :@$value['Plan for the Month'];
					}
					
					if(!empty(@$value['Achieved FTM']) ){
						$acheived_ftm = @$value['Achieved FTM'];
					}

					$totalDaysInMonth = (int) date('t', strtotime($data_date));

				    $dayValue = (int) date('d', strtotime($data_date));
					
					
					
					$completep= ($scope > 0) ? ($work_done_till_date/$scope)*100:0;
					$complete_per = round($completep,0);
					$posValx['% Complete'] = $complete_per;
					$posValx['Balance'] = $scope-$work_done_till_date;
					
					$color = "#808080";
					
					$acheived_aginst_plan= ($acheived_ftm>0 && $plan_ftm>0) ? round((($acheived_ftm/($plan_ftm*($dayValue/$totalDaysInMonth)))*100),0) :0;
					$posValx['% Achievement Against Plan'] = $acheived_aginst_plan;
					
					if($acheived_aginst_plan >=90){
						$color ="#6de26d";

					}
					if($acheived_aginst_plan >80 && $acheived_aginst_plan <90){
						$color ="#ffbf00";

					}
					if($acheived_aginst_plan >=0 && $acheived_aginst_plan <=80){
						$color ="#808080";

					}
					
					if(count($posValx) > 0 ){
						$final_array_data = array_merge(
						    array_slice($value, 0, 3),  // First two elements from the first array
						    array_slice($posValx, 0, 2) ,                     // Second array
						    array_slice($value, 2),
						    array_slice($posValx, 2)    
						         // Elements after the second position from the first array
						);
					} else{
						$final_array_data = $value;
					}
					
					$sheet_json = json_encode($final_array_data,true);
					$itemd = ItemDescriptionMaster::select('id','title')->where('title',$key)->first();
					$dprImport = new DprImport;
					$dprImport->user_id         	= auth()->id();
					$dprImport->dpr_manage_id   	= NULL;
					$dprImport->dpr_config_id   	= $request->dpr_config_id;
					$dprImport->data_date       	= $data_date;
					$dprImport->item_desc       	= $key;
					$dprImport->item_desc_id       	= @$itemd->id;
					$dprImport->sheet_json_data 	= $sheet_json;
					$dprImport->color_code = $color;
					$dprImport->random_no 	= $random_no;
					$dprImport->save();


					//notify admin about dpr import
					$notification = new Notification;
					$notification->user_id              = User::first()->id;
					$notification->sender_id            = auth()->id();
					$notification->status_code          = 'success';
		            $notification->type                = 'DprImport';
		            $notification->event                = 'Created';
					$notification->title                = 'New Dpr Uploaded';
					$notification->message              = 'New Dpr Uploaded.';
					$notification->read_status          = false;
					$notification->data_id              = $dprImport->id;
					$notification->save();

					DB::commit();

				}
			}

			$dprData = DprImport::where('data_date',$data_date)->where('dpr_config_id',$request->dpr_config_id)->get();
			
			return response(prepareResult(false, $dprData, trans('translate.dpr_import_created')),config('httpcodes.created'));

		} catch (\Throwable $e) {
			\Log::error($e);
			return response(prepareResult(true, $e->getMessage(), trans('translate.something_went_wrong')), config('httpcodes.internal_server_error'));
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	//import through file

		public function dprImport(Request $request) {
		try {


			$user = getUser();
			$validator = \Validator::make($request->all(),[ 
				'file'     => 'required|min:1|mimes:xlsx,xls',
				'dpr_config_id' => 'required|exists:dpr_configs,id',  
			]);
			if ($validator->fails()) {
				return response(prepareResult(true, $validator->messages(),$validator->messages()->first()), config('httpcodes.bad_request'));
			}
			$sheetname = DB::table('dpr_configs')->where('id',$request->dpr_config_id)->first();
			if(empty($sheetname)){
				return response(prepareResult(true,[],trans('translate.Mapping_does_not_exists_for_this_sheet')), config('httpcodes.bad_request'));
			}

			$sheet_name = str_replace('_',' ',$sheetname->sheet_name);
			$sheetname = \Str::slug($sheet_name);
			$formatCheck = ['xlsx','xls'];
			$file = $request->file;

			$extension = strtolower($file->getClientOriginalExtension());
			$original_file_name = $file->getClientOriginalName();
			if(!in_array($extension, $formatCheck))
			{
				return response(prepareResult(true,[],trans('translate.Only_xlsx_xls_files_are_acceptable')), config('httpcodes.bad_request'));
			}
            // $userDprMap = DprMap::where('sheet_name',$sheet_name)->orderby('id','ASC')->get();
			$userDprMap = DprMap::where('dpr_config_id',$request->dpr_config_id)->whereNotNull('orderno')->orderby('orderno','ASC')->get();
			
		
			if(count($userDprMap) < 1){
				return response(prepareResult(true,[],trans('translate.Mapping_does_not_exists_for_this_sheet')), config('httpcodes.bad_request'));
			}

			if($request->hasFile('file')) {

				
				$FileName = time() . '-' . $sheetname.'.'.$extension;
				$FilePath = 'import/' . $FileName;
				$readerExtension = ucfirst($file->getClientOriginalExtension());

				\Storage::disk('excel_uploads')->put($FilePath, file_get_contents($file), 'public');

				$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($readerExtension);
				$reader->setReadDataOnly(true);
				$spreadsheet = $reader->load($request->file('file'));
				$allSheetsArray = $spreadsheet->getSheetNames();
				if($extension !='csv'){
					if(is_array($allSheetsArray) && !in_array($sheet_name,$allSheetsArray)){
						return response(prepareResult(true,[],trans('translate.Invalid_Sheet_name')), config('httpcodes.bad_request'));
					}

				}
				/*--------------User Mapping-----------------------*/
				$checkMax = DprMap::select('id','dpr_config_id','position')->where('dpr_config_id',$request->dpr_config_id)->orderBy('position','DESC')->first();

				$data =[];
				$organizedData = [];
				$posValx = [];
				if($extension !='csv'){
					$import = $spreadsheet->getSheetByName($sheet_name);

				} else{
					$import = $spreadsheet->getActiveSheet($sheet_name);
				}
				
				$work_item = NULL;
				$data_date = NULL;
				$manpower = $request->manpower;
				for ($i=0; $i <= $checkMax->position ; $i++) { 
				foreach($userDprMap as $key => $value){
						$val = DprMap::where('dpr_config_id',$request->dpr_config_id)->where('slug',$value->slug)->where('position',$i)->first();
						$position =  @$val->cell_value.@$val->row_position ;
						
						$checkValExist = (!empty($position)) ? $import->getCell($position)->getValue(): NULL;
						
						//\Log::info($checkValExist);
						$colValue = (!empty($position)) ? $import->getCell($position)->getOldCalculatedValue(): NULL;
						if(empty($colValue)){
    					    if (preg_match('/^\d*\.?\d+$/', $checkValExist)) {
                                $colValue = $checkValExist;
                            } else {
                               $colValue= NULL;
                            }
					    
						}
					
				
						$cell = $import->getCell($position);
						$cellDataType = $cell->getDataType();
						// Determine if the cell value can be parsed as a date
						//$posVal['Item Description'] = $val->item_desc;
							
						if(strtolower($val->name) == strtolower('Data Date')){
						    $colValue = (!empty($position)) ? $import->getCell($position)->getCalculatedValue(): NULL;
							if (is_numeric($colValue)){
								$unix_date = ($colValue - 25569) * 86400;
								$data_date  = gmdate("Y-m-d", $unix_date);
								
							} else{
							   
								$date = ($colValue!='') ?  date('Y-m-d',strtotime($colValue)) : '';
								$data_date =($date=='1970-01-01') ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($colValue): $date;
								
							}

						}
						
						if($extension =='csv' &&  strtolower($val->name) != strtolower('Data Date')){
							$stringValue = (!empty($position)) ? $import->getCell($position)->getValue(): NULL;
							$colValue =  str_replace(',', '', $stringValue);
							
						}
						if(strtolower($val->name) == strtolower('Data Date')){
							$posVal = [];
						} else {
						   if (is_numeric($colValue)){
						   		$posVal[$val->name] =  $colValue;
						   } else{
						   		$posVal[$val->name] = $colValue;
						   }
						  
						}
						
						if($val->name =='manpower'){
							$manpower = (!empty($colValue))? $colValue :'';
						}

						if(!empty($manpower)) {
							$posValx['manpower'] =  $manpower;
						}
						if(!empty($request->change_reason_for_plan_ftm)) {
							$posValx['Change reason for plan ftm'] = $request->change_reason_for_plan_ftm;
						}
						$data[$val->item_desc]= array_merge($posVal,$posValx);
						
				
					}
					
				}
			
			    //dd($data);
				if($data_date==''){
				    	return response(prepareResult(true, [], 'Invalid Data Date ('.$data_date.') recieved.You can check your dpr mapping or excel file'), config('httpcodes.bad_request'));
				}
				if($data_date =='1970-01-01' ){
    				return response(prepareResult(true, [], 'Invalid Data Date ('.$data_date.') recieved.You can check your dpr mapping or excel file'), config('httpcodes.bad_request'));
    			}
    			$twoYearBefore = strtotime('-2 year', strtotime(date('Y-m-d')));
    			if($twoYearBefore > strtotime($data_date)){
    				return response(prepareResult(true, [], 'Data date ('.$data_date.') can not be less than two years'), config('httpcodes.bad_request'));
    			}
    			if(strtotime(date('Y-m-d')) < strtotime($data_date)){
    				return response(prepareResult(true, [], 'Data date ('.$data_date.') can not be greather than today date'), config('httpcodes.bad_request'));
    			}
    			$is_validate = true;
    			$message = "";
    			if(is_array($data) && count($data) >0 ){
					foreach ($data as $key => $value) {
						$checkPreviousDayImport = \DB::table('dpr_imports')->where('dpr_config_id',$request->dpr_config_id)->where('item_desc',$key)->whereDate('data_date','<',$data_date)->whereMonth('data_date',date('m',strtotime($data_date)))->whereYear('data_date',date('Y',strtotime($data_date)))->orderBy('data_date','DESC')->first();
                        if(!empty($checkPreviousDayImport)){
    						$jsonData = (!empty($checkPreviousDayImport)) ? json_decode($checkPreviousDayImport->sheet_json_data, true) : [];
    						$work_done_till_date_old =NULL;
    						$plan_ftm_old =NULL;
    						$acheived_ftm_old =NULL;
    						$work_done_till_date =NULL;
    						$plan_ftm =NULL;
    						$acheived_ftm =NULL;
    						if(!empty(@$jsonData['Work Done Till Date']) || !empty(@$jsonData['Actuals till date'])){
    							$work_done_till_date_old = (!empty(@$jsonData['Work Done Till Date']))? @$jsonData['Work Done Till Date'] :@$jsonData['Actuals till date'];
    						}
    						if(!empty(@$jsonData['Plan FTM']) || !empty(@$jsonData['Plan for the Month'])){
    							$plan_ftm_old = (!empty(@$jsonData['Plan FTM']))? @$jsonData['Plan FTM'] :@$jsonData['Plan for the Month'];
    						}
    						if(!empty(@$jsonData['Achieved FTM']) ){
    							$acheived_ftm_old = @$jsonData['Achieved FTM'];
    						}
    						
    						if(!empty(@$value['Work Done Till Date']) || !empty(@$value['Actuals till date'])){
    							$work_done_till_date = (!empty(@$value['Work Done Till Date']))? @$value['Work Done Till Date'] :@$value['Actuals till date'];
    						}
    						if(!empty(@$value['Plan FTM']) || !empty(@$value['Plan for the Month'])){
    							$plan_ftm = (!empty(@$value['Plan FTM']))? @$value['Plan FTM'] :@$value['Plan for the Month'];
    						}
    						if(!empty(@$value['Achieved FTM']) ){
    							$acheived_ftm = @$value['Achieved FTM'];
    						}
    						if($plan_ftm != $plan_ftm_old){
    							$is_validate = false;
    							$message ="You can't upload file due to change in Plan FTM";
    						}
    						if($work_done_till_date_old < $work_done_till_date){
    							$is_validate = false;
    							$message = "You can't upload file due to change in work done till date";
    						}
    						if($acheived_ftm < $acheived_ftm){
    							$is_validate = false;
    							$message = "You can't upload file due to change in Acheived FTM";
    						}
                        }

					}
				}
				
			/*	if($is_validate==false){
					return response(prepareResult(true,[],$message), config('httpcodes.bad_request'));
				}*/
				$dprManage = new DprManage;
				$dprManage->user_id = $user->id;
				$dprManage->dpr_config_id = $request->dpr_config_id;
				$dprManage->original_import_file = $FileName;
				$dprManage->save();

				$deleteOld = DprImport::where('data_date',$data_date)->where('dpr_config_id',$request->dpr_config_id)->delete();
				$random_no = \Str::random(15);
				if(is_array($data) && count($data) >0 ){
					$posValx =[];
					foreach ($data as $key => $value) {
						
						$scope = NULL;
						$work_done_till_date = NULL;
						$complete_per = NULL;
						$plan_ftm = NULL;
						$acheived_ftd = NULL;
						$acheived_ftm = NULL;

						if(!empty(@$value['Scope']) || !empty(@$value['Plan Scope'])){

							$scope = (!empty(@$value['Scope']))? @$value['Scope'] : @$value['Plan Scope'];

						}
						if(!empty(@$value['Work Done Till Date']) || !empty(@$value['Actuals till date'])){

							$work_done_till_date = (!empty(@$value['Work Done Till Date']))? @$value['Work Done Till Date'] :@$value['Actuals till date'];

						}
						if(!empty(@$value['Plan FTM']) || !empty(@$value['Plan for the Month'])){

							$plan_ftm = (!empty(@$value['Plan FTM']))? @$value['Plan FTM'] :@$value['Plan for the Month'];

						}

				        $totalDaysInMonth = (int) date('t', strtotime($data_date));

				        $dayValue = (int) date('d', strtotime($data_date));
						
						if(!empty(@$value['Achieved FTM']) ){

							$acheived_ftm = @$value['Achieved FTM'];

						}
						
						$completep= ($scope> 0) ? ($work_done_till_date/$scope)*100:0;
						$complete_per = round($completep,0);
						$posValx['% Complete'] = $complete_per;
						$posValx['Balance'] = $scope-$work_done_till_date;
							
						$color = "#808080";
		                
						$acheived_aginst_plan= ($acheived_ftm>0 && $plan_ftm>0) ? round((($acheived_ftm/($plan_ftm*($dayValue/$totalDaysInMonth)))*100),0) :0;
						
						$posValx['% Achievement Against Plan'] = $acheived_aginst_plan;
						
						if($acheived_aginst_plan >=90){
							$color ="#6de26d";

						}
						if($acheived_aginst_plan >80 && $acheived_aginst_plan <90){
							$color ="#ffbf00";

						}
						if($acheived_aginst_plan >=0 && $acheived_aginst_plan <=80){
							$color ="#ff0000";

						}
						
						if(count($posValx) > 0 ){
							$final_array_data = array_merge(
							    array_slice($value, 0, 3),  // First two elements from the first array
							    array_slice($posValx, 0, 2) ,                     // Second array
							    array_slice($value, 2),
							    array_slice($posValx, 2)    
							         // Elements after the second position from the first array
							);
						} else{
							$final_array_data = $value;
						}

						
						$sheet_json = json_encode($final_array_data,true);
						$itemd = ItemDescriptionMaster::select('id','title')->where('title',$key)->first();
						$dprImport = new DprImport;
						$dprImport->user_id = $user->id;
						$dprImport->dpr_manage_id = $dprManage->id;
						$dprImport->dpr_config_id = $request->dpr_config_id;
						$dprImport->sheet_json_data = $sheet_json;
						$dprImport->random_no = $random_no;
						$dprImport->item_desc_id       	= @$itemd->id;
						$dprImport->item_desc = $key;
						$dprImport->color_code = $color;
						$dprImport->data_date = date('Y-m-d',strtotime($data_date));
						$dprImport->save();

						//creating upload log
						$dprLog = new DprLog;
						$dprLog->user_id = $user->id;
						$dprLog->dpr_import_id = $dprImport->id;
						$dprLog->dpr_config_id = $request->dpr_config_id;
						$dprLog->import_file = $FileName;
						$dprLog->random_no = $random_no;
						$dprLog->original_import_file = $original_file_name;

						if(env('APP_ENV', 'local')==='production')
		                {
		                    $dprLog->file_path = secure_url('api/file-access/'.$FilePath);
		                }
		                else
		                {
		                    $dprLog->file_path = url('api/file-access/'.$FilePath);
		                }

						$dprLog->data_date = date('Y-m-d',strtotime($data_date));
						$dprLog->save();

						//notify admin
						$notification = new Notification;
						$notification->user_id              = User::first()->id;
						$notification->sender_id            = auth()->id();
						$notification->status_code          = 'success';
			            $notification->type                = 'DprImport';
			            $notification->event                = 'Created';
						$notification->title                = 'New Dpr Uploaded';
						$notification->message              = 'New Dpr Uploaded.';
						$notification->read_status          = false;
						$notification->data_id              = $dprImport->id;
						$notification->save();

					}

					

				}

				$dprData = DprImport::where('data_date',$data_date)->where('dpr_config_id',$request->dpr_config_id)->get();
				
				if($dprData){
					return response(prepareResult(false,$dprData, trans('translate.data_imported')),config('httpcodes.created'));
				} else{
					return response(prepareResult(true, [], trans('translate.something_went_wrong')),config('httpcodes.bad_request'));
				}
			} else{
				return response(prepareResult(true, [], trans('translate.file_required')),config('httpcodes.bad_request'));

			}

            /*$sheetName = $request->sheet_name;
            $import = new DprSelectSheet($request->sheet_name);
            $import->onlySheets($request->sheet_name);
            Excel::import($import,request()->file('file'));
            //Excel::import(new DprSelectSheet($sheetName), request()->file('file'));*/

            
        } catch (Exception $e) {
        	\Log::error($e);
        	return response(prepareResult(true, $e->getMessage(),trans('translate.something_went_wrong')), config('httpcodes.internal_server_error'));
        }
    }
   

    /**
     * show data resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //getting import list
   public function dprImportList(Request $request) {
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
			->groupBy('item_description_masters.id')
			->orderBy('item_description_masters.orderno','ASC');

			if(!empty(auth()->user()->dpr_item_desc_ids))
         {
            $dpr_item_desc_ids = explode(',',auth()->user()->dpr_item_desc_ids);
            $query->whereIn('dpr_imports.item_desc_id',$dpr_item_desc_ids);
         }
    		if(!empty($request->item_desc))
			{
				$query = $query->where('dpr_imports.item_desc_id',$request->item_desc);
			}

			if(!empty($request->dpr_config_id))
         {
             $query = $query->where('dpr_imports.dpr_config_id',$request->dpr_config_id);
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
               if(!empty(auth()->user()->dpr_item_desc_ids))
		         {
		            $dpr_item_desc_ids = explode(',',auth()->user()->dpr_item_desc_ids);
		            $alldpr->whereIn('dpr_imports.item_desc_id',$dpr_item_desc_ids);
		         }
               
               if(!empty(auth()->user()->dpr_config_ids))
               {
                   $dpr_config_ids = explode(',',auth()->user()->dpr_config_ids);
                   $alldpr = $alldpr->whereIn('dpr_imports.dpr_config_id',$dpr_config_ids);
               }
               if(!empty($request->dpr_config_id))
	            {
	                $alldpr = $alldpr->where('dpr_imports.dpr_config_id',$request->dpr_config_id);
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
                    ->where('dpr_imports.item_desc_id', $value->item_desc_id)
                    ->where('dpr_configs.project_id', $dpr->project_id)
                    ->whereDate('dpr_imports.data_date', '<=', $date)
                    ->whereHas('project',function($q){
			              $q->where('status', 1);
			            })
                    ->groupBy('dpr_configs.vendor_id')
                    ->orderBy('dpr_imports.data_date','DESC');
                    
                   

                      
                    /*$latestDataWithProject= NULL;
            	
	            	if(count($allData->get()) <=0){
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
	                }*/
	               if(!empty(auth()->user()->dpr_item_desc_ids))
			         {
			            $dpr_item_desc_ids = explode(',',auth()->user()->dpr_item_desc_ids);
			            $allData->whereIn('dpr_imports.item_desc_id',$dpr_item_desc_ids);
			         }
                  if(!empty(auth()->user()->dpr_config_ids))
                	{
                    	$dpr_config_ids = explode(',',auth()->user()->dpr_config_ids);
	                    $allData->whereIn('dpr_imports.dpr_config_id',$dpr_config_ids);
	               }
	               if(!empty($request->dpr_config_id))
		            {
		                $allData = $allData->where('dpr_imports.dpr_config_id',$request->dpr_config_id);
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

    		
    		return response(prepareResult(false,$dprList, trans('translate.dpr_import_list')),config('httpcodes.success'));

    	} catch (Exception $e) {
    		\Log::error($e);
    		return response(prepareResult(true, $e->getMessage(),trans('translate.something_went_wrong')), config('httpcodes.internal_server_error'));
    	}
    }
    
    /**
     * get pdf/excel/html data of resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //download pdf/ecxel/html file version of imported data
    public function downloadReport(Request $request) {
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
			->groupBy('item_description_masters.id')
			->orderBy('item_description_masters.orderno','ASC');

			if(!empty(auth()->user()->dpr_item_desc_ids))
         {
            $dpr_item_desc_ids = explode(',',auth()->user()->dpr_item_desc_ids);
            $query->whereIn('dpr_imports.item_desc_id',$dpr_item_desc_ids);
         }
         if(!empty($request->item_desc))
         {
             $query = $query->where('dpr_imports.item_desc_id',$request->item_desc);
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
               if(!empty(auth()->user()->dpr_item_desc_ids))
		         {
		            $dpr_item_desc_ids = explode(',',auth()->user()->dpr_item_desc_ids);
		            $alldpr->whereIn('dpr_imports.item_desc_id',$dpr_item_desc_ids);
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
                    ->where('dpr_imports.item_desc_id', $value->item_desc_id)
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

	               if(!empty(auth()->user()->dpr_item_desc_ids))
			         {
			            $dpr_item_desc_ids = explode(',',auth()->user()->dpr_item_desc_ids);
			            $allData->whereIn('dpr_imports.item_desc_id',$dpr_item_desc_ids);
			         }
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
            $type = (!empty($request->type)) ? $request->type :'pdf';

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
                   	$messages ='Excel '.trans('translate.download_excel');

                } elseif($request->type == "html"){

                	$FileName = date('Y-m-d', strtotime($request->date)).'.html';
                    $FilePath = 'pdf/' . $FileName;
                    $html = view('pdfview',compact('dprList', 'date', 'type','packageName'));
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
                    $messages ='Html '.trans('translate.download_excel');


	    			

    			} else{

    				$type ='pdf';
    				$FileName = date('Y-m-d', strtotime($request->date)).'.pdf';
	    			$date = $request->date;
	    			$pdf = PDF::loadView('pdfview',compact('dprList', 'date', 'type','packageName'));
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
	    			$messages ='PDF '.trans('translate.download_excel');
    				
    			}
    			
    			
    			if (env('IS_MAIL_ENABLE', false) == true && !empty($request->email)){
    				$allEmails = explode(",", $request->email);
	    				if ($this->containsEmails($request->email)) {
						    $content = [
		                    "FileName" => $FileName,
		                    "FilePath" => $path,
		                    "mime" => $mime,
		                ];
		                foreach ($allEmails as $key => $email) {
		                	$recevier = Mail::to($email)->send(new sendReportToEmail($content));
		                	
		                }
					} else {

						return response(prepareResult(true, [],'Requested Emails  does not contain email addresses separated by commas', config('httpcodes.bad_request')));
						
					}

					$messages =trans('translate.Mail-sent');
                	
                   
                }
    			return response(prepareResult(false,$callApi,$messages),config('httpcodes.success'));

    		} else{
    			return response(prepareResult(true,[],trans('translate.record_not_found')), config('httpcodes.bad_request'));
    		}



    	} catch (Exception $e) {
    		\Log::error($e);
    		return response(prepareResult(true, $e->getMessage(),trans('translate.something_went_wrong')), config('httpcodes.internal_server_error'));
    	}
    }

    function containsEmails($string) {
	    // Define the regex pattern to match email addresses separated by commas
	    $pattern = '/^\s*([\w+\-].?)+@[a-z\d\-]+(\.[a-z]+)*\.[a-z]+\s*(,\s*([\w+\-].?)+@[a-z\d\-]+(\.[a-z]+)*\.[a-z]+\s*)*$/i';

	    // Check if the string matches the pattern
	    if (preg_match($pattern, $string)) {
	        return true; // The string contains email addresses separated by commas
	    } else {
	        return false; // The string does not contain email addresses separated by commas
	    }
	}

    /**
     * get excel data of resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //download excel file version of imported data on specified date
    public function downloadExcel(Request $request) {
    	try {
    		$validator = \Validator::make($request->all(),[ 
    			'date'     => 'required',
    		]);
    		if ($validator->fails()) {
    			return response(prepareResult(true, $validator->messages(),$validator->messages()->first()), config('httpcodes.bad_request'));
    		}
    		$date = date('Y-m-d',strtotime($request->date));
    		$query = DprImport::where('data_date',$date)->groupBy('work_item')->with('dprManage');
            if(!empty($request->work_item))
            {
                $query->where('work_item',$request->work_item);
            }
            $appSetting = AppSetting::first();
            $query = $query->get();
            $dprList =[];
            foreach ($query as $value) {
                // Decode the JSON data from the column into a PHP array
                $alldpr = DprImport::where('data_date',$date)->where('work_item',$value->work_item)->get();
                $mergedArray = [];
                foreach ($alldpr as $key => $dpr) {
                    if($value->work_item == $dpr->work_item){
                        $jsonData = json_decode($dpr->sheet_json_data, true);
                        $mergedArray = array_merge($mergedArray, $jsonData);
                    }
                }

                if(env('APP_ENV', 'local')==='production')
                {
                    $original_csv =secure_url('user/import/'.@$value->dprManage->original_import_file.'');
                }
                else
                {
                    $original_csv =url('user/import/'.@$value->dprManage->original_import_file.'');
                }
                
                $dprList[] =[
                    'date' => $request->date,
                    'project_name' => @$value->dprConfig->Project->name,
                    'project_status' => @$value->dprConfig->Project->status,
                    'sheet_name' => @$value->dprConfig->sheet_name,
                    'profile_name' => @$value->dprConfig->profile_name,
                    'work_package' => $value->dprConfig->WorkPackage->name,
                    'work_item' => $value->work_item,
                    'work_item' => $value->work_item,
                    'data' => $mergedArray,
                    'file_name' => @$value->dprManage->original_import_file,
                    'original_csv' => $original_csv,
                    
                ];
            }
    		
    		$appSetting = AppSetting::first();

    		if(count($dprList)>0){
    			$FileName = time().'.xlsx';
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

    			return response(prepareResult(false,$callApi, trans('translate.download_excel')),config('httpcodes.success'));

    		} else{
    			return response(prepareResult(true,[],trans('translate.record_not_found')), config('httpcodes.bad_request'));
    		}



    	} catch (Exception $e) {
    		\Log::error($e);
    		return response(prepareResult(true, $e->getMessage(),trans('translate.something_went_wrong')), config('httpcodes.internal_server_error'));
    	}
    }

    /**
     * show data resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //getting work items list
    public function workItemList(Request $request) {
    	try {
    		$query = DprImport::where('work_item','!=','')->distinct(['work_item']);
    		if(!empty($request->per_page_record))
    		{
    		    $perPage = $request->per_page_record;
    		    $page = $request->input('page', 1);
    		    $total = $query->count();
    		    $result = $query->offset(($page - 1) * $perPage)->limit($perPage)->get(['work_item']);

    		    $pagination =  [
    		        'data' => $result,
    		        'total' => $total,
    		        'current_page' => $page,
    		        'per_page' => $perPage,
    		        'last_page' => ceil($total / $perPage)
    		    ];
    		    $query = $pagination;
    		}
    		else
    		{
    		    $query = $query->get(['work_item']);
    		}
    		
    		return response(prepareResult(false,$query, trans('translate.work_item_list')),config('httpcodes.success'));

    	} catch (Exception $e) {
    		\Log::error($e);
    		return response(prepareResult(true, $e->getMessage(),trans('translate.something_went_wrong')), config('httpcodes.internal_server_error'));
    	}
    }

    public function destroy(Request $request)
    {
        try {
        	$validator = \Validator::make($request->all(),[ 
    			'random_no'      		=> 'required',
    			
    		]);
    		if ($validator->fails()) {
    			return response(prepareResult(true, $validator->messages(),$validator->messages()->first()), config('httpcodes.bad_request'));
    		}
    		
            $dprImport = DprImport::where('random_no',$request->random_no)->first();
           
            if (!is_object($dprImport)) {
                 return response(prepareResult(true, [],'This record is not of the selected date '), config('httpcodes.not_found'));
            }
           
          	$deleteDprManage = \DB::table('dpr_manages')->where('id',$dprImport->dpr_manage_id)->delete();
            $deleteddprImport = DprImport::where('random_no',$request->random_no)->delete();

            $getDprLog = DprLog::where('random_no', $request->random_no)->get();

			/* Log for delete dpr import */
			if ($getDprLog->count() > 0) {
			    foreach ($getDprLog as $log) {
			        $dprLog = new DprLog;
			        $dprLog->user_id = auth()->user()->id; // Use auth()->user()->id to get the currently logged-in user ID
			        $dprLog->dpr_import_id = $log->dpr_import_id;
			        $dprLog->dpr_config_id = $log->dpr_config_id;
			        $dprLog->import_file = $log->import_file;
			        $dprLog->random_no = $log->random_no;
			        $dprLog->original_import_file = $log->original_import_file;
			        $dprLog->status = '0'; // Make sure to add a semicolon here

			        if (env('APP_ENV', 'local') === 'production') {
			            $dprLog->file_path = secure_url('api/file-access/' . $log->import_file);
			        } else {
			            $dprLog->file_path = url('api/file-access/' . $log->import_file);
			        }

			        $dprLog->data_date = date('Y-m-d', strtotime($log->data_date));
			        $dprLog->save();
			    }
			}

            
            return response(prepareResult(false, [], trans('translate.dpr_import_deleted')), config('httpcodes.success'));
        }
        catch(Exception $e) {
            return response(prepareResult(true, $e->getMessage(), trans('translate.something_went_wrong')), config('httpcodes.internal_server_error'));
        }
    }

    public function summaryReport(Request $request)
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
                if(!empty($request->dpr_config_id))
                {
                    $dprUploads = $dprUploads->where('dpr_imports.dpr_config_id', $request->dpr_config_id);
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
                elseif($request->type == 'delete'){
                	$data[] = [
                		"date" => $org_date,
	                	"name" =>  $work_date_name, //$original_csv,
	                	"link" =>  $dpr_link, //$name_link,
	                	"type" =>  $request->type,
	                	"random_no" =>  @$dprUploads->random_no,

	                ];

                }

                else{
                	if($dprUploads !=''){
                		$data[] = [
		                	"date" => $org_date,
		                	"name" =>  $name_link,
		                	"type" =>  $request->type,

		                ];

                	}

                }
                

			 }
			    return response(prepareResult(false, $data, trans('translate.dpr_summery_report')), config('httpcodes.success'));

		} catch(Exception $exception) {
            return response(prepareResult(false, $exception->getMessage(),trans('translate.something_went_wrong')), config('httpcodes.internal_server_error'));   
        } 
    }
    public function downloadSummaryReport(Request $request)
    {
        try
        {

         $startDate = new \DateTime($request->from_date);
			$endDate = new \DateTime($request->to_date);

			// Increment the end date by one day to include it in the range
			$endDate->modify('+1 day');

			$interval = new \DateInterval('P1D'); // 1 day interval
			$dateRange = new \DatePeriod($startDate, $interval, $endDate);
			$summeryReporr  =[];
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
                if(!empty($request->dpr_config_id))
                {
                    $dprUploads = $dprUploads->where('dpr_imports.dpr_config_id', $request->dpr_config_id);
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
               
                $work_date_name = (!empty($dprUploads)) ? @$dprUploads->work_pack_name.' '.$date_a :'Not Submitted';
                $link =  '<a href="'.$original_csv.'">'.$work_date_name.'</a>';
                $name_link = (!empty($dprUploads)) ? $link :'Not Submitted';
 
               $dpr_link = (!empty($dprUploads)) ? $original_csv : NULL;
          
             	$summeryReporr[] = [
             		"date" => $org_date,
                	"name" =>  $work_date_name,
                	"link" =>  $dpr_link,
                	"type" =>  $request->type,

                ];

           

			 }
			$appSetting = AppSetting::first();
    		if(count($summeryReporr)>0){
    			$FileName = 'summery-report-'.time().'.xlsx';
    			$FilePath = 'excel/'.$FileName;
    			$dataRows =  [
                    'from_date' => $request->from_date,
                    'to_date' => $request->to_date,
                    'summeryReporr' => $summeryReporr,
                    'appSetting' => $appSetting,
                ];

    			$html = view('summeryExcelview',$dataRows)->render();
             Excel::store(new SummeryExport($dataRows),$FilePath, 'excel_uploads');
             if(env('APP_ENV', 'local')==='production')
             {
                 $callApi = secure_url('api/file-access/'.$FilePath);
             }
             else
             {
                 $callApi = url('api/file-access/'.$FilePath);
             }

    			return response(prepareResult(false,$callApi, trans('translate.dpr_summery_report')),config('httpcodes.success'));

    		} else{
    			return response(prepareResult(true,[],trans('translate.record_not_found')), config('httpcodes.bad_request'));
    		}
		

		} catch(Exception $exception) {
            return response(prepareResult(false, $exception->getMessage(),trans('translate.something_went_wrong')), config('httpcodes.internal_server_error'));   
        } 
    }
    
}