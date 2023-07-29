@extends('test')

@if (app('request')->input('status') != '')
    @section('title', 'Bookings - ' . App\Models\Booking::getAllStatus()[app('request')->input('status')])
@else
    @section('title', 'Bookings')
@endif




@section('role', $role)

@section('name', $name)

@section('css')
    <style>
        #service_name_container {
            border-bottom: 3px solid #02b9ff;
        }
    </style>
@endsection


@section('content')
    <div class="table-responsive">
        <table id="booking_table" class="table table-striped table-sm" style="width: 100%">
            <thead>
            <tr>
                <th>#</th>
                <th>Track Code</th>
                <th>Status</th>
                <th>From Name</th>
                <th>To Name</th>
                <th>User Name</th>
                <th>Driver Name</th>
                <th>Created At</th>
                <th>Edit</th>
            </tr>
            </thead>
        </table>
    </div>
    <form role="form" action="" id="bookings_form">
        <div class="modal top fade" id="edit-modal" tabindex="-1" aria-labelledby="editmodalLabel" aria-hidden="true"
             data-bs-backdrop="static" data-bs-keyboard="true">
            <div class="modal-dialog modal-xl  modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editmodalLabel">Edit Booking</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <span id="modal_result"></span>
                        <div class="row justify-content-start">
                            <div class="col-md-3">

                                <div class="card">
                                    <div class="card-body">
                                        <div id="service_image"></div>
                                        <div id="service_name_container">
                                            <h5 id="service_name"></h5>
                                            <div id="service_price"></div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header">
                                                Booking Info
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <h5 class="card-title">Reservation No</h5>
                                                        <p class="card-text" id="reservation_no"></p>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <h5 class="card-title">Booking Date Time</h5>
                                                        <p class="card-text" id="booking_date"></p>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <h5 class="card-title">Status</h5>
                                                        <p class="card-text" id="status_name"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" id="cardriver-container">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="service">Service</label>
                                            <select name="car_type" id="car_type" class="form-control">
                                                <option value="">Choose a Service</option>
                                                @foreach (App\Models\CarType::all() as $carType)
                                                    <option value="{{ $carType->id }}">{{ $carType->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="car">Car</label>
                                            <select name="car_id" id="car_id" class="form-control">
                                                <option value="">Choose a Car</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="driver">Driver</label>
                                            <select name="driver_id" id="driver_id" class="form-control">
                                                <option value="">Choose a Driver</option>
                                                @foreach (App\Models\Driver::select('drivers.id','users.name','users.surname')->join('users', 'users.id', '=', 'drivers.user_id')->get() as $driver)
                                                    <option
                                                        value="{{$driver->id}}">{{ $driver->name }} {{$driver->surname}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="price-container">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="price">Price</label>
                                            <input
                                                onkeypress="return onlyNumberKey(event)"
                                                oninput="enforceNumberValidation(this)"
                                                class="form-control" placeholder="0.00" name="price" id="price">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="display: flex; justify-content: space-between">
                        <div>
                            <button type="button" id="cancel_booking" style="display: none" class="btn btn-danger">
                                Cancel Reservation
                            </button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Close
                            </button>
                            <input type="hidden" id="booking_id" value="">
                            <input type="hidden" id="status_id" value="">
                            <button type="button" class="btn btn-primary" id="save">Save changes</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection
@section('script')
    <script src="{{ asset('js/jquery.tabledit.js') }}"></script>
    <script>
        $(document).on('click', ".edit", function () {
            $(this).addClass('edit-item-trigger-clicked');
            $('#edit-modal').modal('show');
        });


        $("#cancel_booking").click(function () {
            Swal.fire({
                title: 'Do you really want to cancel reservation?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirm Cancellation',
                cancelButtonText: 'Close',

            }).then((result) => {
                if (result.isConfirmed) {

                    //ajax to cancel
                    //reload data

                    var bookingId = $("#booking_id").val();
                    cancelBooking(bookingId);



                }
            })
        })

        var token = Cookies.get('token');
        $.ajaxSetup({
            headers: {
                'authorization': "Bearer " + token
            }
        });

        $('#edit-modal').on('show.bs.modal', function () {
            const el = $(".edit-item-trigger-clicked");
            const id = el.data('id');
            getBookingDetail(id);
        });


        function cancelBooking(id) {
            const token = Cookies.get('token');
            $.ajax({
                url: "/api/bookings/" + id+ "/cancel",
                type: "POST",
                headers: {
                    "accept": "application/json",
                    "content-type": "application/json",
                    "authorization": "Bearer " + token
                },
                success: function (data) {
                    $('#booking_table').DataTable().ajax.reload();
                    $('#edit-modal').modal('hide');
                    reloadCounts();
                    Swal.fire(
                        'Canceled!',
                        'Reservation has been canceled.',
                        'success'
                    );
                }
            });
        }


        function getBookingDetail(id) {
            $("#booking_id").val(id);
            const token = Cookies.get('token');
            $.ajax({
                url: "/api/bookings/" + id,
                type: "GET",
                headers: {
                    "accept": "application/json",
                    "content-type": "application/json",
                    "authorization": "Bearer " + token
                },
                success: function (data) {
                    const obj = JSON.parse(data);
                    setData(obj);
                }
            });
        }


        function onlyNumberKey(evt) {
            let ASCIICode = (evt.which) ? evt.which : evt.keyCode
            if (ASCIICode === 46 || ASCIICode === 44) {
                return true;
            }
            return !(ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57));
        }

        function enforceNumberValidation(ele) {
            let val = $(ele).val();
            val = val.replace(",", ".");
            const splitVal = val.split('.');

            if (splitVal.length > 1) {
                const result = splitVal[0] + '.' + splitVal[1].substr(0, 2)
                $(ele).val(result);
            } else {
                $(ele).val(splitVal);
            }
        }

        function setData(obj) {
            console.log(obj);
            if (!obj) {
                alert("data not found!");
                return;
            }

            $("#status_id").val(obj.status);
            const statusId = parseInt(obj.status) || -1;

            if (statusId === 1 || statusId === 2 || statusId === 9) {
                //show driver form
                let driverId = parseInt(obj.driver_id) || 0;
                if (driverId > 0) {
                    $("#driver_id").val(driverId);
                } else {
                    $("#driver_id").val("");
                }

                if (obj.service) {
                    $("#car_type").val(obj.service.id)
                    setCar(parseInt(obj.service.id) || 0, parseInt(obj.car_id) || 0);
                }

                $("#cardriver-container").show();
                $("#cancel_booking").show();

            } else {
                $("#cardriver-container").hide();
                $("#cancel_booking").hide();
            }

            if (statusId === 9) {
                //show price form
                $("#price-container").show();
            } else {
                $("#price-container").hide();
            }


            $('#booking_date').html(obj.booking_date + ' / ' + obj.booking_time)
            $('#status_name').html(obj.status_name)
            $('#reservation_no').html(obj.track_code)

            if (obj.service) {
                if (obj.service.image_url) {
                    $("#service_image").html("<img style='width:100%' src='" + obj.service.image_url + "'>")
                }
                $("#service_name").html(obj.service.name)
            }
        }

        $('#edit-modal').on('hide.bs.modal', function () {
            $('.edit-item-trigger-clicked').removeClass('edit-item-trigger-clicked')
            $('.is-invalid').removeClass('is-invalid');
            $("#edit-form").trigger("reset");
            $('#packets_table').DataTable().clear().destroy();
            $('#modal_result').empty();
        });



        $("#car_type").change(function () {
            var type = parseInt($(this).val()) || 0;
            setCar(type, 0);
        });

        function setCar(type, selectedValue) {
            if (type > 0) {
                var token = Cookies.get('token');
                $.ajax({
                    url: "/api/carsbytype/" + type,
                    type: "GET",
                    headers: {
                        "accept": "application/json",
                        "content-type": "application/json",
                        "authorization": "Bearer " + token
                    },
                    success: function (data) {
                        $("#car_id").empty();
                        $("#car_id").append('<option>Choose a Car</option>');
                        data.forEach(function (item) {
                            if (selectedValue == parseInt(item.id)) {
                                $("#car_id").append('<option selected value="' + item.id + '">' + item.plate + '</option>');
                            } else {
                                $("#car_id").append('<option value="' + item.id + '">' + item.plate + '</option>');
                            }
                        });
                    }
                });
            } else {
                $("#car_id").empty();
                $("#car_id").append('<option>Choose a Car</option>');
            }
        }


        $('#save').on('click', function (e) {
            e.preventDefault();
            const bookingId = $("#booking_id").val();
            const carType = $("#car_type").val();
            const carId = $("#car_id").val();
            const driverId = $("#driver_id").val();
            const statusId = parseInt($("#status_id").val()) || -1;
            let data = {};

            switch (statusId) {
                case 1: //waiting for confirmation
                    data = {
                        "car_type": carType,
                        "car_id": carId,
                        "driver_id": driverId
                    };
                    break;
                case 2: //trip is expected
                    data = {
                        "car_type": carType,
                        "car_id": carId,
                        "driver_id": driverId
                    };
                    break;
                case 9: //booking request
                    const price = $("#price").val();
                    data = {
                        "car_type": carType,
                        "car_id": carId,
                        "driver_id": driverId,
                        "price": price
                    };
                    break;
            }

            $.ajax({
                headers: {
                    "Authorization": "Bearer " + Cookies.get('token')
                },
                url: "/api/bookings/" + bookingId,
                type: "POST",
                data: data,
                success: function (data) {
                    $('#booking_table').DataTable().ajax.reload();
                    $('#edit-modal').modal('hide');
                    reloadCounts();
                    Swal.fire(
                        'Saved!',
                        'The process completed successfully',
                        'success'
                    );
                },
                error: function (data) {
                    if (data.responseJSON.message) {
                        html = '<div class="alert alert-danger">';
                        html += '<p>' + data.responseJSON.message + '</p>'
                        html += '</div>';
                        $('#modal_result').html(html);
                    }
                    $.each(data.responseJSON.error, function (key, val) {
                        var el = $('#' + key);
                        el.addClass('is-invalid');
                        $.each(val, function (i, err) {
                            html = '<div class="alert alert-danger">';
                            html += '<p>' + err + '</p>'
                            html += '</div>';
                        });
                        $('#modal_result').html(html);
                    });
                }
            });
        });

        function reloadCounts() {
            @foreach (App\Models\Booking::getAllStatus() as  $status)
            $.get('{{ route('count', $loop->index ) }}').then(function (response) {
                $('#count' + {{ $loop->index }}).html(
                    response)
            });
            @endforeach
        }

        $('#booking_table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            order: [
                [6, "desc"]
            ],
            dom: '<"top"f<"clear">>rt<"bottom"ip<"clear">>',
            pageLength: 50,
            ajax: {
                url: "/api/bookings" + window.location.search,
                type: 'get',
                headers: {
                    "Authorization": "Bearer " + Cookies.get('token')
                },
            },
            columns: [
                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'track_code',
                    name: 'track_code'
                },
                {
                    data: 'status_name',
                    name: 'status_name'
                },
                {
                    data: 'from_name',
                    name: 'from_name'
                },
                {
                    data: 'to_name',
                    name: 'to_name'
                },
                {
                    data: 'user_name',
                    name: 'user_name'
                },
                {
                    data: 'driver_name',
                    name: 'driver_name'
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
            ],

        });
    </script>
@endsection
