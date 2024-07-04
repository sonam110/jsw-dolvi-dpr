<!DOCTYPE html>
<html
  lang="en"
  class="light-style layout-navbar-fixed layout-menu-fixed layout-compact"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ url('/')}}/assets/"
  data-template="vertical-menu-template">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>KPMG</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ url('logo.svg') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
      rel="stylesheet" />
    
    <!-- Icons -->
    <link rel="stylesheet" href="{{ url('assets/vendor/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ url('assets/vendor/fonts/tabler-icons.css') }}">
    <link rel="stylesheet" href="{{ url('assets/vendor/fonts/flag-icons.css') }}">


    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ url('assets/vendor/css/rtl/core.css') }}" class="template-customizer-core-css">
    <link rel="stylesheet" href="{{ url('assets/vendor/css/rtl/theme-default.css') }}" class="template-customizer-theme-css" >
    <link rel="stylesheet" href="{{ url('assets/css/demo.css') }}">
   
     <script type="text/javascript" src="{{ url('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script type="text/javascript" src="{{ url('assets/vendor/js/helpers.js') }}"></script>
    <script type="text/javascript" src="{{ url('assets/vendor/js/template-customizer.js') }}"></script>
    <script type="text/javascript"  src="{{ url('assets/js/config.js') }}"></script>
 


    <style>
    .canvasjs-chart-credit {
    display: none;
    
}
.text-primary{
   color: #00338d !important;
}
.bg-menu-theme.menu-vertical .menu-item.active > .menu-link:not(.menu-toggle) {
    background: #00338d !important;
    box-shadow: 0px 2px 6px 0px rgba(115, 103, 240, 0.48);
    color: #fff !important;
}
</style>
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
       <!-- Menu -->

        @include('sidebar')
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->

          <nav
            class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
            id="layout-navbar">
            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
              <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                <i class="ti ti-menu-2 ti-sm"></i>
              </a>
            </div>
            <h4 class="text-primary"><strong>Digital Progress Report</strong></h4>

          </nav>

          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">
              <div class="row">
  
                <div class="col-lg-4 col-sm-6 mb-4">
                  <div class="card">
                    <div class="card-header">
                      <div class="d-flex justify-content-between">
                       <h4 class="card-title mb-1">{{ $data['totalProject'] }}</h4>
                        <div class="card-icon">
                        <span class="badge bg-label-success rounded-pill p-2">
                          <i class="ti ti-credit-card ti-sm"></i>
                        </span>
                      </div>
                      </div>
                      <small class="d-block mb-1 text-muted">Sales Projects</small>
                     
                    </div>
                    
                  </div>
                </div>
                <div class="col-lg-4 col-sm-6 mb-4">
                  <div class="card">
                    <div class="card-header">
                      <div class="d-flex justify-content-between">
                       <h4 class="card-title mb-1">{{ $data['totalVendor'] }}</h4>
                        <div class="card-icon">
                        <span class="badge bg-label-warning rounded-pill p-2">
                          <i class="ti ti-ticket ti-sm"></i>
                        </span>
                      </div>
                      </div>
                      <small class="d-block mb-1 text-muted">Total Vendor</small>
                     
                    </div>
                    
                  </div>
                </div>
                
               <div class="col-lg-4 col-sm-6 mb-4">
                  <div class="card">
                    <div class="card-header">
                      <div class="d-flex justify-content-between">
                       <h4 class="card-title mb-1">{{ $data['TotalWorkPackage'] }}</h4>
                        <div class="card-icon">
                        <span class="badge bg-label-info rounded-pill p-2">
                          <i class="ti ti-file-description"></i>
                        </span>
                      </div>
                      </div>
                      <small class="d-block mb-1 text-muted">Total Item Description</small>
                     
                    </div>
                    
                  </div>
                </div>
                <div class="col-lg-4 col-sm-6 mb-4">
                  <div class="card">
                    <div class="card-header">
                      <div class="d-flex justify-content-between">
                       <h4 class="card-title mb-1">{{ $data['userCount'] }}</h4>
                        <div class="card-icon">
                        <span class="badge rounded bg-label-primary p-1">
                          <i class="ti ti-users ti-sm"></i>
                        </span>
                      </div>
                      </div>
                      <small class="d-block mb-1 text-muted">User Count</small>
                     
                    </div>
                    
                  </div>
                </div>
                <div class="col-lg-4 col-sm-6 mb-4">
                  <div class="card">
                    <div class="card-header">
                      <div class="d-flex justify-content-between">
                       <h4 class="card-title mb-1">{{ $data['dprUploads'] }}</h4>
                        <div class="card-icon">
                        <span class="badge bg-label-danger rounded-pill p-2">
                          <i class="ti ti-link ti-sm"></i>
                        </span>
                      </div>
                      </div>
                      <small class="d-block mb-1 text-muted">DPR Uploads Today</small>
                     
                    </div>
                    
                  </div>
                </div>
                <div class="col-lg-4 col-sm-6 mb-4"></div>

            
              </div>
              <div class="row">
  
                <div class="col-lg-12 col-sm-6 mb-4">
                  <div class="card">
                    <div class="card-header border-bottom" >
                      <h4 class="text-primary fw-bolder mb-0 card-title text-primary">DPR Uploads Graph</h4>
                     
                    </div>
                    <div class="card-header border-bottom" >
                      <div class="row">
                        <div class="col-lg-2 col-sm-6 mb-4">
                         <select class="form-control" id="month">
                            @for ($month = 1; $month <= 12; $month++)
                                @php
                                    $monthName = date("F", mktime(0, 0, 0, $month, 1)); // Get month name from numeric value
                                   $monthVal = date("M", mktime(0, 0, 0, $month, 1)); // Pad month with leading zero if needed
                                @endphp
                                <option value="{{ $monthVal }}" {{ ($monthVal == date('M')) ? 'selected' :''}}>{{ $monthName }}</option>
                            @endfor
                        </select>


                        </div>
                        <div class="col-lg-2 col-sm-6 mb-4" style=" margin-left: -24px;">

                         <select class="form-control" id="year">
                           @for ($year = 2024; $year >= 1974; $year--)
                         
                          <option value="{{ $year }}" {{ ($year == date('Y')) ? 'selected' :''}}>{{ $year }}</option>
                            @endfor
                        </select>
                      </div>
                      <div class="col-lg-2 col-sm-6 mb-4">

                         <select class="form-control" id="project">
                          <option value="" selected >Project</option>
                           @foreach($projects as $project)
                         
                          <option value="{{ $project->id }}" >{{ $project->name }}</option>
                            @endforeach
                        </select>
                      </div>
                      <div class="col-lg-3 col-sm-6 mb-4">

                         <select class="form-control" id="vendor">
                          <option value="" selected >Vendor</option>
                           @foreach($vendors as $vendor)
                         
                          <option  value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                      </div>
                      <div class="col-lg-3 col-sm-6 mb-4">

                         <select class="form-control" id="item_desc">
                          <option value="" selected >Item Description</option>
                           @foreach($itemDescs as $item)
                         
                          <option value="{{ $item->id }}">{{ $item->title }}</option>
                            @endforeach
                        </select>
                      </div>
                     </div>
                    </div>
                    <div class="card-body">
                    <div class="grid-container">
                        <div class="chart-box">
                          <div id="chartContainer" style="height: 370px; width: 100%;"></div>
                        </div>
                      </div>

                      

                    </div>
                    
                  </div>
                </div>
           

            
              </div>
            
              <div class="row">
  
                <div class="col-lg-12 col-sm-6 mb-4">
                  <div class="card">
                    <div class="card-header border-bottom" >
                      <h4 class="text-primary fw-bolder mb-0 card-title text-primary">Manpower Graph</h4>
                     
                    </div>
                    <div class="card-header border-bottom" >
                      <div class="row">
                        <div class="col-lg-2 col-sm-6 mb-4">
                         <select class="form-control" id="monthg">
                            @for ($month = 1; $month <= 12; $month++)
                                @php
                                    $monthName = date("F", mktime(0, 0, 0, $month, 1)); // Get month name from numeric value
                                   $monthVal = date("M", mktime(0, 0, 0, $month, 1)); // Pad month with leading zero if needed
                                @endphp
                                <option value="{{ $monthVal }}" {{ ($monthVal == date('M')) ? 'selected' :''}}>{{ $monthName }}</option>
                            @endfor
                        </select>


                        </div>
                        <div class="col-lg-2 col-sm-6 mb-4" style=" margin-left: -24px;">

                         <select class="form-control" id="yearg">
                           @for ($year = 2024; $year >= 1974; $year--)
                         
                          <option value="{{ $year }}" {{ ($year == date('Y')) ? 'selected' :''}}>{{ $year }}</option>
                            @endfor
                        </select>
                      </div>
                      <div class="col-lg-2 col-sm-6 mb-4">

                         <select class="form-control" id="projectg">
                          <option value="" selected >Project</option>
                           @foreach($projects as $project)
                         
                          <option value="{{ $project->id }}" >{{ $project->name }}</option>
                            @endforeach
                        </select>
                      </div>
                      <div class="col-lg-3 col-sm-6 mb-4">

                         <select class="form-control" id="vendorg">
                          <option value="" selected >Vendor</option>
                           @foreach($vendors as $vendor)
                         
                          <option  value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                      </div>
                      <div class="col-lg-3 col-sm-6 mb-4">

                         <select class="form-control" id="item_descg">
                          <option value="" selected >Item Description</option>
                           @foreach($itemDescs as $item)
                         
                          <option value="{{ $item->title }}">{{ $item->title }}</option>
                            @endforeach
                        </select>
                      </div>
                     </div>
                    </div>
                    <div class="card-body">
                    <div class="grid-container">
                        <div class="chart-box">
                          <div id="chartContainerManpower" style="height: 370px; width: 100%;"></div>
                        </div>
                      </div>

                      

                    </div>
                    
                  </div>
                </div>
           

            
              </div>
            </div>
            <!-- / Content -->

            <!-- Footer -->
            <footer class="footer footer-light footer-fixed"><p class="clearfix mb-0"><span class="float-md-start d-block d-md-inline-block mt-25" style="font-size: 0.8rem;">© 2024 KPMG Assurance and Consulting Services LLP, an Indian Limited Liability Partnership and a member firm of the KPMG global. This organization of independent member firms affiliated with KPMG International Limited, a private English company limited by guarantee. All rights Reserved</span></p></footer>
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>

      <!-- Drag Target Area To SlideIn Menu On Small Screens -->
      <div class="drag-target"></div>
    </div>

    <script type="text/javascript" src="{{ url('assets/vendor/js/menu.js') }}"></script>
    <script type="text/javascript"  src="{{ url('assets/vendor/js/canvasjs.min.js') }}"></script>
    <script type="text/javascript"  src="{{ url('assets/js/main.js') }}"></script>
    <script type="text/javascript"  src="{{ url('assets/js/dashboards-analytics.js') }}"></script>


