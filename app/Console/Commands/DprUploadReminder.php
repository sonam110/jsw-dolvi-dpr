<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mail;
use App\Mail\DprUploadReminderMail;
use App\Models\User;
use App\Models\DprImport;
use Illuminate\Contracts\Encryption\DecryptException;

class DprUploadReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:dpr-upload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command informs all the vendors who have not uploaded the DPR report till 09:30 AM.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        \Log::channel('automation_emails_log')->info('Dpr Uplaod Reminder Mails:'.date('Y-m-d'));
        $allProjectWiseUserEmails = \DB::table('projects')->select('reminder_emails')->whereNotNull('reminder_emails')->get();
        $emails =[];
        foreach ($allProjectWiseUserEmails as $key => $email) {
            $expodedEmails = explode(',', $email->reminder_emails);
            foreach ($expodedEmails as $key => $value) {
                $checkActiveUserExist  = checkActiveUserExist($value);
                if($checkActiveUserExist == true){
                    $emails[] = $value;
                }
            }
        }
        $uniqueEmails = array_unique($emails);

        foreach ($uniqueEmails as $key => $email) {
            $getAssigneProjects = \DB::table('projects')->where('dpr_report_emails','like','%'.$email.'%')->pluck('id');

            $dprImport = DprImport::select('dpr_configs.*','dpr_imports.*', 'dpr_imports.id as id','dpr_configs.id as dpr_config_id')
                ->join('dpr_configs','dpr_imports.dpr_config_id','dpr_configs.id')
                ->whereDate('dpr_imports.data_date', date('Y-m-d'))
            ->whereIn('dpr_configs.project_id',$getAssigneProjects)
            ->count();
            // \Log::info($dprImport);
            if($dprImport <=0)
            {
                $content = [
                    "name" => 'User',
                    "body" => 'Please submit your DPR report before 10:00 AM.',
                ];
                if (env('IS_MAIL_ENABLE', false) == true) {
                
                    $recevier = Mail::to($email)->send(new DprUploadReminderMail($content));
                    \Log::channel('automation_emails_log')->info($email);
                }
                sleep(2);
                
            }
        }
        return;
    }
}
