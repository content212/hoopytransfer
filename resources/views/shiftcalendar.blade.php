@extends('test')

@section('title', 'Shift Calendar')

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
    <div class="modal fade" id="edit-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit_modal_label">Edit Drivers</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form role="form" id="drivers_form">
                        <div class="box-body">
                            <span id="modal_result"></span>
                            <input type="hidden" id="shift_date">
                            <label for="driver1">1. Driver</label>
                            <select name="driver1" id="driver1" class="form-control">
                                <option value=''>Choose a Driver</option>
                                @foreach($drivers as $driver)
                                    <option value={{ $driver->id }}>{{ $driver->name }}</option>
                                @endforeach
                            </select>
                            <hr class="mt-2 mb-3" />
                            <label for="driver2">2. Driver</label>
                            <select name="driver2" id="driver2" class="form-control">
                                <option value=''>Choose a Driver</option>
                                @foreach($drivers as $driver)
                                    <option value={{ $driver->id }}>{{ $driver->name }}</option>
                                @endforeach
                            </select>
                            <hr class="mt-2 mb-3" />
                            <label for="driver3">3. Driver</label>
                            <select name="driver3" id="driver3" class="form-control">
                                <option value=''>Choose a Driver</option>
                                @foreach($drivers as $driver)
                                    <option value={{ $driver->id }}>{{ $driver->name }}</option>
                                @endforeach
                            </select>
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
                        url: '/api/shifts',
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
                dateClick: function(info) {
                    alert('Date: ' + info.dateStr);
                    $.ajax({
                        url: "/api/shifts/" + info.dateStr,
                        type: "GET",
                        headers: {
                            "accept": "application/json",
                            "content-type": "application/json",
                            "authorization": "Bearer " + token
                        },
                        success: function(data) {
                            data.forEach(element => {
                                $("#driver" + element.queue).val(element.driver_id);
                            });
                            $("#shift_date").val(info.dateStr);
                            $('#edit-modal').modal('show');
                        }
                    });
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
        $('#save').on('click', function(e) {
            $("#overlay").fadeIn(300);
            e.preventDefault();
            var form = $('#drivers_form');
            var date = $('#shift_date').val();
            console.log(date);
            var errors = "";
            for (let index = 1; index < 4; index++) {
                var driver_id = $('#driver' + index).val();
                var data = {
                    "shift_date": date,
                    "driver_id": driver_id,
                    "queue": index
                }
                console.log(data);
                $.ajax({
                    url: "/api/shifts",
                    type: "POST",
                    data: {
                        shift_date: date,
                        driver_id: driver_id,
                        queue: index
                    },
                    async: false,
                    error: function(data) {
                        if (data.responseJSON.message) {
                            errors += '<p>' + data.responseJSON.message + '</p>'
                        }
                    }
                });
            }
            setTimeout(function() {
                $("#overlay").fadeOut(300);
            }, 500);
            if (errors != "") {
                html = '<div class="alert alert-danger">';
                html += errors;
                html += '</div>';
                $('#modal_result').html(html);
            }
            else{
                html = '<div class="alert alert-success">';
                html += '<p>Save Success</p>'
                html += '</div>';
                $('#modal_result').html(html);
                $('#edit-modal').animate({
                    scrollTop: $("#modal_result").offset().top
                }, 'slow');
            }
            
        })
    </script>
@endsection