<script type="text/javascript">
  var rowsData = [];
$(document).ready(function() {
    getData();
    getManPowerData();
});
$('#month,#month,#project,#vendor, #item_desc').on('change keyup', function(e) {
 getData();
});

$('#monthg,#monthg,#projectg,#vendorg, #item_descg').on('change keyup', function(e) {
 getManPowerData();
});


function getData() {
    var month = $('#month').val();
    var year = $('#year').val();
    var project_id = $('#project').val();
    var vendor_id = $('#vendor').val();
    var item_desc = $('#item_desc').val();
    $.ajax({
        url: "{{ url('get-upload-graph') }}",
        type: "POST",
        data: { month: month,year:year,project_id:project_id,vendor_id:vendor_id,item_desc:item_desc},
        dataType: "json",
        success: function(response) {
          
            updateChart(response);
            
        },
        error: function(xhr, status, error) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                $.each(xhr.responseJSON.errors, function(key, item) {
                    alert(item);
                });
            } else {
                // Handle other types of errors or no errors in the response
                alert("An error occurred. Please try again later.");
            }
        },
    });
}

function updateChart(response) {

  var dates = response.data.date;
    var data = response.data.data;

    var chartData = [];

    for (var i = 0; i < dates.length; i++) {
        var day = dates[i].toString(); // Extract day from the date
        var count = data[i]; // Get the count value for the corresponding date
        chartData.push({ label: day, y: count });
    }

   

    var chart = new CanvasJS.Chart("chartContainer", {
      theme: "light1",
      animationEnabled: false,
     
      axisY: {
        gridThickness: 1,
         gridColor: "lightgray",
         labelFontColor: "lightgray"

      },
      axisX: {
        labelFontColor: "lightgray",
        interval: 1 // Set interval to 1 to display all labels
      },
      toolTip: {
        content: "{y}" // Show only the count value in the tooltip
      },
      data: [{
        type: "column",
        color: "rgb(0, 143, 251)",
        dataPoints: chartData
      }]
    });

    chart.render();
  }
