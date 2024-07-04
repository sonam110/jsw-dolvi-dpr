<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;
use App\Models\OverallReportMail;
use Validator;
use Auth;
use Exception;
use DB;
class OverAllReportEmailController extends Controller
{
      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //listing vendors
    public function reportEmails(Request $request)
    {
        try {
            $column = 'id';
            $dir = 'Desc';
            if(!empty($request->sort))
            {
                if(!empty($request->sort['column']))
                {
                    $column = $request->sort['column'];
                }
                if(!empty($request->sort['dir']))
                {
                    $dir = $request->sort['dir'];
                }
            }
            $query = OverallReportMail::orderby($column,$dir);
            
            if(!empty($request->email))
            {
                $query->where('email', 'LIKE', '%'.$request->email.'%');
            }
        
           
            if(!empty($request->per_page_record))
            {
                $perPage = $request->per_page_record;
                $page = $request->input('page', 1);
                $total = $query->count();
                $result = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();

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
                $query = $query->get();
            }

            return response(prepareResult(false, $query, 'Email List'), config('httpcodes.success'));
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
    //creating new vendor
     public function store(Request $request)
    {
       
        $validation = \Validator::make($request->all(), [
            'email'     => 'required|email|unique:overall_report_mails,email',
        ]);

        if ($validation->fails()) {
            return response(prepareResult(true, $validation->messages(), $validation->messages()->first()), config('httpcodes.bad_request'));
        }

        DB::beginTransaction();
        try {
            $addEmail = new OverallReportMail;
            $addEmail->email = $request->email;
            $addEmail->status = $request->status ? $request->status : 1;
            $addEmail->save();

            //notify admin about new vendor
            $notification = new Notification;
            $notification->user_id              = User::first()->id;
            $notification->sender_id            = auth()->id();
            $notification->status_code          = 'success';
            $notification->type                = 'OverallReportMail';
            $notification->event                = 'Created';
            $notification->title                = 'New Email Created';
            $notification->message              = 'New Email '.$addEmail->name.' Added.';
            $notification->read_status          = false;
            $notification->data_id              = $addEmail->id;
            $notification->save();

            DB::commit();
            return response(prepareResult(false, $addEmail, 'Email Created'),config('httpcodes.created'));
        } catch (\Throwable $e) {
            \Log::error($e);
            DB::rollback();
            return response(prepareResult(true, $e->getMessage(), trans('translate.something_went_wrong')), config('httpcodes.internal_server_error'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //view vendor
    public function show(OverallReportMail $OverallReportMail)
    {
        try
        {
            return response(prepareResult(false, $OverallReportMail, 'Email Detail'), config('httpcodes.success'));
        } catch (\Throwable $e) {
            \Log::error($e);
            return response(prepareResult(true, $e->getMessage(), trans('translate.something_went_wrong')), config('httpcodes.internal_server_error'));
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //update vendor
    public function update(Request $request, $id)
    {
        $validation = \Validator::make($request->all(), [
            'email'     => 'email|required|unique:overall_report_mails,email,'.$id,
        ]);

        if ($validation->fails()) {
            return response(prepareResult(true, $validation->messages(), $validation->messages()->first()), config('httpcodes.bad_request'));
        }

        DB::beginTransaction();
        try {
            $updateEmail = OverallReportMail::where('id',$id)->first();
            if(!$updateEmail)
            {
                return response(prepareResult(true, [],trans('translate.record_not_found')), config('httpcodes.not_found'));
            }
            $updateEmail->email = $request->email;
            $updateEmail->status = $request->status ? $request->status : 1;
            $updateEmail->save();

            DB::commit();
            return response(prepareResult(false, $updateEmail, 'Email Updated'),config('httpcodes.success'));
        } catch (\Throwable $e) {
            \Log::error($e);
            DB::rollback();
            return response(prepareResult(true, $e->getMessage(), trans('translate.something_went_wrong')), config('httpcodes.internal_server_error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //delete vendor
    public function destroy($id)
    {
        try {
            $reportEmail= OverallReportMail::where('id',$id)->first();
            if (!is_object($reportEmail)) {
                 return response(prepareResult(true, [],trans('translate.record_not_found')), config('httpcodes.not_found'));
            }
           
            $deleteOrg = $reportEmail->delete();
            return response(prepareResult(false, [], 'Deleted Successfully'), config('httpcodes.success'));
        }
        catch(Exception $e) {
            return response(prepareResult(true, $e->getMessage(), trans('translate.something_went_wrong')), config('httpcodes.internal_server_error'));
        }
    }

}
