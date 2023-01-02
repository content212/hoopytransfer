@extends('test')

@section('title', 'Drivers')

@section('role', $role)

@section('name', $name)

@section('css')
@endsection


@section('content')
    <div class="row">
        @if ($role != 'driver')
            <div class="col-lg-2 col-md-6">
                <button type="button" class="btn btn-primary" id="addModalBtn">Add New </button>
            </div>
            <br><br><br>
        @endif
    </div>
    <div class="table-responsive">
        <table id="drivers_table" class="table table-striped table-sm" style="width: 100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Edit</th>
                </tr>
            </thead>
        </table>
    </div>
    <div class="modal top fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="true">
        <div class="modal-dialog  ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add Driver</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="modal_result"></span>
                    <form id="drivers_form">
                        <div class="row ">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" name="name" id="name">
                                </div>
                            </div>

                        </div>
                        <div class="row ">

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" name="email" id="email">
                                </div>
                            </div>

                        </div>
                        <div class="row ">

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" class="form-control" name="phone" id="phone">
                                </div>
                            </div>

                        </div>
                        <div class="row ">

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" name="password" id="password">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" id="addBtn">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal top fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="true">
        <div class="modal-dialog  ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Driver</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="modal_result"></span>
                    <form id="edit_form" method="POST">
                        <input type="hidden" name="id" id="userid">
                        <div class="row ">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" name="name" id="editName">
                                </div>
                            </div>

                        </div>
                        <div class="row ">

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="email">EMail</label>
                                    <input type="email" class="form-control" name="email" id="editEmail">
                                </div>
                            </div>

                        </div>
                        <div class="row ">

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" class="form-control" name="phone" id="editPhone">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" id="driverSaveBtn">Save changes</button>
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
                    <button type="button" class="delete-driver btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('js/jquery.tabledit.js') }}"></script>
    <script>
        var token = Cookies.get('token');
        $.ajaxSetup({
            headers: {
                'authorization': "Bearer " + token
            }
        });

        $('#addModalBtn').on('click', function() {
            $('#addModal').modal('show');
        });

        $('#addBtn').on('click', function() {
            var form = $('#drivers_form');
            $.ajax({
                url: '/api/storedriver',
                type: "POST",
                data: form.serialize(),
                success: function(data) {
                    html = '<div class="alert alert-success">';
                    html += '<p>Save Success</p>'
                    html += '</div>';
                    $('#modal_result').html(html);
                    $('#drivers_table').DataTable().ajax.reload();
                },
                error: function(data) {
                    if (data.responseJSON.message) {
                        html = '<div class="alert alert-danger">';
                        html += '<p>' + data.responseJSON.message + '</p>'
                        html += '</div>';
                        $('#modal_result').html(html);
                    }
                }
            })
        });

        $(document).on('click', ".edit", function() {
            $(this).addClass('edit-item-trigger-clicked');

            $('#editModal').modal('show');
        });
        $('#driverSaveBtn').on('click', function() {
            var formdata = $('#edit_form').serialize() + '&action=edit';
            $.ajax({
                url: document.getElementById('edit_form').action,
                type: "POST",
                data: formdata,
                success: function(data) {
                    $('#drivers_table').DataTable().ajax.reload();
                    $('#editModal').modal('hide');
                },
            });
        });
        $(document).on('click', ".delete", function() {
            $(this).addClass('delete-item-trigger-clicked');

            $('#confirm_modal').modal('show');
        });
        $('.delete-driver').on('click', function(e) {
            var el = $(".delete-item-trigger-clicked");
            var id = el.data('id');
            $("#overlay").fadeIn(300);

            $.ajax({
                url: '/api/driversaction',
                type: "POST",
                data: 'id=' + id + '&action=delete',
                success: function(data) {
                    $('#confirm_modal').modal('hide');
                    $('#drivers_table').DataTable().ajax.reload();
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
        $('#editModal').on('show.bs.modal', function() {
            var el = $(".edit-item-trigger-clicked");
            var id = el.data('id');
            document.getElementById('edit_form').action = "/api/driversaction";

            $.ajax({
                url: "/api/users/" + id,
                type: "GET",
                headers: {
                    "accept": "application/json",
                    "content-type": "application/json"
                },
                success: function(data) {
                    obj = JSON.parse(data);
                    $('#userid').val(obj.id);
                    $('#editName').val(obj.name);
                    $('#editEmail').val(obj.email);
                    $('#editPhone').val(obj.phone);
                }
            });
        });
        $('#editModal').on('hide.bs.modal', function() {
            $('.edit-item-trigger-clicked').removeClass('edit-item-trigger-clicked')
            $("#edit_form").trigger("reset");
            $('#modal_result').empty();
        });
        $('#drivers_table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "/api/getdriverdata",
                type: 'get',
            },

            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'phone',
                    name: 'phone'
                },
                {
                    data: 'edit',
                    name: 'edit'
                }
            ],

        });
    </script>
@endsection
