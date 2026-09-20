<div class="navbar-custom">
    <ul class="list-unstyled topnav-menu float-right mb-0">

        <li class="d-none d-sm-block">
            <!-- <form class="app-search">
                <div class="app-search-box">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search...">
                        <div class="input-group-append">
                            <button class="btn" type="submit">
                                <i class="fe-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form> -->
        </li>

        <li class="dropdown notification-list">
            <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect" data-toggle="dropdown" href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">
                <img src="{{ URL::to('/') }}/assets/images/users/default-user.png" alt="user-image" class="rounded-circle">
                <span class="pro-user-name ml-1">
                    {{ auth()->user()->name }}
                    <i class="mdi mdi-chevron-down"></i>
                </span>
            </a>

            <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                <!-- item-->
                <div class="dropdown-item noti-title">
                    <h6 class="text-overflow m-0">Welcome !</h6>
                </div>

                <!-- item-->
                {{-- <a href="javascript:void(0);" class="dropdown-item notify-item">
                    <i class="fe-user"></i> <span>My Account</span>
                </a> --}}

                <!-- item-->
                {{-- <a href="javascript:void(0);" class="dropdown-item notify-item">
                    <i class="fe-settings"></i> <span>Settings</span>
                </a> --}}

                <!-- item-->
                <a href="{{ route('auth.logout') }}" class="dropdown-item notify-item">
                    <i class="fe-log-out"></i> <span>Logout</span>
                </a>

            </div>

        </li>

        <!-- <li class="dropdown notification-list">
            <a href="javascript:void(0);" class="nav-link right-bar-toggle waves-effect">
                <i class="fe-settings noti-icon"></i>
            </a>
        </li> -->
    </ul>



    <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
        <li>
            <button class="button-menu-mobile disable-btn">
                <i class="fe-menu"></i>
            </button>
        </li>

        <li>
            <h4 class="page-title-main">@yield('title')</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Indococo</a></li>
                <li class="breadcrumb-item active">@yield('title')</li>
            </ol>
        </li>

    </ul>
</div>
