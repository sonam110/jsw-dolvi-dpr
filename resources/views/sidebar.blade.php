<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
          <div class="app-brand demo">
             <a href="#" class="app-brand-link">
               <img src="{{ url('/kpmg-logo.jpeg') }}" alt="logo" class="logo" width="80">
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
              <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
              <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
            </a>
          </div>

          <div class="menu-inner-shadow"></div>

          <ul class="menu-inner py-1">
           
            <li class="menu-item {{ (Request::segment(1)==='dashboard') ? 'active' :''}}">
              <a href="{{ route('dashboard',['access_key'=> $appSetting->access_key])}}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div data-i18n="Email">Dashbord</div>
              </a>
           
           </li>
            <!-- Dashboards -->
            <li class="menu-item {{ (Request::segment(1)==='summary-report'|| Request::segment(1)==='dpr-report') ? 'active open' :''}}">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-files"></i>
                <div data-i18n="Dashboards">Reports</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item {{ (Request::segment(1)==='dpr-report') ? 'active' :''}}">
                  <a href="{{ route('dpr-report',['access_key'=> $appSetting->access_key])}}" class="menu-link">
                    <div data-i18n="Analytics">Dpr Report</div>
                  </a>
                </li>
                <li class="menu-item {{ (Request::segment(1)==='summary-report') ? 'active' :''}}">
                  <a href="{{ route('summery-report',['access_key'=> $appSetting->access_key])}}" class="menu-link">
                    <div data-i18n="CRM">Summary Report</div>
                  </a>
                </li>
              
              </ul>
            </li>
          
          </ul>
        </aside>