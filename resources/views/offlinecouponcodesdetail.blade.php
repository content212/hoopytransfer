@extends('test')

@section('title', 'Coupon Code Groups Details')

@section('role', $role)

@section('name', $name)

@section('content')
    <div class="table-responsive">
        <table id="offlinecouponcodesdetail_table" class="table table-striped table-sm" style="width: 100%">
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
        const token = Cookies.get('token');
        $.ajaxSetup({
            headers: {
                'authorization': "Bearer " + token
            }
        });

        $('#offlinecouponcodesdetail_table').DataTable({
            sDom: '<"top float-right" Bl> <"clear"><"top" <"test">>rt<"bottom" ip><"clear">',
            dom: 'Bfrtip',
            buttons: {
                buttons: [{
                    className: 'btn-primary',
                    text: 'Excel Export',
                    action: function (e, dt, node, config) {
                        window.location.href="/couponcodes/offline/export/{{$id}}";
                    }
                }],
                dom: {
                    button: {
                        className: 'btn'
                    },
                    buttonLiner: {
                        tag: null
                    }
                }
            },
            pageLength: 50,
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "/api/couponcodes/groups/{{$id}}",
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
