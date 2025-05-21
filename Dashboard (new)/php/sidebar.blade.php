
  
  <aside id="sidebar" class="expand">
      <div class="logo-sidebar">
          <div class="sidebar-logo">
              <a href="{{url("$folder")}}">
                  <img src="{{asset("assets_dashboard/img/logo-new-lambda.png")}}" class="img-fluid" alt="">
              </a>
          </div>
      </div>
      <ul class="sidebar-nav">
          <li class="sidebar-item">
              <a href="{{url("$folder")}}" class="sidebar-link">
                  <img src="{{asset("assets_dashboard/img/icon-menu/1.svg")}}" class="img-fluid img-icon" alt=""> <span>DashBoard</span>
              </a>
          </li>
          <li class="sidebar-item">
              <a href="{{url("$folder/journey")}}" class="sidebar-link">
                  <img src="{{asset("assets_dashboard/img/icon-menu/2.svg")}}" class="img-fluid img-icon" alt="">
                  <span>Journeys</span>
              </a>
          </li>
          <li class="sidebar-item">
              <a href="{{url("$folder/set-work")}}" class="sidebar-link">
                  <img src="{{asset("assets_dashboard/img/icon-menu/3.svg")}}" class="img-fluid img-icon" alt=""> <span>Set Tasks</span>
              </a>
          </li>
          <li class="sidebar-item">
              <a href="{{url("$folder/review")}}" class="sidebar-link">
                  <img src="{{asset("assets_dashboard/img/icon-menu/4.svg")}}" class="img-fluid img-icon" alt="">
                  <span>Review</span>
              </a>
          </li>
          <li class="sidebar-item">
              <a href="{{url("$folder/worksheet")}}" class="sidebar-link">
                  <img src="{{asset("assets_dashboard/img/icon-menu/5.svg")}}" class="img-fluid img-icon" alt="">
                  <span>WorkSheet</span>
              </a>
          </li>
          <li class="sidebar-item">
              <a href="{{url("$folder/hangouts")}}" class="sidebar-link">
                  <img src="{{asset("assets_dashboard/img/icon-menu/6.svg")}}" class="img-fluid img-icon-step2" alt="">
                  <span>Hangouts</span>
              </a>
          </li>
          <li class="sidebar-item">
            <a href="{{url("$folder/profile")}}" class="sidebar-link">
                <img src="{{asset("assets_dashboard/img/icon-menu/7.svg")}}" class="img-fluid img-icon-step2" alt="">
                <span>Profile</span>
            </a>
        </li>
      </ul>

    <div class="sidebar-footer">
        <a href="javascript:void(0)" onclick="openQuestionModal()" class="sidebar-link">
            <img src="{{asset("assets_dashboard/img/icon-menu/8.svg")}}" class="img-fluid img-icon-step2" alt="">
        </a>
    </div>

      <div class="sidebar-footer">
          <div class="dropdown ">
              <a class=" dropdown-toggle sidebar-link" href="javascript:void(0)" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  <img src="{{asset("assets_dashboard/img/icon-menu/9.svg")}}" class="img-fluid img-icon" alt=""> <span>More</span>
              </a>

              <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="javascript:void(0)" onclick="logoutChild();"><i class="lni lni-user-multiple-4"></i> <span>Switch to parent account</span></a></li>
                  <li><a class="dropdown-item" href="javascript:void(0)" onclick="logoutChild();"> <i class="lni lni-exit"></i> <span>Logout</span></a></li>
              </ul>
          </div>

      </div>
  </aside>