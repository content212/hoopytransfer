@extends('test')

@section('title', 'Online Coupon Codes')

@section('role', $role)

@section('name', $name)

@section('content')
    <div class="table-responsive">
        <table id="couponcodes_table" class="table table-striped table-sm" style="width: 100%">
            <thead>
            <tr>
                <th>#</th>
                <th width="200">Code</th>
                <th width="200">Coupon Code Package</th>
                <th width="200">User</th>
                <th>Credit</th>
                <th>Price</th>
                <th>Created At</th>
                <th>Date of Use</th>
            </tr>
            </thead>
        </table>
    </div>
@endsection
@section('script')

    <script>

        var token = Cookies.get('token');
        $.ajaxSetup({
            headers: {
                'authorization': "Bearer " + token
            }
        });


     $('#couponcodes_table').DataTable({
            pageLength: 50,
            responsive: true,
            processing: true,
            serverSide: true,

            ajax: {
                url: "/api/couponcodes/online",
                type: 'get',
            },
            columns: [
                {
                    data: 'id',
                    name: 'id',
                    visible: false
                },
                {
                    data: 'code',
                    name: 'code'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'username',
                    name: 'username'
                },
                {
                    data: 'credit',
                    name: 'credit'
                },
                {
                    data: 'price',
                    name: 'price'
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    searchable: false
                },
                {
                    data: 'date_of_use',
                    name: 'date_of_use',
                    searchable: false
                },
            ],

        });
    </script>
@endsection
