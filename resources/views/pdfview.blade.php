@php error_reporting(0);  @endphp
@php $heightWidth = "10"; @endphp
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
        color: #000;
        background: #FFFFFF;
        font-family: opensanscondensed;
        font-size: 12px;
      }
      .app_name {
          font-size: 32px;
          text-transform: uppercase;
      }
    
    
        thead, tfoot {
            display: table-header-group; /* Repeat the header/footer on each page */
        }



      .data table {
        page-break-inside: avoid;
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        overflow: hidden;
        border: #00338d 3px solid;
        page-break-inside: auto; /* Ensure table is not split across pages */
      }

      .data table td,
      .data table th {
        border-top: 1px solid #ecf0f1;
        padding: 10px;
        border-bottom: 1px solid #ecf0f1;
      }
      
      .border table td,
      .border table th {
        border: 1px solid #ecf0f1;
      }

      .data table thead tr.orange {
        color: #FFFFFF !important;
        font-size: 13px !important;
        background: #00338d !important;
      }

      .orange {
        color: #FFFFFF !important;
        font-weight: bold;
      }

      .gray {
        color: #000 !important;
        font-size: 13px !important;
        background: #e7e9ee !important;
      }
      .blue {
        color: #FFFFFF !important;
        font-size: 10px !important;
        background: #00338d !important;
        border-left: 1px solid #ecf0f1;
        border-right: 1px solid #ecf0f1;
      }
     

      .data table td {
        border-left: 1px solid #ecf0f1;
        border-right: 1px solid #ecf0f1;
      }

      .data table tr:nth-of-type(even) td,
      .data table tr:nth-of-type(odd) td {
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
      table.headTable {
        width: 100%;
        overflow: hidden;
        border: 0px transparent solid !important; 
      }
       .desc {
        font-size:10px !important;
        border-left: 1px solid #ecf0f1;
        border-right: 1px solid #ecf0f1;
        text-align:center !important;
        
      }
     @page {  
        header: html_otherpageheader;
    }
    
    @page :first {    
        header: html_firstpageheader;
    }
    
    
   .digit-box {
            display: inline-block;
            border: 1px solid #fff;
            text-align: left;
            margin: 0px;
            padding: 5px 10px;
        }
    
    table.day_counter tr:nth-of-type(even) td {
        background-color: transparent;
    }
     
    </style>
  </head>
  <body>
    @if($type=='pdfmail')
    <htmlpageheader name="firstpageheader" style="display:none">
        <div style="text-align:center"></div>
    </htmlpageheader>

    <htmlpageheader name="otherpageheader" style="display:none">
        <div style="width: 100%; position:absolute; display: inline-block; margin-top:-10px;">
            <div style="float: left; text-align:left; width:40%">
                <img src="{{ env('KPMG_LOGO_PATH_FOR_MAIL','C:/inetpub/wwwroot/jswdpr/dprapi/public/kpmg-logo.jpeg') }}" alt="Left Logo" style="height: 25px;">
            </div>
            <div style="float: right; text-align:right; padding-right:110px;">
                <img src="{{ env('CLIENT_LOGO_PATH_FOR_MAIL','C:/inetpub/wwwroot/jswdpr/dprapi/public/client-logo.jpg') }}" alt="Right Logo" style="height: 30px;">
            </div>
            
            <div style="clear: both;"></div>
        </div>
    </htmlpageheader>
    
    <sethtmlpageheader name="firstpage" value="on" show-this-page="1" />
    <sethtmlpageheader name="otherpages" value="on" />


    @else
    <htmlpageheader name="firstpageheader" style="display:none">
        <div style="text-align:center"></div>
    </htmlpageheader>

    <htmlpageheader name="otherpageheader" style="display:none">
        <div style="width: 100%; position:absolute; display: inline-block; margin-top:-10px;">
            <div style="float: left; text-align:left; width:40%">
                <img src="./kpmg-logo.jpeg" alt="Left Logo" style="height: 25px;">
            </div>
            <div style="float: right; text-align:right; padding-right:110px;">
                <img src="./client-logo.jpg" alt="Right Logo" style="height: 40px; margin-top:-5px;">
            </div>
            
            <div style="clear: both;"></div>
        </div>
    </htmlpageheader>
    
    <sethtmlpageheader name="firstpage" value="on" show-this-page="1" />
    <sethtmlpageheader name="otherpages" value="on" />
    @endif
    <header class="clearfix">
        
      <div id="company">
            
         @php
            $appSetting = App\Models\AppSetting::first();

            $startDate = new DateTime($appSetting->project_start_date);
            $endDate = new DateTime($appSetting->project_end_date);

            // Get the current date
            $currentDate = new DateTime();

            // Calculate the interval between current date and end date
            $intervalToEnd = $currentDate->diff($endDate);
            
            
            $totalDaysToEnd = $intervalToEnd->days;
         @endphp
           

         @if($type=='pdf')
        <table class="headTable" width="100%" border="0px" style="border:0px solid black;">
            <tr>
                <td style="text-align:left" width="65%">
                    <h2 class="name" style="text-align: left;">
                        <img src="./kpmg-logo.jpeg" width="90">
                    </h2>
                </td>
                <td style="text-align:right; margin-right: 0px;">
                    <img src="./client-logo.jpg" width="120">
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                    <h1 class="app_name" style="text-align: center; color: #00338d">
                        {{ $appSetting->app_name }}
                    </h1>
                </td>
            </tr>
        </table>
        @elseif($type=='pdfmail')
        <table class="headTable" width="100%" border="0px" style="border:0px solid black;">
            <tr>
                <td style="text-align:left" width="65%">
                    <h2 class="name" style="text-align: left;">
                        <img src="{{ env('KPMG_LOGO_PATH_FOR_MAIL','C:/inetpub/wwwroot/jswdpr/dprapi/public/kpmg-logo.jpeg') }}" width="60">
                    </h2>
                </td>
                <td style="text-align:right; margin-right: 0px;">
                    <img src="{{ env('CLIENT_LOGO_PATH_FOR_MAIL','C:/inetpub/wwwroot/jswdpr/dprapi/public/client-logo.jpg') }}" width="150">
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                    <h1 class="app_name" style="text-align: center; color: #00338d">
                        {{ $appSetting->app_name }}
                    </h1>
                </td>
            </tr>
        </table>

        @else
       <style>
           .desc h4 {
               display: inline-flex;
           }
       </style>
       @php $heightWidth = "20"; @endphp
        <table class="headTable" width="100%" border="0px" style="border:0px solid black;">
            <tr>
                <td style="text-align:left" width="65%">
                    <h2 class="name" style="text-align: left;">
                        <img src="{{ $appSetting->app_logo }}" width="150">
                    </h2>
                </td>
                <td style="text-align:right; margin-right: 0px;">
                    <img src="{{ asset('client-logo.jpg') }}" width="150">
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                    <h1 class="app_name" style="text-align: center; color: #00338d">
                        {{ $appSetting->app_name }}
                    </h1>
                </td>
            </tr>
        </table>
        
       <!--  <h2 class="name" style="text-align: center; margin-right: 25px; color: #00338d">
            <img src="{{ $appSetting->app_logo }}" width="150">
            <div style="position: absolute; top: 40px; right: 5px;">
                <img src="{{ asset('client-logo.jpg') }}" width="150">
            </div>
            <br>
             {{ $appSetting->app_name }}
        </h2> -->
        @endif
        <br>
        <br>
        <div class="data border">
        <table width="100%" style="padding: 10px 20px;">
            <thead>
            <tr class="orange">
                <th class="orange text-left" width="34%">
                    <h4 >
                    <span class="iconclass">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="{{$heightWidth}}" height="{{$heightWidth}}" viewBox="0 0 256 256" xml:space="preserve">

                    <defs>
                    </defs>
                    <g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: none; fill-rule: nonzero; opacity: 1;" transform="translate(1.4065934065934016 1.4065934065934016) scale(2.81 2.81)" >
                        <path d="M 86.351 80.944 H 3.649 C 1.634 80.944 0 79.31 0 77.295 V 29.61 c 0 -2.015 1.634 -3.649 3.649 -3.649 h 82.703 c 2.015 0 3.649 1.634 3.649 3.649 v 47.685 C 90 79.31 88.366 80.944 86.351 80.944 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(255,255,255); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                        <path d="M 3.648 21.961 h 82.703 c 1.347 0 2.6 0.405 3.648 1.097 v -2.883 c 0 -2.015 -1.634 -3.649 -3.649 -3.649 H 35.525 c -1.909 0 -3.706 -0.903 -4.846 -2.435 l -2.457 -3.302 c -0.812 -1.092 -2.093 -1.735 -3.454 -1.735 H 3.649 C 1.634 9.056 0 10.69 0 12.705 v 10.354 C 1.048 22.367 2.301 21.961 3.648 21.961 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(255,255,255); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                    </g>
                    </svg>
                     PROJECT   - JSW DOLVI PHASE-3  <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;EXPANSION (10-15 MTPA)                                                                                              

                    </h4>
                </th>
                <th class="orange text-left" width="33%">
                    <h4  >
                        <span class="iconclass">
                      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="{{$heightWidth}}" height="{{$heightWidth}}" viewBox="0 0 256 256" xml:space="preserve">

                        <defs>
                        </defs>
                        <g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: none; fill-rule: nonzero; opacity: 1;" transform="translate(1.4065934065934016 1.4065934065934016) scale(2.81 2.81)" >
                            <path d="M 86.536 29.739 H 3.464 c -1.657 0 -3 -1.343 -3 -3 V 12.744 c 0 -3.984 3.241 -7.225 7.225 -7.225 h 74.623 c 3.983 0 7.225 3.241 7.225 7.225 v 13.996 C 89.536 28.396 88.193 29.739 86.536 29.739 z M 6.464 23.739 h 77.072 V 12.744 c 0 -0.675 -0.55 -1.225 -1.225 -1.225 H 7.689 c -0.675 0 -1.225 0.549 -1.225 1.225 V 23.739 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(255,255,255); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                            <path d="M 81.716 90 H 8.284 c -4.312 0 -7.819 -3.508 -7.819 -7.819 V 26.739 c 0 -1.657 1.343 -3 3 -3 h 83.072 c 1.657 0 3 1.343 3 3 v 55.441 C 89.536 86.492 86.028 90 81.716 90 z M 6.464 29.739 v 52.441 C 6.464 83.184 7.28 84 8.284 84 h 73.432 c 1.004 0 1.82 -0.816 1.82 -1.819 V 29.739 H 6.464 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(255,255,255); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                            <path d="M 69.955 17.04 c -1.657 0 -3 -1.343 -3 -3 V 3 c 0 -1.657 1.343 -3 3 -3 s 3 1.343 3 3 v 11.04 C 72.955 15.696 71.612 17.04 69.955 17.04 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(255,255,255); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                            <path d="M 20.045 17.04 c -1.657 0 -3 -1.343 -3 -3 V 3 c 0 -1.657 1.343 -3 3 -3 s 3 1.343 3 3 v 11.04 C 23.045 15.696 21.702 17.04 20.045 17.04 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(255,255,255); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                        </g>
                        </svg>
                        </span>
                        PROJECT START DATE  -  {{ date('d M Y', strtotime($appSetting->project_start_date)) }}   <br>                                  
                        &nbsp;&nbsp;&nbsp;&nbsp;PROJECT END DATE  - {{ date('d M Y', strtotime($appSetting->project_end_date)) }}

                    </h4>
                </th>
                <th class="orange text-left">
                    <h4  >
                    <span class="iconclass">
                  <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="{{$heightWidth}}" height="{{$heightWidth}}" viewBox="0 0 256 256" xml:space="preserve">

                            <defs>
                            </defs>
                            <g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: none; fill-rule: nonzero; opacity: 1;" transform="translate(1.4065934065934016 1.4065934065934016) scale(2.81 2.81)" >
                                <rect x="20.16" y="0" rx="0" ry="0" width="32.27" height="17.65" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(255,255,255); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) "/>
                                <path d="M 89.414 20.792 L 69.209 0.586 C 68.834 0.211 68.325 0 67.795 0 H 56.427 v 19.649 c 0 1.104 -0.896 2 -2 2 H 18.159 c -1.104 0 -2 -0.896 -2 -2 V 0 H 2 C 0.896 0 0 0.896 0 2 v 86 c 0 1.104 0.896 2 2 2 h 86 c 1.104 0 2 -0.896 2 -2 V 22.206 C 90 21.675 89.789 21.167 89.414 20.792 z M 73.841 78 c 0 1.104 -0.896 2 -2 2 H 18.159 c -1.104 0 -2 -0.896 -2 -2 V 38.152 c 0 -1.104 0.896 -2 2 -2 h 53.682 c 1.104 0 2 0.896 2 2 V 78 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(255,255,255); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                                <path d="M 65 49.037 H 25 c -1.104 0 -2 -0.896 -2 -2 s 0.896 -2 2 -2 h 40 c 1.104 0 2 0.896 2 2 S 66.104 49.037 65 49.037 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(255,255,255); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                                <path d="M 65 60.076 H 25 c -1.104 0 -2 -0.896 -2 -2 s 0.896 -2 2 -2 h 40 c 1.104 0 2 0.896 2 2 S 66.104 60.076 65 60.076 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(255,255,255); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                                <path d="M 65 71.114 H 25 c -1.104 0 -2 -0.896 -2 -2 s 0.896 -2 2 -2 h 40 c 1.104 0 2 0.896 2 2 S 66.104 71.114 65 71.114 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(255,255,255); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                            </g>
                            </svg>
                                                </span>

                        REPORT DATE :- {{ date('d M Y', strtotime($date)) }}

                    </h4>
                </th>
                
            </tr>
            
            <tr class="orange">
                <th class="orange text-left" >
                    <h4  >
                    <span class="iconclass">
                   <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="{{$heightWidth}}" height="{{$heightWidth}}" viewBox="0 0 256 256" xml:space="preserve">

                    <defs>
                    </defs>
                    <g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: none; fill-rule: nonzero; opacity: 1;" transform="translate(1.4065934065934016 1.4065934065934016) scale(2.81 2.81)" >
                        <path d="M 45 0 C 27.677 0 13.584 14.093 13.584 31.416 c 0 4.818 1.063 9.442 3.175 13.773 c 2.905 5.831 11.409 20.208 20.412 35.428 l 4.385 7.417 C 42.275 89.252 43.585 90 45 90 s 2.725 -0.748 3.444 -1.966 l 4.382 -7.413 c 8.942 -15.116 17.392 -29.4 20.353 -35.309 c 0.027 -0.051 0.055 -0.103 0.08 -0.155 c 2.095 -4.303 3.157 -8.926 3.157 -13.741 C 76.416 14.093 62.323 0 45 0 z M 45 42.81 c -6.892 0 -12.5 -5.607 -12.5 -12.5 c 0 -6.893 5.608 -12.5 12.5 -12.5 c 6.892 0 12.5 5.608 12.5 12.5 C 57.5 37.202 51.892 42.81 45 42.81 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(255,255,255); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                    </g>
                    </svg>
                    </span>

                        LOCATION -  DOLVI

                    </h4>
                </th>
                 <th  class="orange text-left" >
                    <h4 class="">
                
                  
                        @php
                        //$remainingString = $remainingMonths . $remainingDays;
                        $remainingString = $totalDaysToEnd + 1;
                        @endphp
                    
                      <!-- <table class="day_counter" style="padding:0px; text-align:center; width: 150px; border:1px solid #fff; ">
                          
                           <tr style="padding:0px;">
                               <th style="padding:0px;" class="orange">
                                <span class="iconclass">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="13" height="13" viewBox="0 0 256 256" xml:space="preserve">
            
                                    <defs>
                                    </defs>
                                    <g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: none; fill-rule: nonzero; opacity: 1;" transform="translate(1.4065934065934016 1.4065934065934016) scale(2.81 2.81)" >
                                        <path d="M 52.976 38.792 c 7.213 -6.25 15.987 -12.648 15.987 -32.09 h 3.974 V 0.086 H 17.063 v 6.616 h 3.974 c 0 19.441 8.774 25.839 15.987 32.09 c 3.986 3.454 3.986 9.133 0 12.588 c -7.213 6.25 -15.987 12.648 -15.987 32.09 h -3.974 v 6.616 h 55.875 V 83.47 h -3.974 c 0 -19.441 -8.774 -25.839 -15.987 -32.09 C 48.99 47.926 48.99 42.247 52.976 38.792 z M 49.676 30.926 c -1.559 1.46 -4.033 4.679 -4.676 8.19 c -0.529 -3.51 -3.117 -6.73 -4.676 -8.19 c -4.228 -3.664 -9.371 -7.414 -9.371 -18.81 h 28.094 C 59.047 23.511 53.904 27.262 49.676 30.926 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(255,255,255); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                                    </g>
                                    </svg>
                                </span>
                               </th>
                                @foreach(str_split($remainingString) as $key => $digit)
                                    <td style="padding:0px;" class="orange">{{ $digit }}</td>
                                @endforeach  
                             <td style="padding:0px;border:0px" class="orange">DAYS</td>
                           </tr>
                       </table>-->
                       <span class="iconclass">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="13" height="13" viewBox="0 0 256 256" xml:space="preserve">
    
                            <defs>
                            </defs>
                            <g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: none; fill-rule: nonzero; opacity: 1;" transform="translate(1.4065934065934016 1.4065934065934016) scale(2.81 2.81)" >
                                <path d="M 52.976 38.792 c 7.213 -6.25 15.987 -12.648 15.987 -32.09 h 3.974 V 0.086 H 17.063 v 6.616 h 3.974 c 0 19.441 8.774 25.839 15.987 32.09 c 3.986 3.454 3.986 9.133 0 12.588 c -7.213 6.25 -15.987 12.648 -15.987 32.09 h -3.974 v 6.616 h 55.875 V 83.47 h -3.974 c 0 -19.441 -8.774 -25.839 -15.987 -32.09 C 48.99 47.926 48.99 42.247 52.976 38.792 z M 49.676 30.926 c -1.559 1.46 -4.033 4.679 -4.676 8.19 c -0.529 -3.51 -3.117 -6.73 -4.676 -8.19 c -4.228 -3.664 -9.371 -7.414 -9.371 -18.81 h 28.094 C 59.047 23.511 53.904 27.262 49.676 30.926 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(255,255,255); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                            </g>
                            </svg>
                        </span>
                        @foreach(str_split($remainingString) as $key => $digit)
                            <span style="padding:5px 10px;" class="digit-box" >&nbsp;&nbsp;{{ $digit }}&nbsp;&nbsp;</span>
                        @endforeach  
                        &nbsp;<strong>Days</strong>
                    
                    </h4>
                </th>
                 
                <th class="orange text-left" >
                    <h4>
                    <span class="iconclass">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="{{$heightWidth}}" height="{{$heightWidth}}" viewBox="0 0 256 256" xml:space="preserve">

                    <defs>
                        </defs>
                        <g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: none; fill-rule: nonzero; opacity: 1;" transform="translate(1.4065934065934016 1.4065934065934016) scale(2.81 2.81)" >
                            <path d="M 85.342 21.098 L 47.429 36.415 l -0.203 0.082 V 90 l 38.116 -15.398 V 21.098 z M 53.482 61.674 l 10.505 -4.244 c 1.141 -0.459 2.437 0.09 2.897 1.23 c 0.461 1.14 -0.09 2.436 -1.23 2.897 l -10.505 4.244 c -0.273 0.11 -0.555 0.163 -0.832 0.163 c -0.881 0 -1.715 -0.526 -2.065 -1.393 C 51.791 63.431 52.342 62.135 53.482 61.674 z M 65.654 77.338 l -10.505 4.244 c -0.273 0.11 -0.555 0.163 -0.832 0.163 c -0.881 0 -1.715 -0.526 -2.065 -1.393 c -0.461 -1.14 0.09 -2.436 1.23 -2.897 l 10.505 -4.244 c 1.141 -0.458 2.437 0.09 2.897 1.23 C 67.345 75.58 66.794 76.877 65.654 77.338 z M 65.654 69.447 l -10.505 4.245 c -0.273 0.11 -0.555 0.163 -0.832 0.163 c -0.881 0 -1.715 -0.526 -2.065 -1.393 c -0.461 -1.139 0.09 -2.436 1.23 -2.897 l 10.505 -4.245 c 1.141 -0.458 2.437 0.09 2.897 1.23 C 67.345 67.689 66.794 68.986 65.654 69.447 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(255,255,255); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                            <polygon points="27.61,30.37 27.61,46.96 27.61,50.36 19.82,46.96 19.82,27.22 14.38,25.03 4.66,21.1 4.66,74.6 42.77,90 42.77,36.5 29.34,31.07 " style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(255,255,255); fill-rule: nonzero; opacity: 1;" transform="  matrix(1 0 0 1 0 0) "/>
                            <polygon points="55.86,4.39 45,0 4.66,16.3 16.61,21.12 19.82,22.42 19.82,22.35 36.93,15.4 47.23,19.62 30.1,26.58 45,32.59 85.34,16.3 " style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: rgb(255,255,255); fill-rule: nonzero; opacity: 1;" transform="  matrix(1 0 0 1 0 0) "/>
                        </g>
                        </svg>

                    </span>
                     PACKAGE - {{ @$packageName }}


                    </h4>
                </th>
            </tr>
            </thead>
        </table>
        </div>
        
         <!-- <h4 class="name" style="text-align: center; color: #000000">
            <table class="headTable" width="100%" border="0px">
                <tr>
                    <td >
                        <h4 class="name" style="text-align: left; color: #000000">
                           PROJECT NAME  -JVML 5-MTPA INTEGRATED STEEL PLANT AT VIJAYANAGAR 
                        </h4>
                    </td>
                    <td style="text-align:right">
                         <h4 class="name" style="text-align: right; margin-right:0px; color: #000000">
                            REPORT DATE - {{ date("jS F Y", strtotime($date)) }}
                        </h4>
                    </td>
                </tr>
            </table>
            
        </h4> -->
      </div>
    </header>
    <main> 
      <!--Please find the Daily Progress Report for {{ date('d M Y', strtotime($date)) }} below:-->
      <br>
      <div class="data">
      <table>
        <tr>
           <th>
           </th>
        <th>
           <span style="display: inline-block; width: 25px; height: 25px;">
              <svg width="25" height="25" xmlns="http://www.w3.org/2000/svg">
                  <circle cx="12.5" cy="12.5" r="12.5" fill="#6de26d"/>
              </svg>
              
          </span> % Achievement: 90% & Above

       
      </th>
      <th>
        <span style="display: inline-block; width: 25px; height: 25px;">
              <svg width="25" height="25" xmlns="http://www.w3.org/2000/svg">
                  <circle cx="12.5" cy="12.5" r="12.5" fill="#ffbf00"/>
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
                  <circle cx="12.5" cy="12.5" r="12.5" fill="#808080"/>
              </svg>
          </span>
         Non Submission
      </th>
       <th></th>
    </tr>
    </table>
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
 

        $fixkey =  ['Scope','Drawing Release','Front Available','Work Done Till Date','% Complete','Balance','Plan FTM','Achieved FTM','Achieved FTD','% Achievement Against Plan'];

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
      <table width="100%" style="padding: 10px 20px;">
        <thead>
          <tr class="orange">
            <th class="orange text-left" colspan="{{ $colValue }}">
             <strong>Work Item: {{ !empty(@$dpr['work_item']) ? @$dpr['work_item']: "-" }} {{ $unit_of_measure }}<strong>
            </th>
            <th class="orange text-left" colspan="">
            
            </th>
            
            <th class="orange text-right" colspan="2">
            <strong>Date: {{ date('d M Y', strtotime($date)) }}</strong>
            </th>
          </tr>
           @if(count(@$dpr['item_data']) >0)
          <tr>
            <th class="blue">
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
            <th class="desc">D</th>
            <th class="desc">E=(D/A)%</th>
            <th class="desc">F=A-D</th>
            <th class="desc">G</th>
            <th class="desc">H</th>
            <th class="desc">I</th>
            <th class="desc">J=(H/G*{{ $dayValue }} /{{ $totalDaysInMonth }})%</th>
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
                /*
                $color_org = ($item['is_dpr_submit'] == false) ? $color : $color;
                
                $perSign = ($item['is_dpr_submit'] == false) ? '%' :'%';
                */
                if(strtolower($kval)==strtolower('Plan FTM') || strtolower($kval)==strtolower('Achieved FTM')||  strtolower($kval)==strtolower('Achieved FTD') || strtolower($kval)==strtolower('% Achievement Against Plan')){
            
                  $dval = ($dval == '0') ? '-' : $dval;
                 
                }
               /* if(strtolower($kval)==strtolower('Achieved FTD')){
                      $dval = ($item['is_dpr_submit'] == false) ? '-' : $dval;
                }*/
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
          </tr>
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

               <td class="desc" style="background:{{ ($acheivedaginstplan==0) ? '' : $color_plan_code }} !important"><strong>{{ ($acheivedaginstplan==0) ? '-' : $acheivedaginstplan.' %' }}  </strong></td>
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
      
      <br />
      @endforeach 
      </div>
      <!-- <div id="thanks">Thank you!</div> -->
      <div style="text-align:justify">
          
          <small>Note: This is a system generated mail. Please do not reply on this,</small><br><br>
