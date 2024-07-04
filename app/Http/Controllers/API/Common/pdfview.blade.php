<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>KPMG DPR Report</title>
    <style type="text/css">
      .clearfix:after {
        content: "";
        display: table;
        clear: both;
      }

      a {
        color: #0087C3;
        text-decoration: none;
      }

      body {
        margin: 0 auto;
        color: #555555;
        background: #FFFFFF;
        font-family: opensanscondensed;
        font-size: 14px;
      }

      table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        overflow: hidden;
        border: #00338d 3px solid;
      }

      table td,
      table th {
        border-top: 1px solid #ecf0f1;
        padding: 10px;
      }

      table thead tr.orange {
        color: #FFFFFF !important;
        font-size: 16px !important;
        background: #00338d !important;
      }

      .orange {
        color: #FFFFFF !important;
        font-weight: bold;
      }

      .gray {
        color: #000 !important;
        font-size: 14px !important;
        background: #e7e9ee !important;
      }
      .blue {
        color: #FFFFFF !important;
        font-size: 14px !important;
        background: #00338d !important;
      }

      table td {
        border-left: 1px solid #ecf0f1;
        border-right: 1px solid #ecf0f1;
      }

      table tr:nth-of-type(even) td,
      table tr:nth-of-type(odd) td {
        background-color: #e7e9ee;
      }

      #thanks {
        font-size: 20px !important;
        text-align: center;
      }

      .boxhead a {
          color: #0087C3;
          text-decoration: none;
      }
      .text-left{
        text-align: left;
      }
      .text-right{
        text-align: right;
      }
     
    </style>
  </head>
  <body>
    <header class="clearfix">
      <div id="company">
         @php
            $appSetting = App\Models\AppSetting::first();
         @endphp
        <h2 class="name" style="text-align: center; margin-right: 25px; color: #00338d">
            <img src="{{ $appSetting->app_logo }}" width="150"><br>
             {{ $appSetting->app_name }}
        </h2>
      </div>
    </header>
    <main> 
      
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      Please find the Daily Progress Report for {{ date('d M Y', strtotime($date)) }} below:
      <br>
      <br>
      <br>
      <table>
        <tr>
           <th>
           </th>
        <th>
           <span style="display: inline-block; width: 25px; height: 25px;">
              <svg width="25" height="25" xmlns="http://www.w3.org/2000/svg">
                  <circle cx="12.5" cy="12.5" r="12.5" fill="#008000"/>
              </svg>
          </span> % Achievement: 90% & Above

       
      </th>
      <th>
        <span style="display: inline-block; width: 25px; height: 25px;">
              <svg width="25" height="25" xmlns="http://www.w3.org/2000/svg">
                  <circle cx="12.5" cy="12.5" r="12.5" fill="#c9c916"/>
              </svg>
          </span>
        % Achievement: 80% to 90%
      </th>
       <th>
        <span style="display: inline-block; width: 25px; height: 25px;">
              <svg width="25" height="25" xmlns="http://www.w3.org/2000/svg">
                  <circle cx="12.5" cy="12.5" r="12.5" fill="#ff0000"/>
              </svg>
          </span>
        % Achievement: 0% to 80%
      </th>
        <th>
          <span style="display: inline-block; width: 25px; height: 25px;">
              <svg width="25" height="25" xmlns="http://www.w3.org/2000/svg">
                  <circle cx="12.5" cy="12.5" r="12.5" fill="#ee82ee"/>
              </svg>
          </span>
         Non Submission
      </th>
       <th></th>
    </tr>
    </table>
      @foreach($dprList as $key => $dpr)
      <?php
       
        $totalValue =0;
        $array1 = (!empty(@$dpr['item_data'][0]['data'][0])) ? array_keys(@$dpr['item_data'][0]['data'][0]):[];

        $array2 = ['vendor_name', 'project_name', 'project_status', 'file_name','original_csv'];
        $array_keys = array_diff($array1, $array2);

        $dataCount = count(@$dpr['item_data']);
        $arraySize = sizeof($array_keys);
        $colValue = $arraySize-2;
       
        $unit_of_measure = (!empty(@$dpr['unit_of_measure'])) ? '('.@$dpr['unit_of_measure'].')': NULL;

      ?>
      <table width="100%" style="padding: 10px 20px;">
        <thead>
          <tr class="orange">
            <th class="orange text-left" colspan="{{ $colValue }}">
             <strong>Work Item: {{ !empty(@$dpr['work_item']) ? @$dpr['work_item']: "-" }} {{ $unit_of_measure }}<strong>
            </th>
            <th class="orange text-right" colspan="">
              
            </th>
            <th class="orange text-right" colspan="2">
            <strong> Date &nbsp;&nbsp;&nbsp; {{ date('d M Y', strtotime($date)) }}</strong>
            </th>
          </tr>
           @if(count(@$dpr['item_data']) >0)
          <tr>
            <th class="blue" width="15%">
              <div>Project</div>
      
           </th>

           @foreach($array_keys as $nkey => $kval) 
            
            <th class="blue" width="15%">
              <div>{{ $kval }}</div>
      
           </th>
           @endforeach
          </tr>
          <tr>
            <th class="desc text-left"></th>
            <th class="desc text-left">A</th>
            <th class="desc text-left">B</th>
            <th class="desc text-left">C</th>
            <th class="desc text-left">D=(C/A)%</th>
            <th class="desc text-left">E=A-C</th>
            <th class="desc text-left">F</th>
            <th class="desc text-left">G</th>
            <th class="desc text-left">H</th>
            <th class="desc text-left">I=(H/(F/26))%</th>
        </tr>
          @endif
         
        </thead>
      
      <tbody> 
        @if(count(@$dpr['item_data']) >0)
        @foreach(@$dpr['item_data'] as $nkey => $item) 
        
        <tr>
            <td class="desc">
                <strong>
                  {{ @$item['project_name'] }}
                 
                </strong>
            </td>
             <?php
             $color = @$item['color_code'];

            $totalarray =[];
             $result = [];
              foreach (@$item['data'] as $array) {
                  foreach ($array as $key => $value) {
                      if (!isset($result[$key])) {
                          $result[$key] = $value;
                      } else {
                        if(is_numeric($result[$key])){

                          $result[$key] += $value;
                        }
                      }
                  }
              }


             ?>
             @foreach($array_keys as $nkey => $kval) 
             <?php

                $dval =  '-';
                if(is_numeric(@$result[$kval])){
                  $dval = number_format(@$result[$kval]);
                  
                
                }
                $color_org = ($item['is_dpr_submit'] == false) ? '' : $color;
                if($kval=='Plan FTM' || $kval=='Achieved FTM' || $kval=='Achieved FTD' || $kval=='% Achievement Against Plan'){
                  $dval = ($item['is_dpr_submit'] == false) ? '-' : $dval;
                 
                }
                $perSign = ($item['is_dpr_submit'] == false) ? '' :'%';


            ?>
            @if($kval=='Plan FTM' || $kval=='Achieved FTM' || $kval=='Achieved FTD' || $kval=='% Achievement Against Plan')
                @if(strtolower($kval)==strtolower("% Complete"))
                <td class="desc"><strong>{{ $dval }} %  tesssss</strong></td>
                @elseif($kval=="% Achievement Against Plan")

                <td class="desc" style="background:{{ $color_org }} !important" ><strong>{{ $dval }} {{ $perSign }}</strong></td>
                @else
                 <td class="desc"><strong>{{ $dval }}  </strong></td>
                @endif
            @else
             <td class="desc"><strong>{{ $dval }}  </strong></td>
            @endif
           
            @endforeach
           
          </tr> 

          @foreach(@$item['data'] as $vkey => $data) 
         
         <tr>
          <td class="desc">
                
              <a href="{{ @$data['original_csv'] }}" download>
                  {{ @$data['vendor_name'] }}
                  @if($data['project_status']=='2')
                  (inactive)
                  @endif
                
              </a>
             
            </td>
           @foreach($array_keys as $nkey => $kval) 

            <?php
            
              $dataValue =  @$data[$kval];
              if(is_numeric(@$data[$kval])){
                $dataValue = number_format(@$data[$kval]);
              }

              if($kval=='Plan FTM' || $kval=='Achieved FTM' || $kval=='Achieved FTD' || $kval=='% Achievement Against Plan'){
                  $dataValue = (@$item['is_dpr_submit'] == false) ? '-' : $dataValue;
                 
                }

                $color_org = (@$item['is_dpr_submit'] == false) ? '' : $color;
                $is_color_org = (@$item['is_dpr_submit'] == false) ? '#ee82ee' : '';
                $perSign = (@$item['is_dpr_submit'] == false) ? '' :'%';
            ?>
            @if($kval=='Plan FTM' || $kval=='Achieved FTM' || $kval=='Achieved FTD' || $kval=='% Achievement Against Plan')

               @if(strtolower($kval)==strtolower("% Complete"))
              <td class="desc">{{ $dataValue }} % tessssstessssstesssss1</td>
               @elseif($kval=="% Achievement Against Plan")
              <td class="desc" style="background:{{ $color_org }}  !important">{{ $dataValue }} {{ $perSign }}</td>
              @elseif($kval=="Achieved FTD")
                <td class="desc" style="background:{{ $is_color_org }} !important" ><strong>{{ $dataValue }}  </strong></td>
              @else
               <td class="desc">{{ $dataValue }} </td>
              @endif
            @else
             <td class="desc">{{ $dataValue }} </td>
            @endif
             @endforeach
          </tr> 
          @endforeach 

          
          @endforeach 
          </tr>
          <tr>
          <td class="desc"><strong>Total</strong></td>
           @foreach($array_keys as $nkey1 => $keyvalue) 
           
            <?php
               $sresult = [];
              foreach (@$dpr['item_data'] as $arrays) {
                foreach (@$arrays['data'] as $arrays) {
                    foreach ($arrays as $keys => $values) {
                        if (!isset($sresult[$keys])) {
                            $sresult[$keys] = $values;
                        } else {
                          if(is_numeric($sresult[$keys])){

                            $sresult[$keys] += $values;
                          }
                        }
                    }
                  }
              }

              $totalValue =  '-';
                if(is_numeric(@$sresult[$keyvalue])){
                  $totalValue = number_format(@$sresult[$keyvalue]);
                  
                
                }


                if($keyvalue=='Plan FTM' || $keyvalue=='Achieved FTM' || $keyvalue=='Achieved FTD' || $keyvalue=='% Achievement Against Plan'){
                  $totalValue = (@$item['is_dpr_submit'] == false) ? '-' : $totalValue;
                 
                }
                $color_org = (@$item['is_dpr_submit'] == false) ? '' : $color;
                $perSign = (@$item['is_dpr_submit'] == false) ? '' :'%';
               

               ?>
          @if($keyvalue=='Plan FTM' || $keyvalue=='Achieved FTM' || $keyvalue=='Achieved FTD' || $keyvalue=='% Achievement Against Plan')
               @if(strtolower($keyvalue)==strtolower("% Complete"))
              <td class="desc"><strong>{{ $totalValue }} %fhgj </strong></td>
              @elseif($keyvalue=="% Achievement Against Plan")
               <td class="desc" style="background:{{ $color_org }} !important"><strong>{{ $totalValue }} {{ $perSign }}</strong></td>
               @else
                 <td class="desc"><strong>{{ $totalValue }} </strong></td>
               @endif
          @else
          <td class="desc"><strong>{{ $totalValue }} </strong></td>
          @endif
           @endforeach 
          <tr>
         @endif
        </tbody>
        
      </table> 
      <br />
      @endforeach 
      <!-- <div id="thanks">Thank you!</div> -->
      <div>
          <small>Note: This is a system generated mail. Please do not reply on this,</small><br><br>
