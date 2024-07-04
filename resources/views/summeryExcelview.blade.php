<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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

      
      .orange {
        color: #FFFFFF !important;
        font-weight: bold;
      }

      .gray {
        color: #000 !important;
        font-size: 14px !important;
        background: #e7e9ee !important;
      }

      
     

      #thanks {
        font-size: 20px !important;
        text-align: center;
      }

      .boxhead a {
          color: #0087C3;
          text-decoration: none;
      }
      th, td {
            border: 1px solid #dddddd;
            text-align: center; /* Align text to the left */
            padding: 8px;
        }
      
    </style>
  </head>
  <body>
    

    <table width="100%" style="padding: 10px 20px;">
       <thead>
          <tr class="" height="30">
            <th class="" style="background:#00338d; vertical-align:top" >
                <img src="uploads/Picture1.png" width="150px" height="30px">
            </th>
           
            <th class="" colspan="9" style="background:#00338d; text-align:center">
              <strong>Summary Report</strong>
            </th>
          </tr>
         </thead>

      
       <thead>
          <tr>
            <th class="blue" width="15%">
              SNO
           </th>
            <th class="blue" width="30%">
              WORK PACK NAME
           </th>
           <th class="blue" width="30%">
              DATE
           </th>
           <th class="blue" width="30%">
              FILE PATH
           </th>
         
          </tr>

        </thead>
       <tbody> 
  
       @foreach($summeryReporr as $key => $report)
        
          <tr>
            <td class="desc">{{ $key+1 }}</td>
            <td class="desc">
                <strong>
                  {{ $report['name'] }}
                 
                </strong>
            </td>
             <td class="desc">  {{ $report['date'] }}</td>
             <td class="desc">  {!!$report['link'] !!}</td>
             
          </tr> 
          @endforeach
        </tbody>
        
      <br />
     
       </table> 
    
      <div>
      </div>
  </body>
</html>