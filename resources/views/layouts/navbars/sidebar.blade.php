@php
    $user = Auth::user();
@endphp
<nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light bg-white" id="sidenav-main">
    <div class="container-fluid">
        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#sidenav-collapse-main" aria-controls="sidenav-main" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Brand -->
        <a class="navbar-brand pt-0" href="{{ url('/') }}">
            IT ACP
        </a>
        <!-- User -->
        <ul class="nav align-items-center d-md-none">
            <li class="nav-item dropdown">
                <a class="nav-link" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="media align-items-center">
                        <span class="avatar avatar-sm rounded-circle">
                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-1-800x800.jpg">
                        </span>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right">
                    <div class=" dropdown-header noti-title">
                        <h6 class="text-overflow m-0">{{ __('Welcome!') }}</h6>
                    </div>
                    <a href="{{ url('/profile') }}" class="dropdown-item">
                        <i class="ni ni-single-02"></i>
                        <span>{{ __('My profile') }}</span>
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="ni ni-settings-gear-65"></i>
                        <span>{{ __('Settings') }}</span>
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="ni ni-calendar-grid-58"></i>
                        <span>{{ __('Activity') }}</span>
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="ni ni-support-16"></i>
                        <span>{{ __('Support') }}</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="{{ url('/logout') }}" class="dropdown-item" onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();">
                        <i class="ni ni-user-run"></i>
                        <span>{{ __('Logout') }}</span>
                    </a>
                </div>
            </li>
        </ul>
        <!-- Collapse -->
        <div class="collapse navbar-collapse" id="sidenav-collapse-main">
            <!-- Collapse header -->
            <div class="navbar-collapse-header d-md-none">
                <div class="row">
                    <div class="col-6 collapse-brand">
                        <a href="{{ url('/') }}">
                            <img src="{{ asset('argon') }}/img/brand/blue.png">
                        </a>
                    </div>
                    <div class="col-6 collapse-close">
                        <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#sidenav-collapse-main" aria-controls="sidenav-main" aria-expanded="false" aria-label="Toggle sidenav">
                            <span></span>
                            <span></span>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Form -->
            <form class="mt-4 mb-3 d-md-none">
                <div class="input-group input-group-rounded input-group-merge">
                    <input type="search" class="form-control form-control-rounded form-control-prepended" placeholder="{{ __('Search') }}" aria-label="Search">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <span class="fa fa-search"></span>
                        </div>
                    </div>
                </div>
            </form>
            <!-- Navigation -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/') }}">
                        <i class="ni ni-tv-2 text-primary"></i> {{ __('Dashboard') }}
                    </a>
                </li>

                {{-- 
                        Users
                 --}}
                @if( $user->hasAnyPermission( 'admin', 'edit users' ) )
                    <li class="nav-item">
                        <a class="nav-link {{ ( request()->is( 'users*' ) || request()->is( 'roles*' ) || request()->is( 'permissions*' ) ) ? 'active' : '' }}" href="#users" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="users">
                            <i class="fas fa-users"></i>
                            <span class="nav-link-text">{{ __('Users and Permissions') }}</span>
                        </a>

                        <div class="collapse {{ ( request()->is( 'users*' ) || request()->is( 'roles*' ) || request()->is( 'permissions*' ) ) ? 'show' : '' }}" id="users">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item {{ request()->is( 'users*' ) ? 'active' : '' }} ">
                                    <a class="nav-link" href="{{ url('/users') }}">
                                        {{ __('Users') }}
                                    </a>
                                </li>
                                <li class="nav-item {{ request()->is( 'roles*' ) ? 'active' : '' }} ">
                                    <a class="nav-link" href="{{ url('/roles') }}">
                                        {{ __('Roles') }}
                                    </a>
                                </li>
                                <li class="nav-item {{ request()->is( 'permissions*' ) ? 'active' : '' }} ">
                                    <a class="nav-link" href="{{ url('/permissions') }}">
                                        {{ __('Permissions') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                {{-- 
                        Campuses
                 --}}
                @if( $user->hasAnyPermission( 'admin', 'view campus' ) )
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/campuses') }}">
                            <i class="fas fa-school"></i> {{ __('Campuses') }}
                        </a>
                    </li>
                @endif

                {{-- 
                        Apple Classroom
                 --}}
                @if( $user->hasAnyPermission( 'admin', 'view appleclassroom' ) )
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is( 'appleclassroom*' ) ? 'active' : '' }}" href="#apple" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="apple">
                            <i class="fab fa-apple"></i>
                            <span class="nav-link-text">{{ __('Apple Classroom') }}</span>
                        </a>

                        <div class="collapse {{ request()->is( 'appleclassroom*' ) ? 'show' : '' }}" id="apple">
                            <ul class="nav nav-sm flex-column">
                                @if( $user->hasAnyPermission( 'admin', 'edit appleclassroom' ) )
                                    <li class="nav-item {{ request()->is( 'appleclassroom' ) ? 'active' : '' }} ">
                                        <a class="nav-link" href="{{ url('/appleclassroom') }}">
                                            {{ __('Update') }}
                                        </a>
                                    </li>
                                @endif
                                @if( $user->hasAnyPermission( 'admin', 'view appleclassroom' ) )
                                    <li class="nav-item {{ request()->is( 'appleclassroom/archives' ) ? 'active' : '' }} ">
                                        <a class="nav-link" href="{{ url('/appleclassroom/archives') }}">
                                            {{ __('Archive') }}
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif

                {{-- 
                        network
                 --}}
                 @if( $user->hasAnyPermission( 'admin', 'view network' ) )
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is( 'network*' ) ? 'active' : '' }}" href="#network" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="cisco">
                                <i class="fas fa-network-wired"></i>                            <span class="nav-link-text">Network</span>
                        </a>

                        <div class="collapse {{ request()->is( 'network*' ) ? 'show' : '' }}" id="network">
                            <ul class="nav nav-sm flex-column">
                                @if( $user->hasAnyPermission( 'admin', 'view network' ) )
                                    <li class="nav-item {{ request()->is( 'network/vlans*' ) ? 'active' : '' }} ">
                                        <a class="nav-link" href="{{ url('/network/vlans') }}">
                                            Vlans
                                        </a>
                                    </li>
                                @endif
                                @if( $user->hasAnyPermission( 'admin', 'view network' ) )
                                    <li class="nav-item {{ request()->is( 'network/switches*' ) ? 'active' : '' }} ">
                                        <a class="nav-link" href="{{ url('/network/switches') }}">
                                            Switches
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif

                {{-- 
                        Cisco
                 --}}
                @if( $user->hasAnyPermission( 'admin', 'view cisco' ) )
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is( 'cisco*' ) ? 'active' : '' }}" href="#cisco" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="cisco">
                            <i class="fas fa-tablet-alt"></i>
                            <span class="nav-link-text">{{ __('Cisco MDM') }}</span>
                        </a>

                        <div class="collapse {{ request()->is( 'cisco*' ) ? 'show' : '' }}" id="cisco">
                            <ul class="nav nav-sm flex-column">
                                @if( $user->hasAnyPermission( 'admin', 'edit cisco' ) )
                                    <li class="nav-item {{ request()->is( 'cisco/wipe*' ) ? 'active' : '' }} ">
                                        <a class="nav-link" href="{{ url('/cisco/wipe') }}">
                                            {{ __('Wipe') }}
                                        </a>
                                    </li>
                                @endif
                                @if( $user->hasAnyPermission( 'admin', 'view cisco' ) )
                                    <li class="nav-item {{ request()->is( 'cisco/search*' ) ? 'active' : '' }} ">
                                        <a class="nav-link" href="{{ url('/cisco/search') }}">
                                            {{ __('Search') }}
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif
            </ul>


            <!-- Divider -->
            <hr class="my-3">
            <!-- Heading -->
            <h6 class="navbar-heading text-muted">Documentation</h6>
            <!-- Navigation -->
            <ul class="navbar-nav mb-md-3">
                <li class="nav-item">
                    <a class="nav-link" href="https://demos.creative-tim.com/argon-dashboard/docs/getting-started/overview.html">
                        <i class="ni ni-spaceship"></i> Getting started
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://demos.creative-tim.com/argon-dashboard/docs/foundation/colors.html">
                        <i class="ni ni-palette"></i> Foundation
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://demos.creative-tim.com/argon-dashboard/docs/components/alerts.html">
                        <i class="ni ni-ui-04"></i> Components
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>