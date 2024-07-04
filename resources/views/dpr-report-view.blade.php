
@foreach($dprList as $key => $dpr)
      <?php
        
        $projectCount =0;
        $totalValue =0;
        $wdtd = 0;
        $scopetotal = 0;
        $complete_per = 0;
        $aftd = 0;
        $planftm = 0;
        $acheivedaginstplan = 0;
        
        $wdtdhead = 0;
        $scopetotahead = 0;
        $complete_per_head = 0;
        $aftdhead = 0;
        $planftmhead = 0;
        $acheivedaginstplanhead = 0;

        $aftdmid = 0;
        $planftmmid = 0;
        $acheivedaginstplanmid = 0;
        
        $columArray = (!empty(@$dpr['item_data'][0]['data'][0])) ? array_keys(@$dpr['item_data'][0]['data'][0]):[];
 

        $fixkey =  ['Scope','Drawing Release','Work Done Till Date','% Complete','Balance','Plan FTM','Achieved FTM','Achieved FTD','% Achievement Against Plan'];

        $mergeArray = array_merge($fixkey,$columArray);
        $array1 = array_unique($mergeArray);

        $array2 = ['is_dpr_submit','is_this_month_submit','vendor_name', 'project_name', 'project_status', 'file_name','original_csv'];
        $array_keys = array_diff($array1, $array2);

        $dataCount = count(@$dpr['item_data']);
        $arraySize = sizeof($array_keys);
        
        $array_diff_result = array_diff($array1, $array2);
       
         $colValue =  $arraySize-2;
       
       
        $unit_of_measure = (!empty(@$dpr['unit_of_measure'])) ? '('.@$dpr['unit_of_measure'].')': NULL;
              
        // Get the total number of days in the month for the given date
        $totalDaysInMonth = (int) date('t', strtotime($date));

      // Get the day value (1-31) for the given date
        $dayValue = (int) date('d', strtotime($date));
        ?>