function getManPowerData() {
    var month = $('#monthg').val();
    var year = $('#yearg').val();
    var project_id = $('#projectg').val();
    var vendor_id = $('#vendorg').val();
    var item_desc = $('#item_descg').val();
    $.ajax({
        url: "{{ url('get-manpower-graph') }}",
        type: "POST",
        data: { month: month,year:year,project_id:project_id,vendor_id:vendor_id,item_desc:item_desc},
        dataType: "json",
        success: function(response) {
          
            updateManpowerChart(response);
            
        },
        error: function(xhr, status, error) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                $.each(xhr.responseJSON.errors, function(key, item) {
                    alert(item);
                });
            } else {
                // Handle other types of errors or no errors in the response
                alert("An error occurred. Please try again later.");
            }
        },
    });
}

function updateManpowerChart(response) {

  var dates = response.data.date;
    var data = response.data.data;

    var chartData = [];

    for (var i = 0; i < dates.length; i++) {
        var day = dates[i].toString(); // Extract day from the date
        var count = data[i]; // Get the count value for the corresponding date
        chartData.push({ label: day, y: count });
    }

   

    var chart = new CanvasJS.Chart("chartContainerManpower", {
      theme: "light1",
      animationEnabled: false,
     
      axisY: {
        gridThickness: 1,
         gridColor: "lightgray",
         labelFontColor: "lightgray"

      },
      axisX: {
        labelFontColor: "lightgray",
        interval: 1 // Set interval to 1 to display all labels
      },
      toolTip: {
        content: "{y}" // Show only the count value in the tooltip
      },
      data: [{
        type: "column",
        color: "rgb(0, 143, 251)",
        dataPoints: chartData
      }]
    });

    chart.render();
  }
</script>
    <!--/ Layout wrapper -->

    

 
  </body>
</html>