Regards,<br>
KPMG PIVOT Team<br>
<a href="mailto:in-fmpivotsupport@kpmg.com">in-fmpivotsupport@kpmg.com</a><br>
<hr>
2023 KPMG International Cooperative<br>
<hr>
KPMG (in India) allows reasonable personal use of the e-mail system. Views and opinions expressed in these communications do not necessarily represent those of KPMG (in India).<br>

******************************************************************************************************************************************************************************************************<br>
DISCLAIMER<br>
The information in this e-mail is confidential and may be legally privileged. It is intended solely for the addressee. Access to this e-mail by anyone else is unauthorized. If you have received this communication in error, please address with the subject heading "Received in error," send to postmaster1@kpmg.com, then delete the e-mail and destroy any copies of it. If you are not the intended recipient, any disclosure, copying, distribution or any action taken or omitted to be taken in reliance on it, is prohibited and may be unlawful. Any opinions or advice contained in this e-mail are subject to the terms and conditions expressed in the governing KPMG client engagement letter. Opinions, conclusions and other information in this e-mail and any attachments that do not relate to the official business of the firm are neither given nor endorsed by it.
<br><br>
KPMG cannot guarantee that e-mail communications are secure or error-free, as information could be intercepted, corrupted, amended, lost, destroyed, arrive late or incomplete, or contain viruses.
<br><br>
KPMG, an Indian partnership and a member firm of KPMG International Cooperative ("KPMG International"), an English entity that serves as a coordinating entity for a network of independent firms operating under the KPMG name. KPMG International Cooperative (“KPMG International”) provides no services to clients. Each member firm of KPMG International Cooperative (“KPMG International”) is a legally distinct and separate entity and each describes itself as such.
<br><br>
"Notwithstanding anything inconsistent contained in the meeting invite to which this acceptance pertains, this acceptance is restricted solely to confirming my availability for the proposed call and should not be construed in any manner as acceptance of any other terms or conditions. Specifically, nothing contained herein may be construed as an acceptance (or deemed acceptance) of any request or notification for recording of the call, which can be done only if it is based on my explicit and written consent and subject to the terms and conditions on which such consent has been granted"<br>
******************************************************************************************************************************************************************************************************

      </div>
    </main>
  </body>
</html>