<div class="card">
    <h6 class="card-header" >Work Item: {{ !empty(@$dpr['work_item']) ? @$dpr['work_item']: "-" }} {{ $unit_of_measure }}
      <span class="date-dd text-left">Date: {{ date('d M Y', strtotime($date)) }}</span></h6>

    
            
    <div class="card-body">
      <div class="table-responsive text-nowrap">
        <table class="table table-bordered">
          <thead class="table-light">
            @if(count(@$dpr['item_data']) >0)
           <tr>
            <th class="blue" width="15%">
              <div>Project</div>
      
           </th>
           @foreach($array_keys as $nkey => $kval) 
             @if($kval=='Plan FTM' || $kval=='Achieved FTM' || $kval=='Achieved FTD' || $kval=='% Achievement Against Plan' )
            <th class="blue">
              <div>{{ $kval }}</div>
      
           </th>
           @else
           <th class="blue" >
              <div>{{ $kval }}</div>
      
           </th>
          
           @endif
           @endforeach
          </tr>
           <tr>
            <th class="desc"></th>
            <th class="desc">A</th>
            <th class="desc">B</th>
            <th class="desc">C</th>
            <th class="desc">D=(C/A)%</th>
            <th class="desc">E=A-C</th>
            <th class="desc">F</th>
            <th class="desc">G</th>
            <th class="desc">H</th>
            <th class="desc">I=(G/F*{{ $dayValue }} /{{ $totalDaysInMonth }})%</th>
        </tr>
          @endif
          </thead>
          <tbody>
           @if(count(@$dpr['item_data']) >0)
           @foreach(@$dpr['item_data'] as $nkey => $item) 
            <tr>
              <td class="bold">
                {{ @$item['project_name'] }}
              </td>
              <?php
             $color = @$item['color_code'];
             
            $projectCount = count(@$item['data']);
            $totalarray =[];
             $result = [];
             
              foreach (@$item['data'] as $arrayd) {
                  foreach ($arrayd as $key => $value) {
                     
                      if (!isset($result[$key])) {
                           if( strtolower($key)==strtolower('Achieved FTD')){
                                 $dvalue = (@$arrayd['is_dpr_submit'] == false ) ? 0 : $value;
                                 
                            }
                            elseif(strtolower($key)==strtolower('Achieved FTD') || strtolower($key)==strtolower('Achieved FTM') ||  strtolower($key)==strtolower('Plan FTM') ||  strtolower($key)==strtolower('% Achievement Against Plan')){
                                 $dvalue = (@$arrayd['is_this_month_submit'] == false ) ? 0 : $value;
                                 
                            } else{
                                $dvalue = $value;
                            }
                          $result[$key] = $dvalue;
                      } else {
                        if(is_numeric($result[$key])){
                             if( strtolower($key)==strtolower('Achieved FTD')){
                                 $dvalue = (@$arrayd['is_dpr_submit'] == false ) ? 0 : $value;
                                 
                            }
                          elseif(strtolower($key)==strtolower('Achieved FTD') || strtolower($key)==strtolower('Achieved FTM') ||  strtolower($key)==strtolower('Plan FTM') ||  strtolower($key)==strtolower('% Achievement Against Plan')){
                                 $dvalue = (@$arrayd['is_this_month_submit'] == false ) ? 0 : $value;
                                 
                            } else{
                                $dvalue = $value;
                            }
                          $result[$key] += $dvalue;
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
                
                if(strtolower($kval)==strtolower('Plan FTM') || strtolower($kval)==strtolower('Achieved FTM')||  strtolower($kval)==strtolower('Achieved FTD') || strtolower($kval)==strtolower('% Achievement Against Plan')){
            
                  $dval = ($dval == '0') ? '-' : $dval;
                 
                }
               
                $perComplete = ($kval =="% Complete" || $kval=='% Achievement Against Plan') ? '%' :'';
                

            ?>
             
            @if(strtolower($kval)==strtolower('Plan FTM') || strtolower($kval)==strtolower('Achieved FTM')  || strtolower($kval)==strtolower('Achieved FTD') || strtolower($kval)==strtolower('% Achievement Against Plan'))
                
                
                @if($kval=='Achieved FTM')
                    @php $aftdhead = str_replace(',','',$dval) @endphp
                @endif
                @if($kval=='Plan FTM')
                    @php $planftmhead =str_replace(',','',$dval) @endphp
                @endif
                
                @if(strtolower($kval) == strtolower("% Achievement Against Plan")  )
                <?php
                        $acheivedaginstplanhead = ($aftdhead>0 && $planftmhead>0) ? round((($aftdhead/($planftmhead*($dayValue/$totalDaysInMonth)))*100),0) :0;
                        $color_plan_code = (  $acheivedaginstplanhead =='0' ) ? '' : getColorCode($acheivedaginstplanhead);
                        $acheived_aginst_plan = ( $acheivedaginstplanhead =='0' ) ? '-' : $acheivedaginstplanhead;
                    ?>
                 <td class="desc" style="background:{{ $color_plan_code }} !important" ><strong>{{ ($acheived_aginst_plan=='-') ? '-' : $acheived_aginst_plan.' %' }}  </strong></td>
                @else
                 <td class="desc"><strong>{{ $dval }} {{ $perComplete }} </strong></td>
                @endif
             @else
            
              @if($kval=='Work Done Till Date')
                 @php $wdtdhead = str_replace(',','',$dval) @endphp
              @endif
              @if($kval=='Scope')
                @php $scopetotalhead =str_replace(',','',$dval) @endphp
              @endif
              @if($wdtdhead>0 && $scopetotalhead >0)
              @php 
              
              $complete_per_head = round((($wdtdhead / $scopetotalhead)*100),0); 
              
              @endphp
              @endif
            
            
              @if(strtolower($kval)==strtolower("% Complete"))
              <td class="desc"><strong>{{ ($dval > 0 ) ? $complete_per_head : 0 }} {{ $perComplete }}</strong></td>
                 @else
              <td class="desc"><strong>{{ $dval }} {{ $perComplete }} </strong></td>
              @endif
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
            @foreach($array_keys as $nkey => $kval1) 

            <?php
            
            
              $dataValue =  '-';
             
              if(is_numeric(@$data[$kval1])){
                $dataValue = number_format(@$data[$kval1]);
              }
              
                if(strtolower($kval1)==strtolower('Achieved FTD') || strtolower($kval1)==strtolower('Achieved FTM') ||  strtolower($kval1)==strtolower('Plan FTM') ||  strtolower($kval1)==strtolower('% Achievement Against Plan')){
                     $dataValue = (@$data['is_this_month_submit'] == false ) ? '-' : $dataValue;
                     
                } else{
                    $dataValue = $dataValue;
                }

                 /* 
                $color_org = (@$data['is_dpr_submit'] == false) ?  $color: $color;
                $perSign = (@$data['is_dpr_submit'] == false) ? '%' :'%';
                */
                $is_color_org = (@$data['is_dpr_submit'] == false) ? '#808080' : '';
                if(strtolower($kval1)==strtolower('Plan FTM')  || strtolower($kval1)==strtolower('Achieved FTM') || strtolower($kval1)==strtolower('Achieved FTD') || strtolower($kval1)==strtolower('% Achievement Against Plan'))
                {

                  $dataValue = ($dataValue == '0') ? '-' : $dataValue;
                 
                }
                if(strtolower($kval1)==strtolower('Achieved FTD')){
                      $dataValue = ($data['is_dpr_submit'] == false) ? '-' : $dataValue;
                }
                $perComplete = ($kval1 =="% Complete" || $kval1=='% Achievement Against Plan') ? '%' :'';
               
            ?>
           @if(strtolower($kval1)==strtolower('Plan FTM')  || strtolower($kval1)==strtolower('Achieved FTM') || strtolower($kval1)==strtolower('Achieved FTD') || strtolower($kval1)==strtolower('% Achievement Against Plan'))
              
              @if($kval1=='Achieved FTM')
                    @php $aftdmid = str_replace(',','',$dataValue) @endphp
                @endif
                @if($kval1=='Plan FTM')
                    @php $planftmmid =str_replace(',','',$dataValue) @endphp
                @endif
                

              @if($kval1=="% Complete")
              <td class="desc">{{ $dataValue }} {{ $perComplete }} </td>
               @elseif($kval1=="% Achievement Against Plan")
               @php
                    $acheivedaginstplanmid = ($aftdmid>0 && $planftmmid>0) ? round((($aftdmid/($planftmmid*($dayValue/$totalDaysInMonth)))*100),0) :0;
                    $color_plan_code = (  $acheivedaginstplanmid =='0') ? '' : getColorCode($acheivedaginstplanmid);
                    $acheived_aginst_plan = (  $acheivedaginstplanmid =='0' ) ?'-' : $acheivedaginstplanmid ;

                @endphp
              <td class="desc" style="background:{{ $color_plan_code }}  !important">{{ ($acheived_aginst_plan=='-') ? '-' : $acheived_aginst_plan.' %' }}</td>
              @elseif($kval1=="Achieved FTD")
                <td class="desc" style="background:{{ $is_color_org }} !important" ><strong>{{ $dataValue }}  </strong></td>
              @else
               <td class="desc">{{ $dataValue }} {{ $perComplete }}  </td>
              @endif
            @else
             <td class="desc">{{ $dataValue }} {{ $perComplete }}  </td>
            @endif
             @endforeach
            
            </tr>
             @endforeach 
             @endforeach 
               <tr>
          <td class="desc"><strong>Total</strong></td>
           @foreach($array_keys as $nkey1 => $keyvalue) 
           
            <?php
               $sresult = [];
              foreach (@$dpr['item_data'] as $arraydata) {
                foreach (@$arraydata['data'] as $arrays) {
                    foreach ($arrays as $keys => $values) {
                        
                        if (!isset($sresult[$keys])) {
                            if( strtolower($keys)==strtolower('Achieved FTD')){
                                 $evalues = (@$arrays['is_dpr_submit'] == false ) ? 0 : $values;
                                 
                            }
                          
                            elseif(strtolower($keys)==strtolower('Achieved FTD') || strtolower($keys)==strtolower('Achieved FTM') ||  strtolower($keys)==strtolower('Plan FTM') ||  strtolower($keys)==strtolower('% Achievement Against Plan')){
                                 $evalues = (@$arrays['is_this_month_submit'] == false  ) ? 0 : $values;
                                 
                            } else{
                                $evalues = $values;
                            }
                           
                            $sresult[$keys] = $evalues;
                            
                        } else {
                          if(is_numeric($sresult[$keys])){
                          
                            if( strtolower($keys)==strtolower('Achieved FTD')){
                                 $evalues = (@$arrays['is_dpr_submit'] == false ) ? 0 : $values;
                                 
                            }
                          
                            elseif(strtolower($keys)==strtolower('Achieved FTD') || strtolower($keys)==strtolower('Achieved FTM') ||  strtolower($keys)==strtolower('Plan FTM') ||  strtolower($keys)==strtolower('% Achievement Against Plan')){
                                 $evalues = (@$arrays['is_this_month_submit'] == false   ) ? 0 : $values;
                                 
                            } else{
                                $evalues = $values;
                            }
                           
                            $sresult[$keys] += $evalues;
                          }
                        }
                    }
                  }
              }

              $totalValue =  0;
                if(is_numeric(@$sresult[$keyvalue])){
                  $totalValue = number_format(@$sresult[$keyvalue]);
                  
                
                }


               if($keyvalue=='Plan FTM' || $keyvalue=='Achieved FTM' || $keyvalue=='Achieved FTD' || $keyvalue=='% Achievement Against Plan'  ){
                  $totalValue = ($totalValue == 0) ? '-' : $totalValue;
                 
                }
                
                 /*$color_org = (@$item['is_dpr_submit'] == false) ? $color : $color;
                $perSign = (@$item['is_dpr_submit'] == false) ? '%' :'%';*/
                
                
                $perComplete = ($keyvalue =="% Complete" || $keyvalue=='% Achievement Against Plan') ? '%' :'';
              

               ?>
          
          @if(strtolower($keyvalue)==strtolower('Plan FTM')  || strtolower($keyvalue)==strtolower('Achieved FTM') || strtolower($keyvalue)==strtolower('Achieved FTD') || strtolower($keyvalue)==strtolower('% Achievement Against Plan'))
                @if($keyvalue=='Achieved FTM')
                    @php $aftd = str_replace(',','',$totalValue) @endphp
                @endif
                @if($keyvalue=='Plan FTM')
                    @php $planftm =str_replace(',','',$totalValue) @endphp
                @endif
                @if($aftd>0 && $planftm>0)
                    @php 
                
                  $acheivedaginstplan = round((($aftd/($planftm*($dayValue/$totalDaysInMonth)))*100),0);
                 
              
              @endphp
              @endif
              
             
              @if($keyvalue=="% Achievement Against Plan")
               @php
                    $color_plan_code =   ( $totalValue =='-' || $totalValue == '0') ? '' : getColorCode($acheivedaginstplan);
                    $acheived_aginst_plan = ( $totalValue =='-' || $totalValue == '0' ) ?'-' : $acheivedaginstplan ;

                @endphp

               <td class="desc" ><strong>{{ ($acheivedaginstplan==0) ? '-' : $acheivedaginstplan.' %' }}  </strong></td>
               @else
                 <td class="desc"><strong>{{ $totalValue }} {{ $perComplete }} </strong></td>
               @endif
          @else
          @php @endphp
          @if($keyvalue=='Work Done Till Date')
          @php $wdtd = str_replace(',','',$totalValue) @endphp
          @endif
          @if($keyvalue=='Scope')
          @php $scopetotal =str_replace(',','',$totalValue) @endphp
          @endif
          @if($wdtd>0 && $scopetotal>0)
          @php 
          
          $complete_per = round((($wdtd / $scopetotal)*100),0); 
          
          @endphp
          @endif
           @if(strtolower($keyvalue)==strtolower("% Complete"))
              <td class="desc"><strong>{{ $complete_per }} {{ $perComplete }}</strong></td>
             @else
          <td class="desc"><strong>{{ $totalValue }} {{ $perComplete }} </strong></td>
          @endif
          @endif
           @endforeach 
          <tr>
             @endif
          </tbody>
        </table>
      </div>
    </div>
</div>
@endforeach