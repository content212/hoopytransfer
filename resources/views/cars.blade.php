@extends('test')

@section('title', 'Cars')

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
        <table id="cars_table" class="table table-striped table-sm" style="width: 100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Type</th>
                    <th>Person Capacity</th>
                    <th>Baggage Capacity</th>
                    <th>Edit/Delete</th>
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
                        aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Do you really want to delete these records? This process cannot be undone.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="delete-car btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="edit-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit_modal_label">Edit Car</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form role="form" method="POST" action="" id="cars_form">
                        <div class="box-body">
                            <span id="modal_result"></span>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Name">
                            </div>
                            <div class="form-group">
                                <label for="type">Type</label>
                                <select class="form-select" name="type" id="type">
                                    <option value="Sedan">Sedan</option>
                                    <option value="VIP">VIP</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="person_capacity">Person Capacity</label>
                                <input type="text" class="form-control" name="person_capacity" id="person_capacity"
                                    placeholder="Person Capacity">
                            </div>
                            <div class="form-group">
                                <label for="baggage_capacity">Baggage Capacity</label>
                                <input type="text" class="form-control" name="baggage_capacity" id="baggage_capacity"
                                    placeholder="Baggage Capacity">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default pull-left"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="button" id="save" class="btn btn-primary">Save changes</button>
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
        $(document).on('click', ".edit", function() {
            $(this).addClass('edit-item-trigger-clicked');

            $('#edit-modal').modal('show');
        });
        $(document).on('click', '.delete', function() {
            $(this).addClass('delete-item-trigger-clicked');

            $('#confirm_modal').modal('show');

        });
        $('.delete-car').on('click', function(e) {
            var el = $(".delete-item-trigger-clicked");
            var id = el.data('id');
            $("#overlay").fadeIn(300);

            $.ajax({
                url: 'api/cars/' + id,
                type: "DELETE",
                success: function(data) {
                    $('#confirm_modal').modal('hide');
                    $('#cars_table').DataTable().ajax.reload();
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
        $('#save').on('click', function(e) {
            e.preventDefault();

            var form = $('#cars_form');
            $("#overlay").fadeIn(300);
            $.ajax({
                url: document.getElementById('cars_form').action,
                type: "POST",
                data: form.serialize(),
                success: function(data) {
                    html = '<div class="alert alert-success">';
                    html += '<p>Save Success</p>'
                    html += '</div>';
                    $('#modal_result').html(html);
                    $('#edit-modal').animate({
                        scrollTop: $("#modal_result").offset().top
                    }, 'slow');
                    $('#cars_table').DataTable().ajax.reload();
                },
                error: function(data) {
                    if (data.responseJSON.message) {
                        html = '<div class="alert alert-danger">';
                        html += '<p>' + data.responseJSON.message + '</p>'
                        html += '</div>';
                        $('#modal_result').html(html);
                    }
                }
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
        $('#edit-modal').on('show.bs.modal', function() {
            var el = $(".edit-item-trigger-clicked");
            var row = el.closest(".data-row");

            var id = el.data('id');
            if(id)
            {
                document.getElementById('cars_form').action = "/api/cars/" + id;
                $.ajax({
                    url: "/api/cars/" + id,
                    type: "GET",
                    headers: {
                        "accept": "application/json",
                        "content-type": "application/json",
                    },
                    success: function(data) {
                        obj = JSON.parse(data);
                        $('#name').val(obj.name);
                        $('#type').val(obj.type)
                        $('#person_capacity').val(obj.person_capacity)
                        $('#baggage_capacity').val(obj.baggage_capacity)
                    }
                });
            }
            else{
                document.getElementById('cars_form').action = "/api/cars";
            }
            
        });

        $('#edit-modal').on('hide.bs.modal', function() {
            $('.edit-item-trigger-clicked').removeClass('edit-item-trigger-clicked')
            $("#cars_form").trigger("reset");
            $('#modal_result').empty();
        });

        $('#cars_table').DataTable({
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
                    text: 'Add New Car',
                    action: function(e, dt, node, config) {
                        $('#edit_modal_label').text('Add Car');
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
                url: "/api/cars",
                type: 'get',
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'person_capacity',
                    name: 'person_capacity'
                },
                {
                    data: 'baggage_capacity',
                    name: 'baggage_capacity',
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
