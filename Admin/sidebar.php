<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <a href="#" class="nav-link">
                <div class="nav-profile-text d-flex flex-column">
                    <span class="font-weight-bold mb-2">Admin </span>
                    <span class="text-secondary text-small">Administrator</span>
                </div>
                <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="../dashboard/dashboard">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#tenantSubMenu" aria-expanded="false" aria-controls="tenantSubMenu">
                <span class="menu-title">Tenant</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-account-multiple menu-icon"></i>
            </a>
            <div class="collapse" id="tenantSubMenu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="../customer_management/customer_management">Tenant Management</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../customer_management/booked">Tenant List</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../customer_management/view_contracts">Contract List</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
                  <a class="nav-link" href="../calendar/calendar">
                      <span class="menu-title">Calendar</span>
                      <i class="mdi mdi-calendar menu-icon"></i>
                  </a>
              </li>
        <li class="nav-item">
                  <a class="nav-link" href="../room_management/room_management">
                      <span class="menu-title">Room Management</span>
                      <i class="mdi mdi-houzz-box menu-icon"></i>
                  </a>
              </li>
        <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#paymentSubMenu" aria-expanded="false" aria-controls="paymentSubMenu">
                    <span class="menu-title">Payment</span>
                    <i class="menu-arrow"></i>
                    <i class="mdi mdi-wallet menu-icon"></i>
                </a>
                <div class="collapse" id="paymentSubMenu">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="../payment/payment">Completed</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../payment/pending">Pending</a>
                        </li>
                    </ul>
                </div>
            </li>       
        <li class="nav-item">
                  <a class="nav-link" href="../announcement/announcement">
                      <span class="menu-title">Announcement</span>
                      <i class="mdi mdi-bell-ring menu-icon"></i>
                  </a>
              </li>  
        <li class="nav-item">
                  <a class="nav-link" href="../booking_management/booking_management">
                      <span class="menu-title">Booking</span>
                      <i class="mdi mdi-table menu-icon menu-icon"></i>
                  </a>
              </li>  
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#incomeReportSubMenu" aria-expanded="false" aria-controls="incomeReportSubMenu">
                <span class="menu-title">Income Report</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-chart-bar menu-icon"></i>
            </a>
            <div class="collapse" id="incomeReportSubMenu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="../income_report/income_report">Chart</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../income_report/preview_income_report">Yearly Report</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../income_report/monthly">Monthly Report</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#settingsSubMenu" aria-expanded="false" aria-controls="settingsSubMenu">
                <span class="menu-title">Settings</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-settings menu-icon"></i>
            </a>
            <div class="collapse" id="settingsSubMenu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="../account_sett/account_sett">Account Settings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../account_sett/change_email">Change Email</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../website_sett/website_sett">Website Settings</a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
</nav>
