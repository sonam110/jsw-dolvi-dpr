<?php
use App\Models\User;

function prepareResult($error, $data, $msg)
{
    return ['error' => $error, 'data' => $data, 'message' => $msg];
}


function generateRandomString($len = 12) {
    return Str::random($len);
}

function timeDiff($time)
{
    return strtotime($time) - time();
}

function getUser() {
    return auth('api')->user();
}

function checkUserExist($email)
{
   $users = User::select('email')->get();
   foreach ($users as $key => $user) 
   {
     if($email==$user->email)
     {
        return true;
     }
   }
   return false;
}

function checkActiveUserExist($email)
{
   $users = User::select('email', 'status')->get();
   foreach ($users as $key => $user) 
   {
     if($email==$user->email)
     {
        return ($user->status==1) ? true : false;
     }
   }
   return true;
}
function userDetail($email)
{
   $users = User::select('email','id','dpr_item_desc_ids')->get();
   foreach ($users as $key => $user) 
   {
     if($email==$user->email)
     {
        return $user;
     }
   }
   return NUll;
}

function validatePassword($val) {
  $re = array();
  if ($val) {
  	// check password must contain at least one number
      if (preg_match('/\d/', $val)) {
        array_push($re, true);
      }
      // check password must contain at least one special character
      if (preg_match('/[!@#$%^&*(),.?":{}|<>]/', $val)) {
        array_push($re, true);
      }
      // check password must contain at least one uppercase letter
      if (preg_match('/[A-Z]/', $val)) {
        array_push($re, true);
      }
      // check password must contain at least one lowercase letter
      if (preg_match('/[a-z]/', $val)) {
        array_push($re, true);
      }
  }
  return count($re) >= 3;
}

function getColorCode($acheived_aginst_plan=NULL){

  $color = "";
  $acheived_aginst_plan = str_replace(',','',$acheived_aginst_plan);
  if($acheived_aginst_plan >=90){
    $color ="#6de26d";

  }
  if($acheived_aginst_plan >80 && $acheived_aginst_plan <90){
    $color ="#ffbf00";

  }
  if($acheived_aginst_plan >=0 && $acheived_aginst_plan <=80){
    $color ="#ff0000";
   // \Log::info($acheived_aginst_plan);

  }
  return $color;

}
