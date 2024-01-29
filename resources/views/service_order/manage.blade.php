@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Service Order Management',
    'activePage' => 'service_order',
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
                        <h5 class="title">{{ __('All Orders') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="card-body" style="border: 3px solid #161A3A; border-radius: 25px;">
                            <div class="col">
                                <div class="col-md-12">
                                    <h5>{{ __('Filtering') }}</h5>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 col-lg-3">
                                        <h6>Status</h6>
                                        <select id="search-status" placeholder="Status" class="form-control my-3">
                                            <option value="">Choose..</option>
                                            <option value="-1">Expired / Declined</option>
                                            <option value="0">Pending</option>
                                            <option value="1">Active</option>
                                            <option value="2">Cancel</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <h6>Service</h6>
                                        <select id="search-service" placeholder="Service" class="form-control my-3">
                                            <option value="">Choose..</option>
                                            <option value="Software Developement">Software Developement</option>
                                            <option value="Graphic Design">Graphic Design</option>
                                        </select>
                                    </div>
                                    {{-- <div class="col-md-3 col-lg-3">
                                        <h6>Search Text</h6>
                                        <input type="text" id="search-input" placeholder="Search something"
                                            class="form-control my-3" />
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <h6>Action</h6>
                                        <button class="btn btn-primary btn-round my-1" id="btn-search">Serach</button>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                        <table id="master-service-table" class="table table-striped " style="width:100%;">
                        </table>
                    </div>
                    {{-- Part 2 --}}
                    <div class="card-header">
                        <h5 class="title">{{ __('Password') }}</h5>
                    </div>
                    <div class="card-body">

                    </div>
                </div>
            </div>

        </div>
    </div>
    <script>
        var status = '';
        var service = '';
        $(document).ready(function() {
            var status = $('#search-status').val();
            var service = $('#search-service').val();

            filterOrder(status, service);

        });

        function ServiceTable(elementId, headers, data, status, service_type) {
            // Convert data array to DataTable format
            // Initialize DataTable
            $(document).ready(function() {
                $('#' + elementId).DataTable({
                    columns: headers.map(function(header) {
                        var columnConfig = {
                            title: header,
                            data: header
                        };
                        if (header == 'status') {
                            columnConfig.render = function(data, type, row, meta) {
                                switch (data) {
                                    case -1:
                                        return '<span class="badge text-bg-danger">Expired/Declined </span>';
                                        break;
                                    case 0:
                                        return '<span class="badge text-bg-warning">Pending </span>';
                                        break;
                                    case 1:
                                        return '<span class="badge text-bg-success">Active </span>';
                                        break;
                                    case 2:
                                        return '<span class="badge text-bg-primary">Cancel </span>';
                                        break;
                                    default:
                                        return `<span class="badge text-bg-dark">Unknown</span>`;
                                        break;
                                }
                            }
                        }
                        if (header == 'attachments') {
                            columnConfig.render = function(data, type, row, meta) {
                                var images = "";
                                if (data != null) {

                                    // data = data.replace(/&quot;/g, '\"');
                                    // var array = JSON.parse(data);
                                    for (var key in data) {
                                        console.log(data);
                                        if (data.hasOwnProperty(key)) {
                                            var value = data[key];
                                            // Check if value is an object
                                            if (typeof value !== 'object' && value !== null && !
                                                Array.isArray(value) && typeof value ===
                                                'string') {
                                                // If it is an object, access the 'jpg' property
                                                // images +=
                                                //     `<img src="${value['jpg']}" alt="${key}" style="width: auto; height: 100px;">`;

                                                // If it's a string, handle it as before
                                                images +=
                                                    `<img src="${value}" alt="${key}" style="width: auto; height: 100px;">`;
                                            }
                                        }
                                    }


                                }
                                return images;
                            }
                        }

                        return columnConfig;
                    }),
                    responsive: {
                        details: {
                            display: $.fn.dataTable.Responsive.display.modal({
                                header: function(row) {
                                    var data = row.data();
                                    return 'Details for ' + data['title'] + ' / ID:' + data[
                                        'id'];
                                }
                            }),
                            renderer: function(api, rowIdx, columns) {
                                // This is the default renderer function. Modify it to add your custom items.
                                var id = 0;
                                var data = $.map(columns, function(col, i) {
                                    var columnData = '';
                                    columnData = '<tr data-dt-row="' + col.rowIndex +
                                        '" data-dt-column="' + col.columnIndex + '">' +
                                        '<td>' + col.title + ':' + '</td> ' +
                                        '<td>' + col.data + '</td>' +
                                        '</tr>';
                                    console.log(col.data);

                                    if (col.title == "id") id = col.data;

                                    if (col.title == 'status' && col.data.includes('Pending')) {
                                        columnData += `<tr>
                                                        <td>Approval :</td>
                                                        <td>
                                                            <button class="btn btn-success" onclick="serviceApproval(${id},true)">Approve</button>
                                                            <button class="btn btn-danger" onclick="serviceApproval(${id},false)">Reject</button>
                                                        </td>
                                                    </tr>`;
                                    }
                                    return columnData;
                                }).join('');

                                return data ? $('<table/>').append(data) : false;
                            }
                        },
                    },
                    fixedHeader: true,
                    select: true,
                    dom: 'Bfrtip',
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
                                filterOrder(status, service);
                            }
                        }

                    ],
                    bDestroy: true,
                    processing: true,
                    fixedHeader: true,
                    fixedColumn: true,
                    paging: true,
                    searching: true,
                    columnDefs: [{
                        className: 'dtr_control'
                    }],
                    data: data,
                    // serverSide: true,
                    // ajax: {
                    //     url: "{{ url('/service/management/pending') }}",
                    //     type: 'POST',
                    //     dataType: 'JSON',
                    //     data: {
                    //         '_token': "<?= csrf_token() ?>",
                    //         service_type: service_type,
                    //         status: status
                    //     }
                    // }
                });
            });
        }

        function serviceApproval(service_id, isApprove) {
            var settings = {
                "url": "{{ url('/service/management/approval') }}",
                "method": "POST",
                "timeout": 0,
                "headers": {
                    "Content-Type": "application/json",
                    "Authorization": "Bearer " + "{{ session('user_token') }}"
                },
                "data": JSON.stringify({
                    '_token': "<?= csrf_token() ?>",
                    "service_id": service_id,
                    "isApprove": isApprove
                }),
                "success": function(response) {
                    if (response != null && response.verified) {
                        Swal.fire({
                            icon: response.status,
                            title: "Success",
                            text: response.msg,
                        }).then(function() {
                            filterOrder(status, service);
                        });
                    }
                }
            };


            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax(settings).done(function(response) {});

                } else {
                    Swal.fire({
                        title: "Cacneled!",
                        text: "The process has been canceled.",
                        icon: "error"
                    });
                }
            });
        }

        function filterOrder(status = '', service = '', input_email = '') {
            var settings = {
                "url": "{{ url('/service/order/data') }}",
                "method": "POST",
                "timeout": 0,
                "headers": {
                    "Content-Type": "application/json",
                    "Authorization": "Bearer " + "{{ session('user_token') }}"
                },
                "data": JSON.stringify({
                    '_token': "<?= csrf_token() ?>",
                    "status": status,
                    "service": service,
                    "input_email": input_email,
                }),
                "success": function(response) {
                    if (response != null && response.msg != null && response.msg != '') {
                        Swal.fire({
                            icon: response.status,
                            title: "Error",
                            text: response.msg,
                        });
                    } else {
                        ServiceTable('master-service-table', ["id",
                            "title",
                            "description",
                            "status",
                            "attachments",
                            "requirement",
                            "price",
                            "discount",
                            "service_type",
                            "start_date",
                            "end_date",
                            "created_by",
                            "updated_by",
                            "created_at",
                            "updated_at",
                            "view",
                            "service_rate",
                            "service_ordered_count"
                        ], response.data);
                    }
                },
                "error": function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    if (jqXHR.status == 401) {
                        // Handle the 401 error here
                        Swal.fire({
                            icon: jqXHR.responseJSON.status,
                            title: "Error",
                            text: jqXHR.responseJSON.msg,
                        });
                    }
                }
            };

            $.ajax(settings).done(function(response) {

            });
        }
    </script>
    {{-- // UI Action Script --}}
    <script>
        $('#search-status').change(function() {
            status = $('#search-status').val();
            service = $('#search-service').val();
            console.log(status, service);
            filterOrder(status, service);
        });
        $('#search-service').change(function() {
            status = $('#search-status').val();
            service = $('#search-service').val();
            console.log(status, service);
            filterOrder(status, service);

        });
    </script>
@endsection
