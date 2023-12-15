@extends('test')

@section('title', 'Gift Cards')

@section('role', $role)

@section('name', $name)

@section('content')
    <div class="table-responsive">
        <table id="couponcodes_table" class="table table-striped table-sm" style="width: 100%">
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Credit</th>
                <th>Validity</th>
                <th>Active</th>
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
                    <span id="couponcodes_result"></span>
                    <form id="couponcode_form" method="GET">
                        <input type="hidden" name="id" id="id" value="-1">
                        <div class="row ">
                            <div class="col-md-3">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name" id="name">
                            </div>
                            <div class="col-md-3">
                                <label for="credit">Credit</label>
                                <input type="number" class="form-control" name="credit" id="credit">
                            </div>
                            <div class="col-md-3">
                                <label for="validity">Validity (Day)</label>
                                <input type="number" class="form-control" name="validity" id="validity">
                            </div>
                            <div class="col-md-3">
                                <label for="active">Active</label>
                                <br>
                                <input type="checkbox" name="active" value="1" id="active">
                            </div>
                        </div>
                        <br/>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Close
                            </button>
                            <button type="button" class="btn btn-primary" id="submitBtn">Save changes</button>
                        </div>
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
                            aria-label="Close">&times;
                    </button>
                </div>
                <div class="modal-body">
                    <p>Do you really want to delete these records? This process cannot be undone.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="delete-couponcode btn btn-danger">Delete</button>
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

        $("#couponcode_form").validate({
            rules: {
                name: "required",
                credit: "required",
                validity: "required",
            },
        });

        var couponcodes_table = $('#couponcodes_table').DataTable({
            sDom: '<"top float-right" Bl> <"clear"><"top" <"test">>rt<"bottom" ip><"clear">',
            dom: 'Bfrtip',
            buttons: {
                buttons: [{
                    className: 'btn-primary',
                    text: 'Add New couponcode',
                    action: function (e, dt, node, config) {
                        $('#edit_modal_label').text('Add couponcode');
                        $('#id').val(-1);
                        $("#name").rules("add", "required");
                        $("#credit").rules("add", "required");
                        $("#validity").rules("add", "required");
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

            ajax: {
                url: "/api/couponcodes",
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
                    data: 'credit',
                    name: 'credit'
                },
                {
                    data: 'validity',
                    name: 'validity'
                },
                {
                    data: 'active',
                    name: 'active',
                    render: function (data, type, row) {
                        return data === "1" ? "Yes" : "No";
                    }
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
        $(document).on('click', ".edit", function () {
            $(this).addClass('edit-item-trigger-clicked');
            $('#edit_modal_label').text('Edit couponcode');
            $('#edit_modal').modal('show');
        });
        $('#edit_modal').on('show.bs.modal', function () {
            var el = $(".edit-item-trigger-clicked");
            var id = el.data('id');
            if (id) {
                $.ajax({
                    url: "/api/couponcodes/" + id,
                    type: "GET",
                    headers: {
                        "accept": "application/json",
                        "content-type": "application/json",
                    },
                    success: function (data) {

                        obj = JSON.parse(data);
                        $('#id').val(obj.id);
                        $('#name').val(obj.name)
                        $('#credit').val(obj.credit)
                        $('#validity').val(obj.validity)

                        if (obj.active) {
                            $("#active").prop("checked", true);
                        }

                        $("#name").rules("add", "required");
                        $("#credit").rules("add", "required");
                        $("#validity").rules("add", "required");

                    }
                });
            }
        });


        $('#submitBtn').on('click', function () {
            if ($("#couponcode_form").valid()) {
                var form = $('#couponcode_form').serialize();
                $.ajax({
                    url: '/api/couponcodes',
                    type: 'POST',
                    data: form,
                    success: function (data) {
                        $('#edit_modal').modal('hide');
                        couponcodes_table.ajax.reload();
                    },
                    error: function (data) {
                        if (data.responseJSON.message) {
                            var errors = $.parseJSON(data.responseText);
                            html = '<div class="alert alert-danger">';
                            $.each(data.responseJSON.message, function (key, value) {
                                html += '<p>' + value[0] + '</p>';
                            });
                            html += '</div>';
                            $('#couponcodes_result').html(html);
                        }
                        couponcodes_table.ajax.reload();
                    }
                })
            }
        });

        $('#edit_modal').on('hide.bs.modal', function () {
            $('.edit-item-trigger-clicked').removeClass('edit-item-trigger-clicked')
            $("#couponcode_form").trigger("reset");
            $('#couponcodes_result').empty();
        });

        $(document).on('click', '.delete', function () {
            $(this).addClass('delete-item-trigger-clicked');
            $('#confirm_modal').modal('show');
        });

        $('.delete-couponcode').on('click', function (e) {
            var el = $(".delete-item-trigger-clicked");
            var id = el.data('id');
            $("#overlay").fadeIn(300);

            $.ajax({
                url: 'api/couponcodes/' + id,
                type: "DELETE",
                success: function (data) {
                    $('#confirm_modal').modal('hide');
                    couponcodes_table.ajax.reload();
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
    </script>
@endsection
