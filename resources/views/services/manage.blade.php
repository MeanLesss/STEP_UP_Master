@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Service Management',
    'activePage' => 'services',
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
            <div class="col-md-10">
                <div class="card">
                    {{-- Part 1 --}}
                    <div class="card-header">
                        <h5 class="title">{{ __('All Services') }}</h5>
                    </div>
                    <div class="card-body">
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
            <div class="col-md-2">
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
            ]);
        });

        function ServiceTable(elementId, headers, status, service_type) {
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
                                        if (data.hasOwnProperty(key)) {
                                            // images += "- " + key + "<br>";
                                            console.log(data[key]['jpg']);
                                            images += `<img src="${data[key]['jpg']}" alt="${key}" style="width: auto; height: 100px;">`;
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
                                    if (col.title == "attachments") {
                                        // for (var key in col.data) {
                                        //     if (col.data.hasOwnProperty(key)) {
                                        //         console.log(col.data.value,key);
                                        //         if(key != 0 && key != 1 && key != 2 && key != ''){

                                        //             var value = col.data[key];
                                        //             console.log(value);
                                        //             columnData += `<tr data-dt-row="${col.rowIndex }" data-dt-column="${col.columnIndex}">
                                        //                 <td>${key}:</td>
                                        //                 <td><img src="${value}" alt="${key}"></td>
                                        //                 </tr>`;
                                        //         }
                                        //     }
                                        // }
                                    }
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
                            extend: 'pageLength'
                        },
                        {
                            extend: 'excelHtml5',
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
                            extend: 'pdf'
                        },
                        {
                            text: 'Reload',
                            action: function(e, dt, node, config) {
                                dt.ajax.reload();
                            }
                        },
                    ],
                    bDestroy: true,
                    processing: true,
                    fixedHeader: true,
                    fixedColumn: true,
                    paging: true,
                    searching: false,
                    columnDefs: [{
                        className: 'dtr_control'
                    }],
                    serverSide: true,
                    ajax: {
                        url: "{{ url('/service/management/pending') }}",
                        type: 'POST',
                        dataType: 'JSON',
                        data: {
                            '_token': "<?= csrf_token() ?>",
                            service_type: service_type,
                            status: status
                        }
                    }
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
                        });
                    }
                }
            };

            $.ajax(settings).done(function(response) {
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
                ]);
            });
        }
    </script>
@endsection
