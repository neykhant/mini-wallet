@extends('backend.layouts.app');
@section('title', 'Admin Users');
@section('admin-user-active','mm-active');
@section('content')
<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="pe-7s-users icon-gradient bg-mean-fruit">
                </i>
            </div>
            <div>Admin Users</div>
        </div>
    </div>
</div>

<div class="pt-3">
    <a href="{{ route('admin.admin-user.create') }}" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Create Admin User</a>
</div>

<div class="content pt-3">
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered Datatable">
                <thead>
                    <tr class="bg-light">
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.Datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "/admin/admin-user/datatable/ssd",
            columns: [{
                    data: "name",
                    name: "name"
                },
                {
                    data: "email",
                    name: "email"
                },
                {
                    data: "phone",
                    name: "phone"
                },
                {
                    data: "action",
                    name: "action"
                }
            ]

        });
    });
</script>
@endsection