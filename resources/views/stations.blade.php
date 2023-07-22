@extends('test')

@section('title', 'Stations')

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
        <table id="stations_table" class="table table-striped table-sm" style="width: 100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Official Name</th>
                    <th>Official Phone</th>
                    <th>Country</th>
                    <th>State</th>
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
                    <button type="button" class="delete-station btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="edit-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit_modal_label">Edit Stations</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form role="form" method="POST" action="" id="stations_form">
                        <div class="box-body">
                            <span id="modal_result"></span>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name" id="name"
                                    placeholder="Name">
                            </div>
                            <div class="form-group">
                                <label for="official_name">Official Name</label>
                                <input type="text" class="form-control" name="official_name" id="official_name"
                                    placeholder="Official Name">
                            </div>
                            <div class="form-group">
                                <label for="official_phone">Official Phone</label>
                                <input type="text" class="form-control" name="official_phone" id="official_phone"
                                    placeholder="Official Phone">
                            </div>
                            
                            <div class="form-group">
                                @livewire('dropdowns') 
                            </div>
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea type="text" class="form-control" name="address" id="address" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="latitude">Latitude</label>
                                <input type="text" class="form-control" name="latitude" id="latitude"
                                    placeholder="Latitude">
                            </div>
                            <div class="form-group">
                                <label for="longitude">Longitude</label>
                                <input type="text" class="form-control" name="longitude" id="longitude"
                                    placeholder="Longitude">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default pull-left"
                                    data-bs-dismiss="modal">Close</button>
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
        $(document).on('click', ".edit", function() {
            $(this).addClass('edit-item-trigger-clicked');

            $('#edit-modal').modal('show');
        });
        $(document).on('click', '.delete', function() {
            $(this).addClass('delete-item-trigger-clicked');

            $('#confirm_modal').modal('show');

        });
        $('.delete-station').on('click', function(e) {
            var el = $(".delete-item-trigger-clicked");
            var id = el.data('id');
            $("#overlay").fadeIn(300);

            $.ajax({
                url: 'api/stations/' + id,
                type: "DELETE",
                success: function(data) {
                    $('#confirm_modal').modal('hide');
                    $('#stations_table').DataTable().ajax.reload();
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


        $("#stations_form").validate({
			rules: {
				name: "required",
                official_name: "required",
                official_phone: "required",
                country: "required",
                state: "required",
                address: "required",
                latitude: "required",
                longitude: "required"
			},
		});

    

        $('#save').on('click', function(e) {
            e.preventDefault();

            if (!$("#stations_form").valid()) {
                return;
            }


            var form = $('#stations_form');
            $("#overlay").fadeIn(300);
            $.ajax({
                url: document.getElementById('stations_form').action,
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
                    $('#stations_table').DataTable().ajax.reload();
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
                document.getElementById('stations_form').action = "/api/stations/" + id;
                $.ajax({
                    url: "/api/stations/" + id,
                    type: "GET",
                    headers: {
                        "accept": "application/json",
                        "content-type": "application/json",
                    },
                    success: function(data) {
                        obj = JSON.parse(data);
                        $('#name').val(obj.name);
                        $('#official_name').val(obj.official_name)
                        $('#official_phone').val(obj.official_phone)
                        $('#country').val(obj.country).trigger('click')
                        setTimeout(function() { 
                            $('#state').val(obj.state)
                        }, 200);
                        
                        $('#address').val(obj.address)
                        $('#latitude').val(obj.latitude)
                        $('#longitude').val(obj.longitude)
                    }
                });
            }
            else{
                document.getElementById('stations_form').action = "/api/stations";
            }
            
        });

        $('#edit-modal').on('hide.bs.modal', function() {
            $('.edit-item-trigger-clicked').removeClass('edit-item-trigger-clicked')
            $("#stations_form").trigger("reset");
            $('#modal_result').empty();
        });

        $('#stations_table').DataTable({
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
                    text: 'Add New Stations',
                    action: function(e, dt, node, config) {
                        $('#edit_modal_label').text('Add Stations');
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
                url: "/api/stations",
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
                    data: 'official_name',
                    name: 'official_name'
                },
                {
                    data: 'official_phone',
                    name: 'official_phone',
                },
                {
                    data: 'country',
                    name: 'country',
                },
                {
                    name: 'state',
                    data: 'state',
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
