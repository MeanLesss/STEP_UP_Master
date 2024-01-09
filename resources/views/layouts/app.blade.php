@guest
{{-- @include('layouts.page_template.guest') --}}
@include('login')
@endguest

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets') }}/img/apple-icon.png">
    <link rel="icon" type="image/png" href="{{ asset('assets') }}/img/favicon.png">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <!-- Extra details for Live View on GitHub Pages -->
    <title>
        Now UI Dashboard by Creative Tim
    </title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no'
        name='viewport' />
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css"
        integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <!-- CSS Files -->
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> --}}
        <link rel="stylesheet" href="{{ asset('plugins') }}/css/sweetalert2.css ">
        <link href="{{ asset('plugins') }}/js/core/bootstrap.min.css" rel="stylesheet" />
    <link href="{{ asset('plugins') }}/css/now-ui-dashboard.css?v=1.3.0" rel="stylesheet" />
    {{-- Data Table css --}}
    <link href="{{ asset('plugins') }}/js/DataTable/datatables.min.css" rel="stylesheet" />
    <link href="{{ asset('plugins') }}/js/DataTable/DataTable/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <link href="{{ asset('plugins') }}/js/DataTable/Button/css/buttons.bootstrap5.min.css" rel="stylesheet" />
    <link href="{{ asset('plugins') }}/js/DataTable/FixedHeader/fixedHeader.bootstrap5.min.css" rel="stylesheet" />
    <link href="{{ asset('plugins') }}/js/DataTable/Responsive/responsive.dataTables.min.css" rel="stylesheet" />
    <link href="{{ asset('plugins') }}/js/DataTable/Responsive/responsive.bootstrap5.min.css" rel="stylesheet" />
    <link href="{{ asset('plugins') }}/js/DataTable/Select/select.bootstrap5.min.css" rel="stylesheet" />
    <!-- CSS Just for demo purpose, don't include it in your project -->
    <script src="{{ asset('plugins') }}/js/jquery.min.js"></script>
</head>

<body class="{{ $class ?? '' }}">
    <div class="wrapper">
        <div class="full-page-background" style="background-image: url('{{ asset('plugins/img/bg14.jpg') }}')"/>

        @auth
            @include('layouts.page_template.auth')
        @endauth
        {{-- @guest
            @include('layouts.page_template.guest')
        @endguest --}}
    </div>

    <!--   Core JS Files   -->
    <script>
       $(document).ready(function() {
            if(sessionStorage.getItem('user_token') == '' || sessionStorage.getItem('user_token') == null){
                window.location.href = "/login_app";
            }
        });
    </script>
    {{-- <script src="{{ asset('plugins') }}/js/core/jquery.min.js"></script> --}}
    <script src="{{ asset('plugins') }}/js/core/popper.min.js"></script>
    <script src="{{ asset('plugins') }}/js/core/bootstrap.min.js"></script>
    <script src="{{ asset('plugins') }}/js/plugins/perfect-scrollbar.jquery.min.js"></script>
    <script src="{{ asset('plugins/js/sweetalert2.all.min.js') }}"></script>
    <!--  Google Maps Plugin    -->
    {{-- <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script> --}}
    <!-- Chart JS -->
    {{-- <script src="{{ asset('plugins') }}/js/plugins/chartjs.min.js"></script> --}}
    <script src="{{ asset('plugins') }}/js/chart.umd.js"></script>
    <!--  Notifications Plugin    -->
    <script src="{{ asset('plugins') }}/js/plugins/bootstrap-notify.js"></script>
    <!-- Control Center for Now Ui Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="{{ asset('plugins') }}/js/now-ui-dashboard.min.js?v=1.3.0" type="text/javascript"></script>
    <!-- Now Ui Dashboard DEMO methods, don't include it in your project! -->
    <script src="{{ asset('plugins') }}/demo/demo.js"></script>
    {{-- dataTable --}}
    <script src="{{ asset('plugins') }}/js/DataTable/datatables.min.js"></script>
    <script src="{{ asset('plugins') }}/js/DataTable/Responsive/responsive.dataTables.min.js"></script>
    <script src="{{ asset('plugins') }}/js/DataTable/DataTable/jquery.dataTables.min.js"></script>
    <script src="{{ asset('plugins') }}/js/DataTable/DataTable/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('plugins') }}/js/DataTable/Button/js/dataTables.buttons.min.js"></script>
    <script src="{{ asset('plugins') }}/js/DataTable/Button/js/buttons.bootstrap5.min.js"></script>
    <script src="{{ asset('plugins') }}/js/DataTable/Button/js/buttons.colVis.min.js"></script>
    <script src="{{ asset('plugins') }}/js/DataTable/Button/js/buttons.dataTables.min.js"></script>
    <script src="{{ asset('plugins') }}/js/DataTable/Button/js/buttons.html5.min.js"></script>
    <script src="{{ asset('plugins') }}/js/DataTable/Button/js/buttons.print.min.js"></script>
    <script src="{{ asset('plugins') }}/js/DataTable/FixedHeader/fixedHeader.bootstrap5.min.js"></script>
    <script src="{{ asset('plugins') }}/js/DataTable/Responsive/dataTables.responsive.min.js"></script>
    <script src="{{ asset('plugins') }}/js/DataTable/Responsive/responsive.bootstrap5.min.js"></script>
    <script src="{{ asset('plugins') }}/js/DataTable/Select/select.bootstrap5.min.js"></script>


    @stack('js')
</body>

</html>
