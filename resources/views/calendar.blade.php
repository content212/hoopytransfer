@extends('test')

@section('title', 'Calendar')

@section('role', $role)

@section('name', $name)

@section('css')
    <style>
        .popover {
            z-index: 999999;
        }
        #service_name_container {
            border-bottom: 3px solid #02b9ff;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
                    <h5 class="modal-title" id="editmodalLabel">Booking Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="modal_result"></span>
                    <div class="row justify-content-start">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <div id="service_container">
                                        <div id="service_image"></div>
                                        <div id="service_name_container">
                                            <h5 id="service_name"></h5>
                                        </div>
                                        <div class="mt-2">
                                            <ul style="list-style: none; margin: 0; padding: 0">
                                                <li>Max. <span id="person"></span> person</li>
                                                <li>Max. <span id="suitcase"></span> suitcase</li>
                                                <li>Free Cancellation <small id="free_cancellation"></small></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div id="traveler_details_container">

                                        <div class="mt-2">
                                            <h5>From</h5>
                                            <div id="from"></div>
                                        </div>

                                        <div class="mt-2">
                                            <h5>To</h5>
                                            <div id="to"></div>
                                        </div>

                                        <div class="mt-2">
                                            <h5>Date Time</h5>
                                            <div id="date"></div>
                                        </div>

                                        <div class="mt-2">
                                            <h5>Distance</h5>
                                            <div id="distance"></div>
                                        </div>

                                        <div class="mt-2">
                                            <h5>Estimated Time</h5>
                                            <div id="estimated_time"></div>
                                        </div>
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
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            Customer Info
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <h5 class="card-title">Name / Surname</h5>
                                                    <p class="card-text" id="name"></p>
                                                </div>
                                                <div class="col-md-4">
                                                    <h5 class="card-title">Phone</h5>
                                                    <p class="card-text" id="phone"></p>
                                                </div>
                                                <div class="col-md-4">
                                                    <h5 class="card-title">Mail</h5>
                                                    <p class="card-text" id="mail"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row" id="payment_info_container">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            Payment Info
                                        </div>
                                        <div class="card-body">
                                            <div class="row">

                                                <div class="col-md-3">
                                                    <h5 class="card-title">Type / Status</h5>
                                                    <p class="card-text" id="payment_type"></p>
                                                </div>

                                                <div class="col-md-3">
                                                    <h5 class="card-title" id="p3_title"></h5>
                                                    <p class="card-text" id="p3"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="display: flex; justify-content:flex-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <input type="hidden" id="booking_id" value="">
                        <input type="hidden" id="status_id" value="">
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
        $(document).ready(function () {
            var calendar = $("#calendar");
            var calendarObj = new FullCalendar.Calendar(calendar[0], {
                slotDuration: "00:15:00",
                slotMinTime: "08:00:00",
                slotMaxTime: "19:00:00",
                themeSystem: "bootstrap",
                bootstrapFontAwesome: !1,
                events: function (info, successCallback, failureCallback) {
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
                        success: function (data) {
                            successCallback(
                            $.map(data, function (item) {
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
                eventClick: function (arg) {
                    console.log(arg.event);
                    $.ajax({
                        url: "/api/driver-bookings/" + arg.event.id,
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

                },
                buttonText: {
                    prev: "Prev",
                    next: "Next",
                    today: "Today"
                },
                eventDidMount: function (event, element) {
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

        function setData(obj) {
            if (!obj) {
                return;
            }
            $("#cardriver-container").hide();
            $("#cardriver-container").hide();
            $("#price-container").hide();
            $("#payment_info_container").hide();
            $("#service_container").hide();

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

                const carType = parseInt(obj.car_type) || 0;
                const carId = parseInt(obj.car_id) || 0;

                if (carType > 0) {
                    $("#car_type").val(carType)
                } else {
                    $("#car_type").val("")
                }
                $("#cardriver-container").show();
            }

            $('#booking_date').html(obj.booking_date + ' / ' + obj.booking_time)
            $('#status_name').html(obj.status_name)
            $('#reservation_no').html(obj.track_code)
            $('#name').html(obj.user.name + ' ' + obj.user.surname);
            $('#mail').html(obj.user.email);
            $('#phone').html(obj.user.country_code + obj.user.phone);

            $("#from").html(obj.from_name);
            $("#to").html(obj.to_name);
            $("#date").html(obj.booking_date + ' ' + obj.booking_time);
            $("#estimated_time").html(obj.duration + " min");
            $("#distance").html(obj.km + " km");

            if (obj.data) {

                if (obj.data.payment_type === "Pre") {
                    $("#payment_type").html(obj.data.payment_type + ' / ' + obj.payment_status)
                    $("#payment_info_container").show();
                    $("#p3_title").html("Paid After Trip");
                    $("#p3").html("$" + obj.data.driver_payment);
                }else {
                    $("#payment_info_container").hide();
                    $("#p3").html("");
                    $("#p3_title").html("");
                    $("#payment_type").html("");
                }
            }else {
                $("#payment_info_container").hide();
                $("#p3").html("");
                $("#p3_title").html("");
                $("#payment_type").html("");
            }


            if (obj.service) {
                $("#service_container").show();
                $("#person").html(obj.service.person_capacity);
                $("#suitcase").html(obj.service.baggage_capacity);
                $("#free_cancellation").html("(up to " + obj.service.free_cancellation + " hours)")
                if (obj.service.image_url) {
                    $("#service_image").html("<img style='width:100%' src='" + obj.service.image_url + "'>")
                }
                $("#service_name").html(obj.service.name)
            }

            $('#edit-modal').modal('show');
        }


    </script>
@endsection
