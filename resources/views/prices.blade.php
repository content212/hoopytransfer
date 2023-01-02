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
                    <th>Area</th>
                    <th>Zip Code</th>
                    <th>Km Price</th>
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
    <div id="all_confirm_modal" class="modal fade">
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
                    <button type="button" class="delete-all btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Load From Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            Upload Validation Error<br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-block">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>{{ $message }}</strong>
                        </div>
                    @endif
                    <form method="get" enctype="multipart/form-data" id="upload_form">
                        <div class="form-group">
                            <table class="table">
                                <tr>
                                    <td width="30">
                                        <input type="file" name="select_file" />
                                    </td>
                                    <td width="30%" align="left">
                                        <input type="button" name="upload" id="upload" class="btn btn-primary"
                                            value="Upload">
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="edit-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Price</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form role="form" method="POST" action="" id="prices_form">
                        <div class="box-body">
                            <span id="modal_result"></span>
                            <div class="form-group">
                                <label for="area">Area</label>
                                <input type="text" class="form-control" name="area" id="area" placeholder="Area">
                            </div>
                            <div class="form-group">
                                <label for="zip_code">Zip Code</label>
                                <input type="text" class="form-control" name="zip_code" id="zip_code"
                                    placeholder="Zip Code">
                            </div>
                            <div class="form-group">
                                <label for="bp_km_price">Box/Packet KM Price</label>
                                <input type="text" class="form-control" name="bp_km_price" id="bp_km_price"
                                    placeholder="Box/Packet KM Price">
                            </div>
                            <div class="form-group">
                                <label for="bp_small_6">Box/Packet Small 6 Hours</label>
                                <input type="text" class="form-control" name="bp_small_6" id="bp_small_6"
                                    placeholder="Box/Packet Small 6 Hours">
                            </div>
                            <div class="form-group">
                                <label for="bp_small_3">Box/Packet Small 3 Hours</label>
                                <input type="text" class="form-control" name="bp_small_3" id="bp_small_3"
                                    placeholder="Box/Packet Small 3 Hours">
                            </div>
                            <div class="form-group">
                                <label for="bp_small_2">Box/Packet Small 2 Hours</label>
                                <input type="text" class="form-control" name="bp_small_2" id="bp_small_2"
                                    placeholder="Box/Packet Small 2 Hours">
                            </div>
                            <div class="form-group">
                                <label for="bp_small_express">Box/Packet Small Express</label>
                                <input type="text" class="form-control" name="bp_small_express" id="bp_small_express"
                                    placeholder="Box/Packet Small express">
                            </div>
                            <div class="form-group">
                                <label for="bp_small_timed">Box/Packet Small With Time</label>
                                <input type="text" class="form-control" name="bp_small_timed" id="bp_small_timed"
                                    placeholder="Box/Packet Small With Time">
                            </div>
                            <div class="form-group">
                                <label for="bp_medium_6">Box/Packet Medium 6 Hours</label>
                                <input type="text" class="form-control" name="bp_medium_6" id="bp_medium_6"
                                    placeholder="Box/Packet Medium 6 Hours">
                            </div>
                            <div class="form-group">
                                <label for="bp_medium_3">Box/Packet Medium 3 Hours</label>
                                <input type="text" class="form-control" name="bp_medium_3" id="bp_medium_3"
                                    placeholder="Box/Packet Medium 3 Hours">
                            </div>
                            <div class="form-group">
                                <label for="bp_medium_2">Box/Packet Medium 2 Hours</label>
                                <input type="text" class="form-control" name="bp_medium_2" id="bp_medium_2"
                                    placeholder="Box/Packet Medium 2 Hours">
                            </div>
                            <div class="form-group">
                                <label for="bp_medium_express">Box/Packet Medium Express</label>
                                <input type="text" class="form-control" name="bp_medium_express" id="bp_medium_express"
                                    placeholder="Box/Packet Medium Express">
                            </div>
                            <div class="form-group">
                                <label for="bp_medium_timed">Box/Packet Medium With Time</label>
                                <input type="text" class="form-control" name="bp_medium_timed" id="bp_medium_timed"
                                    placeholder="Box/Packet Medium With Time">
                            </div>
                            <div class="form-group">
                                <label for="bp_large_6">Box/Packet Large 6 Hours</label>
                                <input type="text" class="form-control" name="bp_large_6" id="bp_large_6"
                                    placeholder="Box/Packet Large 6 Hours">
                            </div>
                            <div class="form-group">
                                <label for="bp_large_3">Box/Packet Large 3 Hours</label>
                                <input type="text" class="form-control" name="bp_large_3" id="bp_large_3"
                                    placeholder="Box/Packet Large 3 Hours">
                            </div>
                            <div class="form-group">
                                <label for="bp_large_2">Box/Packet Large 2 Hours</label>
                                <input type="text" class="form-control" name="bp_large_2" id="bp_large_2"
                                    placeholder="Box/Packet Large 2 Hours">
                            </div>
                            <div class="form-group">
                                <label for="bp_large_express">Box/Packet Large Express</label>
                                <input type="text" class="form-control" name="bp_large_express" id="bp_large_express"
                                    placeholder="Box/Packet Large Express">
                            </div>
                            <div class="form-group">
                                <label for="bp_large_timed">Box/Packet Large With Time</label>
                                <input type="text" class="form-control" name="bp_large_timed" id="bp_large_timed"
                                    placeholder="Box/Packet Large With Time">
                            </div>
                            <div class="form-group">
                                <label for="lp_km">Lastpall KM</label>
                                <input type="text" class="form-control" name="lp_km" id="lp_km"
                                    placeholder="Lastpall Price">
                            </div>
                            <div class="form-group">
                                <label for="lp_price">Lastpall Price</label>
                                <input type="text" class="form-control" name="lp_price" id="lp_price"
                                    placeholder="Lastpall Price">
                            </div>
                            <div class="form-group">
                                <label for="lp_extra">Lastpall Extra</label>
                                <input type="text" class="form-control" name="lp_extra" id="lp_extra"
                                    placeholder="Lastpall Extra">
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
        $(document).on('click', '#upload', function() {
            $("#overlay").fadeIn(300);
            $('#exampleModal').modal('hide');
            var form = $('#upload_form')[0];

            var data = new FormData(form);
            $.ajax({
                url: "/api/pricesimport",
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                success: function(data) {
                    location.reload();
                },
                error: function(data) {
                    alert('error on import');
                    location.reload();
                }
            });
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
                    $('#area').val(obj.area);
                    $('#zip_code').val(obj.zip_code)
                    $('#bp_km_price').val(obj.bp_km_price)
                    $('#bp_large_2').val(obj.bp_large_2)
                    $('#bp_large_3').val(obj.bp_large_3)
                    $('#bp_large_6').val(obj.bp_large_6)
                    $('#bp_large_express').val(obj.bp_large_express)
                    $('#bp_large_timed').val(obj.bp_large_timed)
                    $('#bp_medium_2').val(obj.bp_medium_2)
                    $('#bp_medium_3').val(obj.bp_medium_3)
                    $('#bp_medium_6').val(obj.bp_medium_6)
                    $('#bp_medium_express').val(obj.bp_medium_express)
                    $('#bp_medium_timed').val(obj.bp_medium_timed)
                    $('#bp_small_2').val(obj.bp_small_2)
                    $('#bp_small_3').val(obj.bp_small_3)
                    $('#bp_small_6').val(obj.bp_small_6)
                    $('#bp_small_express').val(obj.bp_small_express)
                    $('#bp_small_timed').val(obj.bp_small_timed)
                    $('#lp_km').val(obj.lp_km)
                    $('#lp_price').val(obj.lp_price)
                    $('#lp_extra').val(obj.lp_extra)
                }
            });
        });

        $('#edit-modal').on('hide.bs.modal', function() {
            $('.edit-item-trigger-clicked').removeClass('edit-item-trigger-clicked')
            $("#prices_form").trigger("reset");
            $('#modal_result').empty();
        });

        $('.delete-all').on('click', function() {
            $("#overlay").fadeIn(300);
            $.ajax({
                url: '/api/pricestruncate',
                type: "GET",
                success: function(data) {
                    html = '<div class="alert alert-success">';
                    html += '<p>Delete Success</p>'
                    html += '</div>';
                    $('#form_result').html(html);
                    $('#all_confirm_modal').modal('hide');
                    $('#prices_table').DataTable().ajax.reload();
                },
                error: function(data) {
                    if (data.responseJSON.message) {
                        html = '<div class="alert alert-danger">';
                        html += '<p>' + data.responseJSON.message + '</p>'
                        html += '</div>';
                        $('#form_result').html(html);
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
                buttons: [{
                        text: 'Load from Excel',
                        action: function(e, dt, node, config) {
                            $('#exampleModal').modal('show');
                        },
                        className: 'btn-primary'
                    },
                    {
                        text: 'Export to Excel',
                        action: function(e, dt, node, config) {
                            $("#overlay").fadeIn(300);
                            $.ajax({
                                xhrFields: {
                                    responseType: 'blob',
                                },
                                type: 'GET',
                                url: '/api/pricesexport',
                                success: function(result, status, xhr) {

                                    var disposition = xhr.getResponseHeader(
                                        'content-disposition');
                                    var filename = '';
                                    if (disposition && disposition.indexOf('attachment') !== -
                                        1) {
                                        var filenameRegex =
                                            /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                                        var matches = filenameRegex.exec(disposition);
                                        if (matches != null && matches[1]) {
                                            filename = matches[1].replace(/['"]/g, '');
                                        }
                                    }

                                    var blob = new Blob([result], {
                                        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                    });
                                    console.log(disposition);
                                    var link = document.createElement('a');
                                    link.href = window.URL.createObjectURL(blob);
                                    link.download = filename;

                                    document.body.appendChild(link);

                                    link.click();
                                    document.body.removeChild(link);
                                }
                            }).done(function() {
                                setTimeout(function() {
                                    $("#overlay").fadeOut(300);
                                }, 500);
                            }).fail(function() {
                                setTimeout(function() {
                                    $("#overlay").fadeOut(300);
                                }, 500);
                            });;
                        },
                        className: 'btn-success'

                    },
                    {
                        className: 'btn-danger',
                        text: 'Delete all data',
                        action: function(e, dt, node, config) {
                            $('#all_confirm_modal').modal('show');
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
                    data: 'area',
                    name: 'area'
                },
                {
                    data: 'zip_code',
                    name: 'zip_code'
                },
                {
                    data: 'bp_km_price',
                    name: 'bp_km_price',
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