Regards,<br>
KPMG PIVOT Team<br>
<a href="mailto:in-fmpivotsupport@kpmg.com">in-fmpivotsupport@kpmg.com</a><br>
<hr>
{{ date('Y') }} KPMG International Cooperative<br>
<hr>
KPMG (in India) allows reasonable personal use of the e-mail system. Views and opinions expressed in these communications do not necessarily represent those of KPMG (in India).<br>

***********************************************************************************************************************************************************<br>
DISCLAIMER<br>
The information in this e-mail is confidential and may be legally privileged. It is intended solely for the addressee. Access to this e-mail by anyone else is unauthorized. If you have received this communication in error, please address with the subject heading "Received in error," send to postmaster1@kpmg.com, then delete the e-mail and destroy any copies of it. If you are not the intended recipient, any disclosure, copying, distribution or any action taken or omitted to be taken in reliance on it, is prohibited and may be unlawful. Any opinions or advice contained in this e-mail are subject to the terms and conditions expressed in the governing KPMG client engagement letter. Opinions, conclusions and other information in this e-mail and any attachments that do not relate to the official business of the firm are neither given nor endorsed by it.
<br><br>
KPMG cannot guarantee that e-mail communications are secure or error-free, as information could be intercepted, corrupted, amended, lost, destroyed, arrive late or incomplete, or contain viruses.
<br><br>
KPMG, an Indian partnership and a member firm of KPMG International Cooperative ("KPMG International"), an English entity that serves as a coordinating entity for a network of independent firms operating under the KPMG name. KPMG International Cooperative (“KPMG International”) provides no services to clients. Each member firm of KPMG International Cooperative (“KPMG International”) is a legally distinct and separate entity and each describes itself as such.
<br><br>
"Notwithstanding anything inconsistent contained in the meeting invite to which this acceptance pertains, this acceptance is restricted solely to confirming my availability for the proposed call and should not be construed in any manner as acceptance of any other terms or conditions. Specifically, nothing contained herein may be construed as an acceptance (or deemed acceptance) of any request or notification for recording of the call, which can be done only if it is based on my explicit and written consent and subject to the terms and conditions on which such consent has been granted"<br>
***********************************************************************************************************************************************************

      </div>
    </main>
  </body>
</html>