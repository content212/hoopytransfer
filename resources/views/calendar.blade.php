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
                                <div id="calendar" class="fc fc-media-screen fc-direction-ltr fc-theme-bootstrap" style="height: 807px;">
                                </div>
                            </div>
                        </div> <!-- end col -->
                    
                    </div> <!-- end row -->
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div>
        <!-- end col-12 -->
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
        $(document).ready(function(){
            
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
                                        title: item.title,
                                        start: item.start,
                                        backgroundColor: item.backgroundColor,
                                        allDay: item.allDay
                                    }
                                }))
                        }
                    })
                
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

    </script>
@endsection
