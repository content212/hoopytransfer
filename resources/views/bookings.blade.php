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
                        <span id="modal_result"></span>

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
                                    <input type="text" class="form-control" id="to" placeholder="To Zip Code"
                                        readonly>
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
                                    <input type="text" class="form-control" id="from_address"
                                        placeholder="From Address" readonly>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-6">
                                <div class="form-group">
                                    <label for="to_name">To Name</label>
                                    <input type="text" class="form-control" id="to_name" placeholder="To Name"
                                        readonly>
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
                                    <input type="text" class="form-control" id="km" placeholder="Distance"
                                        readonly>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <div class="form-group">
                                    <label for="duration">Duration</label>
                                    <input type="text" class="form-control" id="duration" placeholder="Duration"
                                        readonly>
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
                            @foreach (App\Models\Booking::getAllStatus() as $status)
                                <div class="col-lg-1">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="status" value="{{ $loop->index }}" @if ($loop->index != 5) disabled  @endif>
                                        <label class="form-check-label" for="{{ $loop->index }}">
                                            {{ $status }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach

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
                    $('#name').val(obj.user.name)
                    $('#phone').val(obj.user.phone)
                    $('#email').val(obj.user.email)
                    $('#driver_id').val(obj.driver_id)
                    Livewire.emit('setBookingId', id);
                    //$('#car_type').val(obj.car_type).trigger('click')
                    //setTimeout(function() {
                    //    $('#car_id').val(obj.car_id)
                    //}, 500);
                    
                }
            });
        });

        $('#edit-modal').on('hide.bs.modal', function() {
            $('.edit-item-trigger-clicked').removeClass('edit-item-trigger-clicked')
            $('.is-invalid').removeClass('is-invalid');
            $("#edit-form").trigger("reset");
            $('#packets_table').DataTable().clear().destroy();
            $('#modal_result').empty();
        });
        $('#save').on('click', function(e) {
            e.preventDefault();

            var form = $('#bookings_form');
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
                    @foreach (App\Models\Booking::getAllStatus() as  $status)
                        $.get('{{ route('count', $loop->index ) }}').then(function(response) {
                            $('#count'+ {{ $loop->index }}).html(
                                response)
                        });
                    @endforeach
                },
                error: function(data) {
                    if (data.responseJSON.message) {
                        html = '<div class="alert alert-danger">';
                        html += '<p>' + data.responseJSON.message + '</p>'
                        html += '</div>';
                        $('#modal_result').html(html);
                    }

                    $.each(data.responseJSON.error, function(key, val) {
                        var el = $('#' + key);
                        el.addClass('is-invalid');
                        $.each(val, function(i, err) {
                            html = '<div class="alert alert-danger">';
                            html += '<p>' + err + '</p>'
                            html += '</div>';
                        });
                        $('#modal_result').html(html);
                    });
                    //(data.responseJSON.errors).forEach(function(item){

                    //});
                }
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
                    data: 'status_name',
                    name: 'status_name'
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
