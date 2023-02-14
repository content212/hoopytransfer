@extends('test')

@if (app('request')->input('status') != '')
    @if (app('request')->input('status') == 0)
        @section('title', 'Bookings - Waiting for Booking')
    @elseif (app('request')->input('status') == 1)
        @section('title', 'Bookings - Trip is expected')
    @elseif (app('request')->input('status') == 2)
        @section('title', 'Bookings - Waiting for Confirmation')
    @elseif (app('request')->input('status') == 3)
        @section('title', 'Bookings - Trip is completed')
    @elseif (app('request')->input('status') == 4)
        @section('title', 'Bookings - Trip is not Completed')
    @elseif (app('request')->input('status') == 5)
        @section('title', 'Bookings - Canceled by Customer')
    @elseif (app('request')->input('status') == 6)
        @section('title', 'Bookings - Canceled by System')
    @elseif (app('request')->input('status') == 7)
        @section('title', 'Bookings - Completed')
    @endif
    
@else
    @section('title', 'Bookings')
@endif




@section('role', $role)

@section('name', $name)

@section('css')
    <style>
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
                    <th>From Zip</th>
                    <th>From Name</th>
                    <th>To Zip</th>
                    <th>To Name</th>
                    <th>User Name</th>
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

                        <div class="row justify-content-start">

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label for="track_code">Track Code</label>
                                    <input type="text" class="form-control" id="track_code" placeholder="Track Code"
                                        readonly>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label for="booking_date">Booking Date</label>
                                    <input type="text" class="form-control booking-form" name="booking_date"
                                        id="booking_date" placeholder="Booking Date">
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label for="booking_time">Booking Time</label>
                                    <input type="text" class="form-control booking-form" name="booking_time"
                                        id="booking_time" placeholder="Booking Time">
                                </div>
                            </div>
                        </div>
                        <hr class="mt-2 mb-3" />
                        <div class="row gx-1 justify-content-start">

                            <div class="col-lg-2 col-md-6">
                                <div class="form-group">
                                    <label for="from">From Zip Code</label>
                                    <input type="text" class="form-control" id="from" placeholder="From Zip Code"
                                        readonly>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-6">
                                <div class="form-group">
                                    <label for="from_lat">From Latitude</label>
                                    <input type="text" class="form-control" id="from_lat" placeholder="From Latitude"
                                        readonly>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-6">
                                <div class="form-group">
                                    <label for="from_lng">From Longitude</label>
                                    <input type="text" class="form-control" id="from_lng" placeholder="From Longitude"
                                        readonly>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-6">
                                <div class="form-group">
                                    <label for="to">To Zip Code</label>
                                    <input type="text" class="form-control" id="to" placeholder="To Zip Code" readonly>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-6">
                                <div class="form-group">
                                    <label for="to_lat">To Latitude</label>
                                    <input type="text" class="form-control" id="to_lat" placeholder="To Latitude"
                                        readonly>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-6">
                                <div class="form-group">
                                    <label for="to_lng">To Longitude</label>
                                    <input type="text" class="form-control" id="to_lng" placeholder="To Longitude"
                                        readonly>
                                </div>
                            </div>

                        </div>
                        <hr class="mt-2 mb-3" />
                        <div class="row justify-content-start">

                            <div class="col-lg-2 col-md-6">
                                <div class="form-group">
                                    <label for="from_name">From Name</label>
                                    <input type="text" class="form-control" id="from_name" placeholder="From Name"
                                        readonly>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-6">
                                <div class="form-group">
                                    <label for="from_address">From Address</label>
                                    <input type="text" class="form-control" id="from_address" placeholder="From Address"
                                        readonly>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-6">
                                <div class="form-group">
                                    <label for="to_name">To Name</label>
                                    <input type="text" class="form-control" id="to_name" placeholder="To Name" readonly>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-6">
                                <div class="form-group">
                                    <label for="to_address">To Address</label>
                                    <input type="text" class="form-control" id="to_address" placeholder="To Address"
                                        readonly>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <div class="form-group">
                                    <label for="km">Distance</label>
                                    <input type="text" class="form-control" id="km" placeholder="Distance" readonly>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <div class="form-group">
                                    <label for="duration">Duration</label>
                                    <input type="text" class="form-control" id="duration" placeholder="Duration" readonly>
                                </div>
                            </div>

                        </div>
                        <hr class="mt-2 mb-3" />
                        <div class="row justify-content-start">

                            <div class="col-lg-4 col-md-12">
                                <div class="form-group">
                                    <label for="name">Customer Name</label>
                                    <input type="text" class="form-control" id="name" readonly>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label for="phone">Customer Phone</label>
                                    <input type="text" class="form-control" id="phone" readonly>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label for="email">Customer Email</label>
                                    <input type="text" class="form-control" id="email" readonly>
                                </div>
                            </div>

                        </div>

                        <hr class="mt-2 mb-3" />
                        
                        @livewire('car-driver')
                        
                    </div>

                    <div class="modal-footer">
                        <div class="row justify-content-between">
                            <div class="col-lg-1">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" value="0">
                                    <label class="form-check-label" for="0">
                                        Waiting for Booking
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-1">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" value="1">
                                    <label class="form-check-label" for="1">
                                        Trip is expected
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-1">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" value="2">
                                    <label class="form-check-label" for="2">
                                        Waiting for Confirmation
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-1">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" value="3">
                                    <label class="form-check-label" for="3">
                                        Trip is completed
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-1">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" value="4">
                                    <label class="form-check-label" for="4">
                                        Trip is not Completed
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-1">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" value="5">
                                    <label class="form-check-label" for="5">
                                        Canceled by Customer
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-1">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" value="6">
                                    <label class="form-check-label" for="6">
                                        Canceled by System
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-1">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" value="7">
                                    <label class="form-check-label" for="7">
                                        Completed
                                    </label>
                                </div>
                            </div>

                            <div class="col-lg-3 offset-md-1 btn-margin">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    Close
                                </button>
                                <button type="button" class="btn btn-primary" id="save">Save changes</button>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
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
                    <button type="button" class="delete-packet btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('js/jquery.tabledit.js') }}"></script>
    <script>
        $(document).on('click', ".edit", function() {
            $(this).addClass('edit-item-trigger-clicked');

            $('#edit-modal').modal('show');
        });
        var token = Cookies.get('token');
        $.ajaxSetup({
            headers: {
                'authorization': "Bearer " + token
            }
        });
        $('#edit-modal').on('show.bs.modal', function() {
            var el = $(".edit-item-trigger-clicked");
            var row = el.closest(".data-row");

            var id = el.data('id');
            var token = Cookies.get('token');
            document.getElementById('bookings_form').action = "/api/bookings/" + id;
            $.ajax({
                url: "/api/bookings/" + id,
                type: "GET",
                headers: {
                    "accept": "application/json",
                    "content-type": "application/json",
                    "authorization": "Bearer " + token
                },
                success: function(data) {
                    obj = JSON.parse(data);
                    $('#bookings_form').find(':radio[name=status][value="' + obj.status + '"]').prop(
                        'checked', true);
                    $('#status').val(obj.status);
                    $('#track_code').val(obj.track_code)
                    $('#from').val(obj.from)
                    $('#from_name').val(obj.from_name)
                    $('#from_address').val(obj.from_address)
                    $('#from_lat').val(obj.from_lat)
                    $('#from_lng').val(obj.from_lng)
                    $('#to').val(obj.to)
                    $('#to_name').val(obj.to_name)
                    $('#to_address').val(obj.to_address)
                    $('#to_lat').val(obj.to_lat)
                    $('#to_lng').val(obj.to_lng)
                    $('#km').val(obj.km)
                    $('#duration').val(obj.duration)
                    $('#booking_date').val(obj.booking_date)
                    $('#booking_time').val(obj.booking_time)
                    $('#name').val(obj.name)
                    $('#phone').val(obj.phone)
                    $('#email').val(obj.email)
                    $('#car_type').val(obj.car_type).trigger('click')
                        setTimeout(function() { 
                            $('#driver_id').val(obj.driver_id)
                        }, 200);
                }
            });
        });

        $('#edit-modal').on('hide.bs.modal', function() {
            $('.edit-item-trigger-clicked').removeClass('edit-item-trigger-clicked')
            $("#edit-form").trigger("reset");
            $('#packets_table').DataTable().clear().destroy();
            $('#modal_result').empty();
        });
        $('#save').on('click', function(e) {
            e.preventDefault();

            var form = $('#bookings_form');
            console.log(form);
            console.log(form.serialize());
            $.ajax({
                headers: {
                    "Authorization": "Bearer " + Cookies.get('token')
                },
                url: document.getElementById('bookings_form').action,
                type: "POST",
                data: form.serialize(),
                success: function(data) {
                    $('#booking_table').DataTable().ajax.reload();
                    $('#edit-modal').modal('hide');
                    $.get('{{ route('count', '0') }}').then(function(response) {
                        response = response.replace(/\s/g, '');
                        $('#count0').html('Waiting for Booking(' +
                            response + ')')
                    });
                    $.get('{{ route('count', '1') }}').then(function(response) {
                        response = response.replace(/\s/g, '');
                        $('#count1').html('Trip is expected(' +
                            response + ')')
                    });
                    $.get('{{ route('count', '2') }}').then(function(response) {
                        response = response.replace(/\s/g, '');
                        $('#count2').html('Waiting for Confirmation(' +
                            response + ')')
                    });
                    $.get('{{ route('count', '3') }}').then(function(response) {
                        response = response.replace(/\s/g, '');
                        $('#count3').html('Trip is completed(' +
                            response + ')')
                    });
                    $.get('{{ route('count', '4') }}').then(function(response) {
                        response = response.replace(/\s/g, '');
                        $('#count4').html('Trip is not Completed(' +
                            response + ')')
                    });
                    $.get('{{ route('count', '5') }}').then(function(response) {
                        response = response.replace(/\s/g, '');
                        $('#count5').html('Canceled by Customer(' +
                            response + ')')
                    });
                    $.get('{{ route('count', '6') }}').then(function(response) {
                        response = response.replace(/\s/g, '');
                        $('#count6').html('Canceled by System(' +
                            response + ')')
                    });
                    $.get('{{ route('count', '7') }}').then(function(response) {
                        response = response.replace(/\s/g, '');
                        $('#count7').html('Completed(' +
                            response + ')')
                    });
                },
            });
        });
        $('#booking_table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            order: [
                [9, "desc"]
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
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'track_code',
                    name: 'track_code'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'from',
                    name: 'from'
                },
                {
                    data: 'from_name',
                    name: 'from_name'
                },
                {
                    data: 'to',
                    name: 'to'
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
