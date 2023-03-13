@extends('test')

@section('title', 'Calendar')

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
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="mt-4 mt-lg-0">
                                <div id="calendar" class="fc fc-media-screen fc-direction-ltr fc-theme-bootstrap"
                                    style="height: 807px;">
                                </div>
                            </div>
                        </div> <!-- end col -->

                    </div> <!-- end row -->
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div>
        <!-- end col-12 -->
    </div>
    <div class="modal top fade" id="edit-modal" tabindex="-1" aria-labelledby="editmodalLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="true">
        <div class="modal-dialog modal-xl  modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editmodalLabel">Booking</h5>
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
                                <input type="text" class="form-control booking-form" id="booking_date"
                                    placeholder="Booking Date" readonly>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <label for="booking_time">Booking Time</label>
                                <input type="text" class="form-control booking-form" id="booking_time"
                                    placeholder="Booking Time" readonly>
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
                                <input type="text" class="form-control" id="from_address" placeholder="From Address"
                                    readonly>
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
                    @livewire('car-driver', ['calendar' => true])
                </div>
                <div class="modal-footer">
                    <div class="row justify-content-between">
                        <div class="col-lg-3 offset-md-1 btn-margin">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Close
                            </button>
                        </div>
                    </div>
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
        $(document).ready(function() {

            var calendar = $("#calendar");
            var calendarObj = new FullCalendar.Calendar(calendar[0], {
                slotDuration: "00:15:00",
                slotMinTime: "08:00:00",
                slotMaxTime: "19:00:00",
                themeSystem: "bootstrap",
                bootstrapFontAwesome: !1,
                events: function(info, successCallback, failureCallback) {
                    var start = new Date(info.start.valueOf());
                    var end = new Date(info.end.valueOf());
                    console.log(start)
                    $.ajax({
                        url: '/api/caledarEvents',
                        type: 'GET',
                        data: {
                            start: start.toISOString(),
                            end: end.toISOString()
                        },
                        success: function(data) {
                            successCallback(
                                $.map(data, function(item) {
                                    return {
                                        id: item.id,
                                        title: item.title,
                                        start: item.start,
                                        backgroundColor: item.backgroundColor,
                                        allDay: item.allDay
                                    }
                                }))
                        }
                    })

                },
                eventClick: function(arg) {
                    console.log(arg.event);
                    $.ajax({
                        url: "/api/bookings/" + arg.event.id,
                        type: "GET",
                        headers: {
                            "accept": "application/json",
                            "content-type": "application/json",
                            "authorization": "Bearer " + token
                        },
                        success: function(data) {
                            obj = JSON.parse(data);
                            $('#bookings_form').find(':radio[name=status][value="' + obj
                                .status + '"]').prop(
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
                            $('#driver_id').val(obj.driver_id)
                            $('#car_type').val(obj.car_type).trigger('click')
                            setTimeout(function() {
                                $('#car_id').val(obj.car_id);
                                $('#driver_id').val(obj.driver_id)
                                $('#car_type').val(obj.car_type)
                            }, 500);
                        }
                    });

                    $('#edit-modal').modal('show');
                },
                buttonText: {
                    prev: "Prev",
                    next: "Next",
                    today: "Today"
                },
                eventDidMount: function(event, element) {
                    console.log(event);
                    $(element).tooltip({
                        title: event.title
                    });
                },
                eventTimeFormat: { // like '14:30:00'
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                },
                initialView: "dayGridMonth",
                handleWindowResize: !0,
                height: $(window).height() - 200,
                headerToolbar: {
                    right: "today prev,next",
                    left: "title",
                },
                editable: false,
                droppable: false,
                selectable: false,
            });
            calendarObj.render();
        });
    </script>
@endsection
