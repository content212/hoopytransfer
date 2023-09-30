@extends('test')

@section('title', 'Drivers')

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

    <span id="form_result"></span>
    <div class="table-responsive">
        <table id="drivers_table" class="table table-striped table-sm" style="width: 100%">
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
    <div id="confirm_modal" class="modal fade">
        <div class="modal-dialog modal-confirm">
            <div class="modal-content">
                <div class="modal-header flex-column">
                    <div class="icon-box">
                        <i class="material-icons">&#xE5CD;</i>
                    </div>
                    <h4 class="modal-title w-100">Are you sure?</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true"
                            aria-label="Close">&times;
                    </button>
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
    <div class="modal fade" id="edit-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit_modal_label">Edit Drivers</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form role="form" method="POST" action="" id="drivers_form">
                        <input type="hidden" id="driver_id" value="">
                        <div class="box-body">
                            <span id="modal_result"></span>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name" id="name"
                                       placeholder="Name">
                            </div>
                            <div class="form-grop">
                                <label for="surname">Surname</label>
                                <input type="text" class="form-control" name="surname" id="surname"
                                       placeholder="Surname">
                            </div>

                            <div class="form-group" >
                                <label for="email">Email</label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Email">
                            </div>

                            <div class="form-group">
                                <label for="phone">Country Code</label>
                                @include('tel-input',['name' => 'country_code'])
                            </div>

                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" class="form-control" name="phone" id="phone" placeholder="Phone">
                            </div>

                            <div class="form-group">
                                <label for="license_date">License Date</label>
                                <input type="text" class="form-control my_date_picker" name="license_date"
                                       id="license_date"
                                       placeholder="License Date">
                            </div>
                            <div class="form-group">
                                <label for="license_class">License Class</label>
                                <input type="text" class="form-control" name="license_class" id="license_class"
                                       placeholder="License Class">
                            </div>
                            <div class="form-group">
                                <label for="license_no">License No</label>
                                <input type="text" class="form-control" name="license_no" id="license_no"
                                       placeholder="License No">
                            </div>
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                            </div>

                            <hr class="mt-2 mb-3" />
                            <div class="row mb-3">

                                <div class="col-md-6">
                                    <label for="password">New Password</label>
                                    <input type="password" class="form-control" name="password" id="password">
                                </div>

                                <div class="col-md-6">
                                    <label for="password_confirm">Password Again</label>
                                    <input type="password" class="form-control" name="password_confirm" id="password_confirm">
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-default pull-left"
                                        data-bs-dismiss="modal">Close
                                </button>
                                <button type="button" id="save" class="btn btn-primary">Save changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="overlay">
        <div class="cv-spinner">
            <span class="spinner"></span>
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
        $(document).on('click', ".edit", function () {
            $(this).addClass('edit-item-trigger-clicked');

            $('#edit-modal').modal('show');
        });
        $(document).on('click', '.delete', function () {
            $(this).addClass('delete-item-trigger-clicked');

            $('#confirm_modal').modal('show');

        });
        $('.delete-driver').on('click', function (e) {
            var el = $(".delete-item-trigger-clicked");
            var id = el.data('id');
            $("#overlay").fadeIn(300);

            $.ajax({
                url: 'api/drivers/' + id,
                type: "DELETE",
                success: function (data) {
                    $('#confirm_modal').modal('hide');
                    $('#drivers_table').DataTable().ajax.reload();
                },
                error: function (data) {
                }
            }).done(function () {
                setTimeout(function () {
                    $("#overlay").fadeOut(300);
                }, 500);
            }).fail(function () {
                setTimeout(function () {
                    $("#overlay").fadeOut(300);
                }, 500);
            });
        });


        $("#drivers_form").validate({
            rules: {
                name: "required",
                surname: "required",
                email: "required",
                country_code: "required",
                phone: "required",
                license_date: "required",
                license_class: "required",
                license_no: "required",
                address: "required",
                password_confirm: {
                    equalTo: "#password"
                },
            },
        });

        $('#save').on('click', function (e) {
            e.preventDefault();

            if (!$("#drivers_form").valid()) {
                return;
            }

            var form = $('#drivers_form');
            $("#overlay").fadeIn(300);
            $.ajax({
                url: document.getElementById('drivers_form').action,
                type: "POST",
                data: form.serialize(),
                success: function (data) {
                    html = '<div class="alert alert-success">';
                    html += '<p>Save Success</p>'
                    html += '</div>';
                    $('#modal_result').html(html);
                    $('#edit-modal').animate({
                        scrollTop: $("#modal_result").offset().top
                    }, 'slow');
                    $('#drivers_table').DataTable().ajax.reload();

                    if (document.getElementById('driver_id').value === "") {
                        $('#edit-modal').modal('hide');
                    }

                },
                error: function (data) {
                    if (data.responseJSON.message) {
                        html = '<div class="alert alert-danger">';
                        html += '<p>' + data.responseJSON.message + '</p>'
                        html += '</div>';
                        $('#modal_result').html(html);
                    }
                }
            }).done(function () {
                setTimeout(function () {
                    $("#overlay").fadeOut(300);
                }, 500);
            }).fail(function () {
                setTimeout(function () {
                    $("#overlay").fadeOut(300);
                }, 500);
            });
        });
        $('#edit-modal').on('show.bs.modal', function () {
            var el = $(".edit-item-trigger-clicked");
            var row = el.closest(".data-row");

            var id = el.data('id');
            if (id) {
                document.getElementById('drivers_form').action = "/api/drivers/" + id;
                $.ajax({
                    url: "/api/drivers/" + id,
                    type: "GET",
                    headers: {
                        "accept": "application/json",
                        "content-type": "application/json",
                    },
                    success: function (data) {
                        obj = JSON.parse(data);
                        $('#driver_id').val(obj.id);
                        $('#name').val(obj.name);
                        $('#surname').val(obj.surname)
                        $('#email').val(obj.email)
                        $('#phone').val(obj.phone)
                        $('#country_code').val(obj.country_code)
                        $('#car').val(obj.car_id)
                        $('#license_date').val(obj.license_date)
                        $('#license_class').val(obj.license_class)
                        $('#license_no').val(obj.license_no)
                        $('#address').val(obj.address)
                    }
                });
            } else {
                document.getElementById('drivers_form').action = "/api/drivers";
            }

        });

        $('#edit-modal').on('hide.bs.modal', function () {
            $('.edit-item-trigger-clicked').removeClass('edit-item-trigger-clicked')
            $("#drivers_form").trigger("reset");
            $("#driver_id").val("");
            $('#modal_result').empty();
            document.getElementById('drivers_form').action = "";
        });

        $('#drivers_table').DataTable({
            order: [
                [0, "asc"]
            ],
            processing: true,
            serverSide: true,
            responsive: true,
            sDom: '<"top float-right" Bl> <"clear"><"top" <"test">>rt<"bottom" ip><"clear">',
            dom: 'Bfrtip',
            buttons: {
                buttons: [
                    {
                        className: 'btn-primary',
                        text: 'Add New Drivers',
                        action: function (e, dt, node, config) {
                            $('#edit_modal_label').text('Add Drivers');
                            $('#id').val(-1);
                            $('#edit-modal').modal('show');
                        }
                    }
                ],
                dom: {
                    button: {
                        className: 'btn'
                    },
                    buttonLiner: {
                        tag: null
                    }
                }
            },
            ajax: {
                url: "/api/drivers",
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
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'edit',
                    name: 'edit',
                    orderable: false,
                    searchable: false
                }
            ]
        });
    </script>
@endsection
