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
            <div class="col-md-10">
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
                                </div>
                            </div>
                        </div>
                        {{ $dataTable->table() }}
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
        // $(document).ready(function() {
        //     $('#users-table').DataTable({
        //         processing: true,
        //         serverSide: true,
        //         ajax: "{{ route('users.data') }}",
        //         column:[]
        //     });
        // });
    </script>
@endsection
@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush

