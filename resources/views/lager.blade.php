@extends('test')

@section('title', 'Price List')

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
        <table id="lager_table" class="table table-striped table-sm" style="width: 100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Overtime</th>
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
                    <span id="lager_result"></span>
                    <form id="lager_form" method="GET">
                        <input type="hidden" name="id" id="id" value="-1">
                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Name"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mt-4">
                                    <div class="form-check mb-2">
                                        <input type="checkbox" class="form-check-input" id="isOvertime" value="0"
                                            name="isOvertime">
                                        <label class="form-check-label" for="isOvertime">Overtime</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="mt-2 mb-3 overtime" style="display: none;" />
                        <div class="row overtime" style="display: none;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="overtime">Overtime Count</label>
                                    <input type="text" class="form-control" id="overtime" name="overtime"
                                        placeholder="Overtime">
                                </div>
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
@section('script')
    <script>
        var token = Cookies.get('token');
        $.ajaxSetup({
            headers: {
                'authorization': "Bearer " + token
            }
        });
        var users_table = $('#lager_table').DataTable({
            sDom: '<"top float-right" Bl> <"clear"><"top" <"test">>rt<"bottom" ip><"clear">',
            dom: 'Bfrtip',
            buttons: {
                buttons: [{
                    className: 'btn-primary',
                    text: 'Add New Lager',
                    action: function(e, dt, node, config) {
                        $('#edit_modal_label').text('Add Lager');
                        $('#id').val(-1);
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
                url: "/api/lagers",
                type: 'get',
            },

            columns: [

                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'isOvertime',
                    name: 'isOvertime'
                },
                {
                    data: 'edit',
                    name: 'edit',
                    orderable: false,
                    searchable: false
                }
            ],

        });
        $("#isOvertime").change(function() {
            if (this.checked) {
                $(".overtime").show();
            } else {
                $(".overtime").hide();
            }
        });
        $(document).on('click', ".edit", function() {
            $(this).addClass('edit-item-trigger-clicked');

            $('#edit_modal_label').text('Edit Lager ');
            $('#edit_modal').modal('show');
        });
        $('#edit_modal').on('show.bs.modal', function() {
            var el = $(".edit-item-trigger-clicked");
            var id = el.data('id');

            if (id) {
                $.ajax({
                    url: "/api/lagers/" + id,
                    type: "GET",
                    headers: {
                        "accept": "application/json",
                        "content-type": "application/json",
                    },
                    success: function(data) {
                        obj = JSON.parse(data);
                        $('#id').val(obj.id);
                        $('#name').val(obj.name);
                        if (obj.isOvertime == 1) {
                            $('#isOvertime').prop("checked", true);
                            $(".overtime").show();
                        }
                        $('#overtime').val(obj.overtime);
                    }
                });
            }

        });
        $('#submitBtn').on('click', function() {

            if ($("#lager_form").valid()) {
                var form = $('#lager_form').serialize();
                $.ajax({
                    url: '/api/lagersaction',
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

@endsection
