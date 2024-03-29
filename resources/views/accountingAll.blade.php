@extends('test', ['accounting' => '1'])

@if (isset($driverName))
    @section('title', 'Accounting - ' . $driverName)
@else
    @section('title', 'Accounting')
@endif

@section('role', $role)

@section('name', $name)

@section('css')
    <style>
        .trigger-btn {
            display: inline-block;
            margin: 100px auto;
        }
    </style>
@endsection

@section('content')
    @if (isset($id))
        @if ($id == -1)
            @livewire('accounting-driver-table', ['driver_id' => $id, 'isDetail' => true, 'role' => $role])
        @else
            @livewire('accounting-driver-table', ['driver_id' => $id, 'role' => $role])
        @endif
    @else
        @livewire('accounting-table')
    @endif
@endsection
@section('script')
    <script>
        var token = Cookies.get('token');
        $.ajaxSetup({
            headers: {
                'authorization': "Bearer " + token
            }
        });
    </script>
@endsection
