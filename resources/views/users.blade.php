@extends('test')

@section('title', 'Users')

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
        <table id="users_table" class="table table-striped table-sm" style="width: 100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Status</th>
                    <th>Name</th>
                    <th>Surname</th>
                    <th>Email</th>
                    <th>Country Code</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Edit</th>
                </tr>
            </thead>
        </table>
    </div>
    <div class="modal top fade" id="edit_modal" tabindex="-1" aria-labelledby="edit_modal_label" aria-hidden="true"
        data-bs-backdrop="true" data-bs-keyboard="true">
        <div class="modal-dialog modal-lg ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit_modal_label">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="users_result"></span>
                    <form id="user_form" method="GET">
                        <input type="hidden" name="id" id="id" value="-1">
                        <div class="row">

                            <div class="col-lg-3 col-md-3">
                                <label for="status">Type</label>
                                <select class="form-select" name="status" id="statuss">
                                    <option value="0">Passive</option>
                                    <option value="1">Active</option>
                                </select>
                            </div>

                            <div class="col-lg-3 col-md-3">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name" id="name">
                            </div>
                            <div class="col-lg-3 col-md-3">
                                <label for="name">Surname</label>
                                <input type="text" class="form-control" name="surname" id="surname">
                            </div>
                            <div class="col-lg-3 col-md-3">
                                <label for="role">Type</label>
                                <select class="form-select" name="role" id="role">
                                    <option value="admin">Admin</option>
                                    <option value="driver">Driver</option>
                                    <option value="driver_manager">Driver Manager</option>
                                    <option value="editor">Editor</option>
                                </select>
                            </div>
                        </div>
                        <hr class="mt-2 mb-3" />
                        <div class="row ">

                            <div class="col-md-4">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" name="email" id="email">
                            </div>

                            <div class="col-md-4">
                                <label for="country_code">Country Code</label>
                                @include('tel-input',['name' => 'country_code'])
                            </div>

                            <div class="col-md-4">
                                <label for="phone">Phone</label>
                                <input type="text" class="form-control" name="phone" id="phone">
                            </div>

                        </div>
                        <hr class="mt-2 mb-3" />
                        <div class="row ">

                            <div class="col-md-6">
                                <label for="password">New Password</label>
                                <input type="password" class="form-control" name="password" id="password">
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirm">Password Again</label>
                                <input type="password" class="form-control" name="password_confirm">
                            </div>

                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" id="submitBtn">Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="confirm_modal" class="modal fade">
        <div class="modal-dialog modal-confirm">
            <div class="modal-content">
                <div class="modal-header flex-column">
                    <div class="icon-box">
                        <i class="material-icons">&#xE5CD;</i>
                    </div>
                    <h4 class="modal-title w-100">Are you sure?</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true"
                        aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Do you really want to delete these records? This process cannot be undone.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="delete-user btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
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
        $('#user_form').validate({
            rules: {
                password_confirm: {
                    equalTo: "#password"
                }
            }
        });
        var users_table = $('#users_table').DataTable({
            sDom: '<"top float-right" Bl> <"clear"><"top" <"test">>rt<"bottom" ip><"clear">',
            dom: 'Bfrtip',
            buttons: {
                buttons: [{
                    className: 'btn-primary',
                    text: 'Add New User',
                    action: function(e, dt, node, config) {
                        $('#edit_modal_label').text('Add User');
                        $('#id').val(-1);
                        $("#password").rules("add", "required");
                        $("#name").rules("add", "required");
                        $("#surname").rules("add", "required");
                        $("#phone").rules("add", "required");
                        $("#country_code").rules("add", "required");
                        $('#edit_modal').modal('show');
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
            order: [
                [8, "desc"]
            ],
            ajax: {
                url: "/api/users",
                type: 'get',
            },
            columns: [
                {
                    data: 'id',
                    name: 'id',
                    visible: false
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'surname',
                    name: 'surname'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'country_code',
                    name: 'country_code'
                },
                {
                    data: 'phone',
                    name: 'phone'
                },
                {
                    data: 'role',
                    name: 'role',
                    searchable: false
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
        $(document).on('click', ".edit", function() {
            $(this).addClass('edit-item-trigger-clicked');

            $('#edit_modal_label').text('Edit User');
            $('#edit_modal').modal('show');
        });
        $('#edit_modal').on('show.bs.modal', function() {
            var el = $(".edit-item-trigger-clicked");
            var id = el.data('id');

            if (id) {
                $.ajax({
                    url: "/api/users/" + id,
                    type: "GET",
                    headers: {
                        "accept": "application/json",
                        "content-type": "application/json",
                    },
                    success: function(data) {
                        obj = JSON.parse(data);
                        $('#id').val(obj.id);
                        $('#statuss').val(obj.status);
                        $('#name').val(obj.name)
                        $('#surname').val(obj.surname)
                        $('#phone').val(obj.phone)
                        $('#country_code').val(obj.country_code)
                        $('#email').val(obj.email)
                        $('#role').val(obj.role)
                    }
                });
            }
        });


        $('#submitBtn').on('click', function() {

            if ($("#user_form").valid()) {
                var form = $('#user_form').serialize();
                $.ajax({
                    url: '/api/usersaction',
                    type: 'POST',
                    data: form,
                    success: function(data) {
                        $('#edit_modal').modal('hide');
                        users_table.ajax.reload();
                    },
                    error: function(data) {
                        if (data.responseJSON.message) {

                            var errors = $.parseJSON(data.responseText);
                            html = '<div class="alert alert-danger">';
                            $.each(data.responseJSON.message, function(key, value) {
                                html += '<p>' + value[0] + '</p>';
                            });
                            html += '</div>';
                            $('#users_result').html(html);
                        }
                        users_table.ajax.reload();
                    }
                })
            }
        });
        $('#edit_modal').on('hide.bs.modal', function() {
            $('.edit-item-trigger-clicked').removeClass('edit-item-trigger-clicked')
            $("#user_form").trigger("reset");
            $('#users_result').empty();
        });
        $(document).on('click', '.delete', function() {
            $(this).addClass('delete-item-trigger-clicked');

            $('#confirm_modal').modal('show');

        });
        $('.delete-user').on('click', function(e) {
            var el = $(".delete-item-trigger-clicked");
            var id = el.data('id');
            $("#overlay").fadeIn(300);

            $.ajax({
                url: 'api/users/' + id,
                type: "DELETE",
                success: function(data) {
                    $('#confirm_modal').modal('hide');
                    users_table.ajax.reload();
                },
                error: function(data) {}
            }).done(function() {
                setTimeout(function() {
                    $("#overlay").fadeOut(300);
                }, 500);
            }).fail(function() {
                setTimeout(function() {
                    $("#overlay").fadeOut(300);
                }, 500);
            });
        });
    </script>
@endsection
