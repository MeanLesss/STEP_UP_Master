@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Service Management',
    'activePage' => 'services',
    'activeNav' => '',
])

@section('content')
    <div class="panel-header panel-header-sm">
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    {{-- Part 1 --}}
                    <div class="card-header">
                        <h5 class="title">{{ __('All Services') }}</h5>
                    </div>
                    <div class="card-body">
                        <table id="master-service-table" class="table table-striped" style="width:100%">

                        </table>
                    </div>
                    {{-- Part 2 --}}
                    <div class="card-header">
                        <h5 class="title">{{ __('Password') }}</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('profile.password') }}" autocomplete="off">
                            @csrf
                            @method('put')
                            @include('alerts.success', ['key' => 'password_status'])
                            <div class="row">
                                <div class="col-md-7 pr-1">
                                    <div class="form-group {{ $errors->has('password') ? ' has-danger' : '' }}">
                                        <label>{{ __(' Current Password') }}</label>
                                        <input class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}"
                                            name="old_password" placeholder="{{ __('Current Password') }}" type="password"
                                            required>
                                        @include('alerts.feedback', ['field' => 'old_password'])
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-7 pr-1">
                                    <div class="form-group {{ $errors->has('password') ? ' has-danger' : '' }}">
                                        <label>{{ __(' New password') }}</label>
                                        <input class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}"
                                            placeholder="{{ __('New Password') }}" type="password" name="password"
                                            required>
                                        @include('alerts.feedback', ['field' => 'password'])
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-7 pr-1">
                                    <div class="form-group {{ $errors->has('password') ? ' has-danger' : '' }}">
                                        <label>{{ __(' Confirm New Password') }}</label>
                                        <input class="form-control" placeholder="{{ __('Confirm New Password') }}"
                                            type="password" name="password_confirmation" required>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer ">
                                <button type="submit"
                                    class="btn btn-primary btn-round ">{{ __('Change Password') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-user">
                    <div class="image">
                        <h3>(Title)Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="author">
                            <h4>Body card</h4>
                        </div>
                    </div>
                    <hr>
                    <div class="button-container">
                        <h1>Footer</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            // new DataTable('#master-service-table');
            // var table = $('#master-service-table').DataTable({
            //     responsive: true,
            //     serverSide: true,
            //     ajax: {
            //         url: '/your-server-side-url',
            //         type: 'POST'
            //     },
            //     columns: [
            //         { data: 'name' },
            //         { data: 'position' },
            //         { data: 'office' },
            //         { data: 'age' },
            //         { data: 'start_date' },
            //         { data: 'salary' }
            //     ],
            //     select: true
            // });

            // new $.fn.dataTable.FixedHeader(table);
            var data = [{
                    "Name": "Tiger Nixon",
                    "Position": "System Architect",
                    "Office": "Edinburgh",
                    "Age": "61",
                    "Start date": "2011/04/25",
                    "Salary": "$320,800"
                },
                {
                    "Name": "Garrett Winters",
                    "Position": "Accountant",
                    "Office": "Tokyo",
                    "Age": "63",
                    "Start date": "2011/07/25",
                    "Salary": "$170,750"
                },
                {
                    "Name": "Ashton Cox",
                    "Position": "Junior Technical Author",
                    "Office": "San Francisco",
                    "Age": "66",
                    "Start date": "2009/01/12",
                    "Salary": "$86,000"
                },
                // More data...
            ];
            ServiceTable('master-service-table', ['Name', 'Position', 'Office', 'Age', 'Start date', 'Salary'],data);
        });

        function ServiceTable(elementId, headers, data) {
            // Convert data array to DataTable format
            var dataSet = data.map(function(item) {
                return headers.map(function(header) {
                    return item[header];
                });
            });

            // Initialize DataTable
            $(document).ready(function() {
                $('#' + elementId).DataTable({
                    data: dataSet,
                    columns: headers.map(function(header) {
                        return {
                            title: header
                        };
                    }),
                    responsive: true,
                    fixedHeader: true,
                    select: true,
                    // serverSide: true,
                    // ajax: {
                    //     url: '/your-server-side-url',
                    //     type: 'POST'
                    // }
                });
            });
        }
    </script>
@endsection
