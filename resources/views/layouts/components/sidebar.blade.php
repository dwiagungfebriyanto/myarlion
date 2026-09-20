<div class="left-side-menu">

    <div class="slimscroll-menu">

        <!-- LOGO -->
        <div class="logo-box">
            <a href="{{route('dashboard')}}" class="logo">
                <span class="logo-lg">
                    <img src="{{ URL::to('/') }}/assets/images/logo-arlion.png" alt="" height="45">
                </span>
                <span class="logo-sm">
                    <img src="{{ URL::to('/') }}/assets/images/logo-arlion.png" alt="" height="45">
                </span>
            </a>
        </div>

        <!-- User box -->
        <div class="user-box">
            <img src="{{ URL::to('/') }}/assets/images/users/default-user.png" alt="user-img" title="Mat Helme" class="rounded-circle" height="48">
            <div class="dropdown">
                <a href="#" class="text-dark dropdown-toggle h5 mt-2 mb-1 d-block" data-toggle="dropdown">{{ auth()->user()->name }}</a>
            </div>
            <p class="text-muted">{{ auth()->user()->getRoleNames() }}</p>
        </div>

        <!--- Sidemenu -->
        <div id="sidebar-menu">

            <ul class="metismenu" id="side-menu">

                @include('layouts.components.sidebar-menu')

            </ul>

        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

</div>
