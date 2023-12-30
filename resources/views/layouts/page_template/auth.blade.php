<form id="logout-form" action="{{ route('logout') }}" method="GET" style="display: none;" >
    @csrf
    <input type="hidden" name="user_token" value="{{session('user_token')}}">
</form>
@include('layouts.navbars.sidebar')
<div class="main-panel">
    @include('layouts.navbars.navs.auth')
    @yield('content')
    @include('layouts.footer')
</div>
