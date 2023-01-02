@extends('test')

@section('title', 'Price List')

@section('role', $role)

@section('name', $name)

@section('css')
    <style>
        .popover {
            z-index: 999999;
        }

        div.dt-buttons {
            margin-right: 1em;
        }

    </style>
@endsection

@section('content')
    <div class="table-responsive">
        <table id="pricelist_table" class="table table-striped table-sm" style="width: 100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Active</th>
                    <th>Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
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

                            <div class="col-lg-5 col-md-6">
                                <label for="Company">Company</label>
                                <select class="form-select" name="company_id" id="company_id">
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <hr class="mt-2 mb-3" />
                        <div class="row">
                            <div class="col-md-2 text-center">
                                X
                            </div>
                            <div class="col-lg-3 col-md-6 text-center">
                                Weekday
                            </div>
                            <div class="col-lg-3 col-md-6 text-center">
                                Saturday
                            </div>
                            <div class="col-lg-3 col-md-6 text-center">
                                Sunday
                            </div>

                        </div>
                        <hr class="mt-2 mb-3" />
                        <div class="row ">

                            <div class="col-md-2">
                                06:00-18:00
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <input type="text" class="form-control price" name="1_weekday" id="1_weekday">
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <input type="text" class="form-control price" name="1_saturday" id="1_saturday">
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <input type="text" class="form-control price" name="1_sunday" id="1_sunday">
                            </div>

                        </div>
                        <hr class="mt-2 mb-3" />
                        <div class="row ">

                            <div class="col-md-2">
                                18:00-22:00
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <input type="text" class="form-control price" name="2_weekday" id="2_weekday">
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <input type="text" class="form-control price" name="2_saturday" id="2_saturday">
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <input type="text" class="form-control price" name="2_sunday" id="2_sunday">
                            </div>

                        </div>
                        <hr class="mt-2 mb-3" />
                        <div class="row ">

                            <div class="col-md-2">
                                22:00-00:00
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <input type="text" class="form-control price" name="3_weekday" id="3_weekday">
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <input type="text" class="form-control price" name="3_saturday" id="3_saturday">
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <input type="text" class="form-control price" name="3_sunday" id="3_sunday">
                            </div>

                        </div>
                        <hr class="mt-2 mb-3" />
                        <div class="row ">

                            <div class="col-md-2">
                                00:00-06:00
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <input type="text" class="form-control price" name="4_weekday" id="4_weekday">
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <input type="text" class="form-control price" name="4_saturday" id="4_saturday">
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <input type="text" class="form-control price" name="4_sunday" id="4_sunday">
                            </div>

                        </div>
                        <hr class="mt-2 mb-3" />
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
    <div class="modal top fade" id="corp_modal" tabindex="-1" aria-labelledby="corp_modal_label" aria-hidden="true"
        data-bs-backdrop="true" data-bs-keyboard="true">
        <div class="modal-dialog modal-lg ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="corp_modal_label">Add Corp</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="corp_result"></span>
                    <form id="corp_form" method="GET">
                        <div class="row ">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="track_code">Name</label>
                                    <input type="text" class="form-control" name="name" placeholder="Name">
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" id="addBtn">Add</button>
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
        var users_table = $('#pricelist_table').DataTable({
            sDom: '<"top float-right" Bl> <"clear"><"top" <"test">>rt<"bottom" ip><"clear">',
            dom: 'B<"toolbar">frtip',
            buttons: {
                buttons: [{
                    className: 'btn-primary',
                    text: 'Add New Price',
                    action: function(e, dt, node, config) {
                        $('#edit_modal_label').text('Add Price');
                        $('#id').val(-1);
                        $('#edit_modal').modal('show');
                    }
                }, {
                    className: 'btn-warning',
                    text: 'Add New Corp',
                    action: function(e, dt, node, config) {
                        $('#id').val(-1);
                        $('#corp_modal').modal('show');
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
                url: "/api/pricelist",
                type: 'get',
            },

            columns: [

                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'active',
                    name: 'active'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'start_date',
                    name: 'start_date'
                },
                {
                    data: 'end_date',
                    name: 'end_date'
                },
                {
                    data: 'edit',
                    name: 'edit',
                    orderable: false,
                    searchable: false
                }
            ],

        });
        $("div.toolbar").html(
            '<div class="ml-2"><label class="form-label" for="archive">Show Archive</label><br><input type="checkbox" id="archive" data-switch="bool" /><label for="archive" data-on-label="" data-off-label=""></label></div>'
        );
        $('#archive').on('change', function(e, data) {
            if (e.target.checked) {
                users_table.ajax.url("/api/pricelist?archive=1");
                users_table.ajax.reload();
            } else {
                users_table.ajax.url("/api/pricelist");
                users_table.ajax.reload();
            }
        });
        $(document).on('input', '.price', function() {
            $(this).val($(this).val().replace(/,/g, '.'));
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
                    url: "/api/pricelist/" + id,
                    type: "GET",
                    headers: {
                        "accept": "application/json",
                        "content-type": "application/json",
                    },
                    success: function(data) {
                        obj = JSON.parse(data);
                        $('#id').val(obj.id);
                        $('#company_id').val(obj.company_id);

                        $('#1_weekday').val(obj['1_weekday']);
                        $('#2_weekday').val(obj['2_weekday']);
                        $('#3_weekday').val(obj['3_weekday']);
                        $('#4_weekday').val(obj['4_weekday']);

                        $('#1_saturday').val(obj['1_saturday']);
                        $('#2_saturday').val(obj['2_saturday']);
                        $('#3_saturday').val(obj['3_saturday']);
                        $('#4_saturday').val(obj['4_saturday']);

                        $('#1_sunday').val(obj['1_sunday']);
                        $('#2_sunday').val(obj['2_sunday']);
                        $('#3_sunday').val(obj['3_sunday']);
                        $('#4_sunday').val(obj['4_sunday']);


                    }
                });
            }

        });
        $('#addBtn').on('click', function() {

            var form = $('#corp_form').serialize();
            $.ajax({
                url: '/api/addcorp',
                type: 'POST',
                data: form,
                success: function(data) {
                    $('#corp_modal').modal('hide');
                    location.reload();
                },
                error: function(data) {
                    if (data.responseJSON.message) {

                        var errors = $.parseJSON(data.responseText);
                        html = '<div class="alert alert-danger">';
                        $.each(data.responseJSON.message, function(key, value) {
                            html += '<p>' + value[0] + '</p>';
                        });
                        html += '</div>';
                        $('#corp_result').html(html);
                    }
                    users_table.ajax.reload();
                }
            })
        });

        $('#submitBtn').on('click', function() {

            if ($("#user_form").valid()) {
                var form = $('#user_form').serialize();
                $.ajax({
                    url: '/api/pricelistaction',
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
