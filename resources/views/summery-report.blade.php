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
    .requiredLabel {
    color: #ff0000;
 }
 .btn-primary {
    border-color: #00338d !important;
    background-color: #00338d !important;
    color: #fff !important;
}
.btn:hover {
    border-color: #00338d !important;
    background-color: #00338d !important;
    color: #fff !important;
}
.btn {
    box-shadow: none;
    font-weight: 500;
}
.text-primary{
   color: #00338d !important;
}
.bg-menu-theme.menu-vertical .menu-item.active > .menu-link:not(.menu-toggle) {
    background: #00338d !important;
    box-shadow: 0px 2px 6px 0px rgba(115, 103, 240, 0.48);
    color: #fff !important;
}
.card{
  margin-bottom: 15px;
}
.card-inside{
      padding: 0px 0px 0px 21px;
}
td {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
        font-size: 13px;
    }
 td.bold {
        font-weight: bold;
        font-size: 13px;
    }
    th {
       font-weight: bold;
    }
    .margin-class{
      margin-top: 25px;
    }
</style>
<script type="text/javascript">
      var appurl = '{{url("/")}}/';
    </script>
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
  
                <div class="col-lg-12 col-sm-6 mb-4">
                  <div class="card">
                    <div class="card-header border-bottom" >
                      <h4 class="text-primary fw-bolder mb-0 card-title">Summary Report</h4>
                     
                    </div>
                    <div class="card-header border-bottom" >
                      <div class="row">
                       
                        <div class="col-md-3 col-xs-12 col-sm-6" id="datefrom" >
                           <label class="form-label" for="date">Select Data Date <span class="requiredLabel">*</span></label>
                          <div class="form-group">
                           <input type="text" name="from_date" id="from_date" class="form-control" placeholder="From date">
                         </div>
                         </div>
                        
                        
                      <div class="col-md-3 col-xs-12 col-sm-6" id="datefrom" >
                           <label class="form-label" for="date">Select Data Date <span class="requiredLabel">*</span></label>
                          <div class="form-group">
                          <input type="text" name="to_date" id="to_date" class="form-control" placeholder="To date">
                         </div>
                         </div>

                         <div class="col-lg-3 col-sm-6 mb-4">
                          <label class="form-label" for="date">Select Vendor <span class="requiredLabel">*</span></label>
                         <select class="form-control" id="vendor_id">
                          <option value="" selected >Vendor</option>
                           @foreach($vendors as $vendor)
                         
                          <option  value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                      </div>

                      
                      <div class="col-lg-3 col-sm-6 mb-4">
                        <label class="form-label" for="date">Item Description <span class="requiredLabel">*</span></label>
                         <select class="form-control" id="item_desc">
                          <option value="" selected >Item Description</option>
                           @foreach($itemDescs as $item)
                         
                          <option value="{{ $item->id }}">{{ $item->title }}</option>
                            @endforeach
                        </select>
                      </div>
                      <div class="col-lg-2 col-sm-6 mb-4 margin-class">
                        <button type="button" class="btn btn-primary extrabtn" data-type="">Submit</button>
                      </div>
                      <div class="col-lg-3 col-sm-6 mb-4 margin-class">
                        <button type="button" class="btn btn-primary extrabtn" data-type="log">Dpr Summary</button>
                      </div>
                     </div>
                    
                    </div>
                    
                    
                  </div>
                </div>
           

            
              </div>
              <div  class="row">
                <div  id="summery-report">
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
<script type="text/javascript"  src="{{ url('assets/js/main.js') }}"></script>
<script type="text/javascript"  src="{{ url('assets/js/dashboards-analytics.js') }}"></script>
<script type="text/javascript"  src="{{ url('assets/js/jquery-ui.js') }}"></script>
 <link rel="stylesheet" href="{{ url('assets/css/jquery-ui.css') }}">
<script type="text/javascript"  src="{{ url('assets/js/custom.js') }}"></script>

 
  </body>
</html>