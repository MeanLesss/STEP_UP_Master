<div class="sidebar" data-color="dark-purple">
    <!--
    Tip 1: You can change the color of the sidebar using: data-color="blue | green | orange | red | yellow"
-->
    <div class="logo">
        <div class="d-flex w-100" style="width: 100%; height: 100%; object-fit: contain;">
            <a href="/index" class="simple-text logo-mini">
                <img src="{{ asset('storage/app_img/Step_up_logo.png') }}"
                    style="width: 100%; height: 100%; object-fit: contain;" />
            </a>
        </div>
    </div>
    <div class="sidebar-wrapper" id="sidebar-wrapper">
        <ul class="nav">
            <li class="@if ($activePage == 'home') active @endif">
                <a href="{{ route('home') }}">
                    <i class="now-ui-icons design_app"></i>
                    <p>{{ __('Dashboard') }}</p>
                </a>
            </li>
            <li>
                <a data-toggle="collapse" href="#laravelExamples">
                    <i class="now-ui-icons loader_gear"></i>
                    <p>
                        {{ __('Management') }}
                        <b class="caret"></b>
                    </p>
                </a>
                <div class="collapse show" id="laravelExamples">
                    <ul class="nav">
                        <li class="@if ($activePage == 'services') active @endif">
                            <a href="{{ route('service.management') }}">
                                <i class="now-ui-icons files_single-copy-04"></i>
                                <p> {{ __('Service Management') }} </p>
                            </a>
                        </li>
                        <li class="@if ($activePage == 'service_order') active @endif">
                            <a href="{{ route('service.order.management') }}">
                                <i class="now-ui-icons education_paper"></i>
                                <p> {{ __('Service Order Management') }} </p>
                            </a>
                        </li>
                        <li class="@if ($activePage == 'profile') active @endif">
                            <a href="{{ route('profile.edit') }}">
                                <i class="now-ui-icons users_single-02"></i>
                                <p> {{ __('User Profile') }} </p>
                            </a>
                        </li>
                        <li class="@if ($activePage == 'users') active @endif">
                            <a href="{{ route('users.index') }}">
                                <i class="now-ui-icons design_bullet-list-67"></i>
                                <p> {{ __('User Management') }} </p>
                            </a>
                        </li>
                    </ul>
                </div>
            <li class="@if ($activePage == 'icons') active @endif">
                <a href="{{ route('page.index', 'icons') }}">
                    <i class="now-ui-icons education_atom"></i>
                    <p>{{ __('Icons') }}</p>
                </a>
            </li>
            <li class = "@if ($activePage == 'maps') active @endif">
                <a href="{{ route('page.index', 'maps') }}">
                    <i class="now-ui-icons location_map-big"></i>
                    <p>{{ __('Maps') }}</p>
                </a>
            </li>
            <li class = " @if ($activePage == 'notifications') active @endif">
                <a href="{{ route('page.index', 'notifications') }}">
                    <i class="now-ui-icons ui-1_bell-53"></i>
                    <p>{{ __('Notifications') }}</p>
                </a>
            </li>
            <li class = " @if ($activePage == 'table') active @endif">
                <a href="{{ route('page.index', 'table') }}">
                    <i class="now-ui-icons design_bullet-list-67"></i>
                    <p>{{ __('Table List') }}</p>
                </a>
            </li>
            <li class = "@if ($activePage == 'typography') active @endif">
                <a href="{{ route('page.index', 'typography') }}">
                    <i class="now-ui-icons text_caps-small"></i>
                    <p>{{ __('Typography') }}</p>
                </a>
            </li>
            <li class = "">
                <a href="{{ route('page.index', 'upgrade') }}" class="bg-info">
                    <i class="now-ui-icons arrows-1_cloud-download-93"></i>
                    <p> {{ Auth::user()->name }}</p>
                </a>
            </li>
        </ul>
    </div>
</div>
