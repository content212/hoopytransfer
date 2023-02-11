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

    <span id="form_result"></span>
    <div class="table-responsive">
        <table id="prices_table" class="table table-striped table-sm" style="width: 100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Type</th>
                    <th>Start Km</th>
                    <th>Finish Km</th>
                    <th>Opening Fee</th>
                    <th>Km Fee</th>
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
                    <button type="button" class="delete-price btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="edit-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit_modal_label">Edit Prices</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form role="form" method="POST" action="" id="prices_form">
                        <div class="box-body">
                            <span id="modal_result"></span>
                            <div class="form-group">
                                <label for="car_type">Car Type</label>
                                {{ Form::select('car_type', $car_types, null, array('class' => 'form-control', 'id' => 'car_type')) }}
                            </div>
                            <div class="form-group">
                                <label for="start_km">Start Km</label>
                                <input type="text" class="form-control" name="start_km" id="start_km"
                                    placeholder="Start Km">
                            </div>
                            <div class="form-group">
                                <label for="finish_km">Finish Km</label>
                                <input type="text" class="form-control" name="finish_km" id="finish_km"
                                    placeholder="Finish Km">
                            </div>
                            <div class="form-group">
                                <label for="opening_fee">Opening Fee</label>
                                <input type="text" class="form-control" name="opening_fee" id="opening_fee"
                                    placeholder="Opening Fee">
                            </div>
                            <div class="form-group">
                                <label for="km_fee">Km Fee</label>
                                <input type="text" class="form-control" name="km_fee" id="km_fee"
                                    placeholder="Km Fee">
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
        $('.delete-price').on('click', function(e) {
            var el = $(".delete-item-trigger-clicked");
            var id = el.data('id');
            $("#overlay").fadeIn(300);

            $.ajax({
                url: 'api/prices/' + id,
                type: "DELETE",
                success: function(data) {
                    $('#confirm_modal').modal('hide');
                    $('#prices_table').DataTable().ajax.reload();
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

            var form = $('#prices_form');
            $("#overlay").fadeIn(300);
            $.ajax({
                url: document.getElementById('prices_form').action,
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
                    $('#prices_table').DataTable().ajax.reload();
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
                document.getElementById('prices_form').action = "/api/prices/" + id;
                $.ajax({
                    url: "/api/prices/" + id,
                    type: "GET",
                    headers: {
                        "accept": "application/json",
                        "content-type": "application/json",
                    },
                    success: function(data) {
                        obj = JSON.parse(data);
                        $('#car_type').val(obj.car_type);
                        $('#start_km').val(obj.start_km)
                        $('#finish_km').val(obj.finish_km)
                        $('#opening_fee').val(obj.opening_fee)
                        $('#km_fee').val(obj.km_fee)
                    }
                });
            }
            else{
                document.getElementById('prices_form').action = "/api/prices";
            }
            
        });

        $('#edit-modal').on('hide.bs.modal', function() {
            $('.edit-item-trigger-clicked').removeClass('edit-item-trigger-clicked')
            $("#prices_form").trigger("reset");
            $('#modal_result').empty();
        });

        $('#prices_table').DataTable({
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
                    text: 'Add New Prices',
                    action: function(e, dt, node, config) {
                        $('#edit_modal_label').text('Add Prices');
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
                url: "/api/prices",
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
                    data: 'start_km',
                    name: 'start_km'
                },
                {
                    data: 'finish_km',
                    name: 'finish_km',
                },
                {
                    data: 'opening_fee',
                    name: 'opening_fee',
                },
                {
                    data: 'km_fee',
                    name: 'km_fee',
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
