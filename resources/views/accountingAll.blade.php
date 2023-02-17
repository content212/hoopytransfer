@extends('test')

@section('title', 'Prices')

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
