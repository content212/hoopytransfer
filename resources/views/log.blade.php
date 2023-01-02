@extends('test')

@section('title', 'Logs')

@section('role', $role)

@section('name', $name)

@section('css')
    <style>
        .popover {
            z-index: 999999;
        }

    </style>
@endsection

@section('content')
    <div class="table-responsive">
        <table id="logs_table" class="table table-striped table-sm" style="width: 100%">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Type</th>
                    <th>URL</th>
                    <th>IP</th>
                    <th>User</th>
                    <th>Date</th>
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
        var logs_table = $('#logs_table').DataTable({
            order: [
                [5, "asc"]
            ],
            pageLength: 50,
            responsive: true,
            processing: true,
            serverSide: true,

            ajax: {
                url: "/api/logs",
                type: 'get',
            },

            columns: [

                {
                    data: 'subject',
                    name: 'logs.subject'
                },
                {
                    data: 'query_type',
                    name: 'logs.query_type'
                },

                {
                    data: 'url',
                    name: 'logs.url'
                },
                {
                    data: 'ip',
                    name: 'logs.ip'
                },
                {
                    data: 'name',
                    name: 'users.name'
                },
                {
                    data: 'created_at',
                    name: 'logs.created_at'
                }
            ],

        });
    </script>
@endsection
