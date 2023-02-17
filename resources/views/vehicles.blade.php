@extends('test')

@section('title', 'Vehicles')

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
        <table id="cars_table" class="table table-striped table-sm nowrap" style="width: 100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Plate</th>
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
                    <h5 class="modal-title" id="edit_modal_label">Edit Vehicles</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form role="form" method="POST" action="" id="cars_form">
                        <div class="box-body">
                            <span id="modal_result"></span>
                            <div class="form-group">
                                <label for="plate">Plate</label>
                                <input type="text" class="form-control" name="plate" id="plate"
                                    placeholder="Plate">
                            </div>
                            <div class="form-group">
                                <label for="type">Car Type</label>
                                {{ Form::select('type', [null=>'Please Select'] + $car_types->toarray(), null, array('class' => 'form-control', 'id' => 'type')) }}
                            </div>
                            <div class="form-group">
                                <label for="insurance_date">Insurance Date</label>
                                <input type="text" class="form-control" name="insurance_date" id="insurance_date"
                                    placeholder="Insurance Date">
                            </div>
                            <div class="form-group">
                                <label for="inspection_date">Inspection Date</label>
                                <input type="text" class="form-control" name="inspection_date" id="inspection_date"
                                    placeholder="Inspection Date">
                            </div>
                            <div class="form-group">
                                <label for="station_id">Station</label>
                                {{ Form::select('station_id', $stations, null, array('class' => 'form-control', 'id' => 'station_id')) }}
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
                        $('#plate').val(obj.plate);
                        $('#type').val(obj.type)
                        $('#person_capacity').val(obj.person_capacity)
                        $('#baggage_capacity').val(obj.baggage_capacity)
                        $('#insurance_date').val(obj.insurance_date)
                        $('#inspection_date').val(obj.inspection_date)
                        $('#station_id').val(obj.station_id)
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
                    text: 'Add New Vehicles',
                    action: function(e, dt, node, config) {
                        $('#edit_modal_label').text('Add Vehicles');
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
                    data: 'plate',
                    name: 'plate'
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'person_capacity',
                    name: 'person_capacity',
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
