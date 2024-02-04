@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'User Management',
    'activePage' => 'users',
    'activeNav' => '',
])
@section('content')
    <style>
        .modal {
            --bs-modal-width: 1000px !important;
        }
    </style>
    <div class="panel-header panel-header-sm">
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    {{-- Part 1 --}}
                    <div class="card-header">
                        <h5 class="title">{{ __('All Users') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="card-body" style="border: 3px solid #161A3A; border-radius: 25px;">
                            <div class="col">
                                <div class="col-md-12">
                                    <h5>Filtering</h5>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 col-lg-3">
                                        <h6>Status</h6>
                                        <select id="search-status" placeholder="Status" class="form-control my-3">
                                            <option value="">Choose..</option>
                                            <option value="1">Active</option>
                                            <option value="2">Inactive</option>
                                        </select>
                                    </div>
                                    {{-- <div class="col-md-3 col-lg-3">
                                        <h6>Service</h6>
                                        <select id="search-service" placeholder="Service" class="form-control my-3">
                                            <option value="">Choose..</option>
                                            <option value="Software Developement">Software Developement</option>
                                            <option value="Graphic Design">Graphic Design</option>
                                        </select>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                        {{-- {{ $dataTable->table() }} --}}
                        <table id="users-table" class="table table-striped " style="width:100%;">
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <script>
        $(document).ready(function() {
            userTable();
        });
        function userTable(){
            $('#users-table').DataTable({
                serverSide: true,
                ajax: "{{ route('users.data') }}",
                columns: [{
                        title:'Id',
                        data: 'id'
                    },
                    {
                        title:'Name',
                        data: 'name'
                    },
                    {
                        title:'Email',
                        data: 'email'
                    },
                    {
                        title:'Role',
                        data: 'role'
                    },
                    {
                        title:'Created At',
                        data: 'created_at'
                    },
                    {
                        title:'Updated At',
                        data: 'updated_at'
                    },
                ],
                dom: 'Bfrtip',
                // select: true,
                lengthMenu: [
                    [10, 25, 50, -1],
                    ['10 rows', '25 rows', '50 rows', 'Show all']
                ],
                buttons: [{
                        extend: 'pageLength',
                        className: "btn-round btn-primary btn",
                    },
                    {
                        extend: 'excelHtml5',
                        className: "btn-round btn-primary btn",
                        customize: function(xlsx) {
                            var sheet = xlsx.xl.worksheets['sheet1.xml'];

                            // Loop over the cells in column `C`
                            $('row c[r^="C"]', sheet).each(function() {
                                // Get the value
                                if ($('is t', this).text() == 'New York') {
                                    $(this).attr('s', '20');
                                }
                            });
                        },
                    },
                    {
                        extend: 'pdf',
                        className: "btn-round btn-primary btn",
                    },
                    {
                        text: 'Reload',
                        className: "btn-round btn-primary btn",
                        action: function(e, dt, node, config) {
                            // dt.ajax.reload();
                            status = $('#search-status').val();
                            service = $('#search-service').val();
                            filterService(status, service);
                        }
                    }

                ],
                responsive: true,
                bDestroy: true,
                processing: true,
                fixedHeader: true,
                fixedColumn: true,
                paging: true,
                searching: true,
                columnDefs: [{
                    className: 'dtr_control'
                }],

            });
        }
    </script>

@endsection
{{-- @push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush --}}
