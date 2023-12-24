@extends('test')

@section('title', 'Coupon Code Groups')

@section('role', $role)

@section('name', $name)

@section('content')
    <div class="table-responsive">
        <table id="offlinecouponcodes_table" class="table table-striped table-sm" style="width: 100%">
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Quantity</th>
                <th>Prefix</th>
                <th>Character Length</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
            </thead>
        </table>
    </div>

@endsection
@section('script')

    <script>
        const token = Cookies.get('token');
        $.ajaxSetup({
            headers: {
                'authorization': "Bearer " + token
            }
        });

        $('#offlinecouponcodes_table').DataTable({
            pageLength: 50,
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "/api/couponcodes/groups",
                type: 'get',
            },
            columns: [

                {
                    data: 'id',
                    name: 'id',
                    visible: false
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'quantity',
                    name: 'quantity'
                },
                {
                    data: 'prefix',
                    name: 'prefix'
                },
                {
                    data: 'character_length',
                    name: 'character_length'
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    searchable: false
                },
                {
                    data: 'edit',
                    name: 'edit',
                    orderable: false,
                    searchable: false
                }
            ],

        });


    </script>
@endsection
