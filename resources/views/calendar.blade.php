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
        @if ($role != 'Driver')
            <div class="col-md-2 col-md-3">
                <select class="form-select filterselect" id="driverselect">
                    <option value="-1">Select Driver</option>
                    @foreach ($drivers as $driver)
                        <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="col-lg-1 col-md-3">
            <select class="form-select filterselect" id="monthselect">
                <option value="-1">Month</option>
                @foreach ($months as $month)
                    <option value="{{ $month->month }}">{{ $month->month }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-lg-1 col-md-3">
            <select class="form-select filterselect" id="yearselect">
                <option value="-1">Year</option>
                @foreach ($years as $year)
                    <option value="{{ $year->year }}">{{ $year->year }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-1 col-md-3">
            <button type="button" class="btn btn-primary" id="filterBtn">Filter</button>
            <button type="button" class="btn btn-danger" id="clearBtn">Clear</button>
        </div>
        <div class="col-lg-1 col-md-3">
            <button type="button" class="btn btn-primary" id="addModalBtn">Add New Job</button>
        </div>
        <div class="col-lg-1 col-md-3">
            <button type="button" class="btn btn-warning" id="excelExport">Export Excel</button>
        </div>
        @if ($role != 'Driver')
            <div class="col-lg-1 col-md-3">
                <select class="form-select filterselect" id="companyselect">
                    <option value="-1">Select Company</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>
    <br>
    <div class="table-responsive">
        <table id="calendar_table" class="table table-striped table-sm" style="width: 100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Name</th>
                    <th>06:00-18:00</th>
                    <th>18:00-22:00</th>
                    <th>22:00-00:00</th>
                    <th>00:00-06:00</th>
                    <th>Note</th>
                    <th>Edit</th>
                </tr>
            </thead>
        </table>
    </div>
    <div class="modal top fade" id="addModal" tabindex="-1" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="true">
        <div class="modal-dialog modal-lg ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="add_result"></span>
                    <form id="addForm">
                        <div class="row ">
                            @if ($role != 'driver')
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="user_id">Driver</label>
                                        <select class="form-select" name="user_id">
                                            <option value="-1">Select Driver</option>
                                            @foreach ($drivers as $driver)
                                                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lager">Lager</label>
                                    <select class="form-select" name="lager">
                                        @foreach ($lagers as $lager)
                                            <option value="{{ $lager->id }}">{{ $lager->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="excuse">Excuse</label>
                                <div class="form-group">
                                    <input type="checkbox" id="excuse" name="excuse" data-switch="bool" />
                                    <label for="excuse" data-on-label="" data-off-label=""></label>
                                </div>
                            </div>
                        </div>
                        <hr class="mt-2 mb-3" />
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="startfinish">Start - Finish</label>
                                    <input type="text" class="form-control date" id="startfinish" data-toggle="date-picker"
                                        data-time-picker="true">
                                </div>
                            </div>
                        </div>
                        <hr class="mt-2 mb-3" />
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="reststartfinish">Rest Start - Rest Finish</label>
                                    <input type="text" class="form-control date" id="reststartfinish"
                                        data-toggle="date-picker" data-time-picker="true">
                                </div>
                            </div>
                        </div>
                        <hr class="mt-2 mb-3" />
                        <div class="row ">

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="note">Note</label>
                                    <textarea class="form-control" name="note" rows="2"></textarea>
                                </div>
                            </div>

                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" id="addBtn">Add</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal top fade" id="editModal" tabindex="-1" tabindex="-1" aria-labelledby="editModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="true">
        <div class="modal-dialog modal-lg ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="edit_result"></span>
                    <form id="editForm" method="POST">
                        <div class="row ">
                            @if ($role != 'driver')
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="user_id">Driver</label>
                                        <select class="form-select" name="user_id" id="edituser_id">
                                            <option value="-1">Select Driver</option>
                                            @foreach ($drivers as $driver)
                                                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lager">Lager</label>
                                    <select class="form-select" name="lager" id="editLager">
                                        @foreach ($lagers as $lager)
                                            <option value="{{ $lager->id }}">{{ $lager->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="excuse">Excuse</label>
                                <div class="form-group">
                                    <input type="checkbox" id="editExcuse" data-switch="bool" />
                                    <label for="editExcuse" data-on-label="" data-off-label=""></label>
                                </div>
                            </div>
                        </div>
                        <hr class="mt-2 mb-3" />
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="startfinish">Start - Finish</label>
                                    <input type="text" class="form-control date" id="editstartfinish"
                                        data-toggle="date-picker" data-time-picker="true">
                                </div>
                            </div>
                        </div>
                        <hr class="mt-2 mb-3" />
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="reststartfinish">Rest Start - Rest Finish</label>
                                    <input type="text" class="form-control date" id="editreststartfinish"
                                        data-toggle="date-picker" data-time-picker="true">
                                </div>
                            </div>
                        </div>
                        <hr class="mt-2 mb-3" />
                        <div class="row ">

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="note">Note</label>
                                    <textarea class="form-control" name="note" id="editNote" rows="2"></textarea>
                                </div>
                            </div>

                        </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" id="saveBtn">Save</button>
                </div>
            </div>
            </form>
        </div>
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
                    <button type="button" class="delete-job btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('js/jquery.tabledit.js') }}"></script>
    <script>
        var token = Cookies.get('token');
        $.ajaxSetup({
            headers: {
                'authorization': "Bearer " + token
            }
        });
        if (getParameterByName('driver')) {
            $('#driverselect').val(getParameterByName('driver'));
        }
        if (getParameterByName('year')) {
            $('#yearselect').val(getParameterByName('year'));
        }
        if (getParameterByName('month')) {
            $('#monthselect').val(getParameterByName('month'));
        }

        $('#addModalBtn').on('click', function() {
            $('#addModal').modal('show');
            $('#startfinish').daterangepicker({
                cancelClass: "btn-light",
                applyButtonClasses: "btn-success",
                timePicker: true,
                timePicker24Hour: true,
                locale: {
                    format: 'MM/DD/YYYY H:mm'
                },
                dateLimit: {
                    'days': 1
                }
            });
            $('#reststartfinish').daterangepicker({
                cancelClass: "btn-light",
                applyButtonClasses: "btn-success",
                timePicker: true,
                timePicker24Hour: true,
                locale: {
                    format: 'MM/DD/YYYY H:mm'
                },
                dateLimit: {
                    'days': 1
                }
            });

        });
        $('#addModal').on('hide.bs.modal', function() {
            $("#addForm").trigger("reset");
            $('#add_result').empty();
        });
        $(document).on('click', '.delete', function() {
            $(this).addClass('delete-item-trigger-clicked');

            $('#confirm_modal').modal('show');

        });
        $('.delete-job').on('click', function(e) {
            var el = $(".delete-item-trigger-clicked");
            var id = el.data('id');

            $.ajax({
                url: 'api/jobs/' + id,
                type: "DELETE",
                success: function(data) {
                    $('#confirm_modal').modal('hide');
                    $('#calendar_table').DataTable().ajax.reload();
                },
                error: function(data) {}
            });
        });
        var company_id = 0;
        $('#companyselect').on('change', function(e) {
            company_id = this.value;
        })
        $('#saveBtn').on('click', function(e) {
            e.preventDefault();

            var form = $('#editForm');
            $.ajax({
                url: document.getElementById('editForm').action,
                type: "POST",
                data: form.serialize(),
                success: function(data) {
                    html = '<div class="alert alert-success">';
                    html += '<p>Save Success</p>'
                    html += '</div>';
                    $('#edit_result').html(html);
                    $('#calendar_table').DataTable().ajax.reload();
                },
                error: function(data) {
                    if (data.responseJSON.message) {
                        html = '<div class="alert alert-danger">';
                        html += '<p>' + data.responseJSON.message + '</p>'
                        html += '</div>';
                        $('#edit_result').html(html);
                    }
                }
            });
        });

        $(document).on('click', ".edit", function() {
            $(this).addClass('edit-item-trigger-clicked');

            $('#editModal').modal('show');
            $('#editstartfinish').daterangepicker({
                cancelClass: "btn-light",
                applyButtonClasses: "btn-success",
                timePicker: true,
                timePicker24Hour: true,
                locale: {
                    format: 'MM/DD/YYYY H:mm'
                },
                dateLimit: {
                    'days': 1
                }
            });
            $('#editreststartfinish').daterangepicker({
                cancelClass: "btn-light",
                applyButtonClasses: "btn-success",
                timePicker: true,
                timePicker24Hour: true,
                locale: {
                    format: 'MM/DD/YYYY H:mm'
                },
                dateLimit: {
                    'days': 1
                }
            });
        });
        $('#editModal').on('show.bs.modal', function() {
            var el = $(".edit-item-trigger-clicked");
            var row = el.closest(".data-row");

            var id = el.data('id');
            document.getElementById('editForm').action = "/api/jobs/" + id;
            $.ajax({
                url: "/api/jobs/" + id,
                type: "GET",
                headers: {
                    "accept": "application/json",
                    "content-type": "application/json",
                },
                success: function(data) {
                    obj = data;
                    $('#edituser_id').val(obj.user_id);
                    $('#editstartfinish').data('daterangepicker').setStartDate(obj.start_datetime);
                    $('#editstartfinish').data('daterangepicker').setEndDate(obj.finish_datetime);
                    $('#editreststartfinish').data('daterangepicker').setStartDate(obj.rest_start);
                    $('#editreststartfinish').data('daterangepicker').setEndDate(obj.rest_finish);
                    if (obj.excuse > 0)
                        $("#editExcuse").prop("checked", true);
                    $('#editNote').val(obj.note);
                }
            });
        });
        $('#editModal').on('hide.bs.modal', function() {
            $('.edit-item-trigger-clicked').removeClass('edit-item-trigger-clicked')
            $("#editForm").trigger("reset");
            $('#edit_result').empty();
        });
        $('#excelExport').on('click', function() {
            var year = getParameterByName('year');
            var month = getParameterByName('month');
            if (!year) {
                alert('Please select a year!');
            } else if (!month) {
                alert('Please select a month!');
            } else {
                var userid = getParameterByName('driver');
                console.log(userid);
                if (userid) {
                    var url = "year=" + year + "&month=" + month + '&user_id=' + userid;
                } else {
                    var url = "year=" + year + "&month=" + month;
                }
                if (company_id > 0) {
                    url += "&company_id=" + company_id;
                }
                console.log(url);
                $.ajax({
                    xhrFields: {
                        responseType: 'blob',
                    },
                    type: 'GET',
                    url: '/api/excelexport?' + url,
                    success: function(result, status, xhr) {

                        var disposition = xhr.getResponseHeader('content-disposition');
                        var filename = '';
                        if (disposition && disposition.indexOf('attachment') !== -1) {
                            var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
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
                });
            }
        });
        $('#addBtn').on('click', function() {
            var form = $('#addForm').serializeArray();
            var start_datetime = $('#startfinish').data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm');
            var finish_datetime = $('#startfinish').data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm');

            var rest_start = $('#reststartfinish').data('daterangepicker').startDate.format('YYYY-MM-DD HH:mm');
            var rest_finish = $('#reststartfinish').data('daterangepicker').endDate.format('YYYY-MM-DD HH:mm');

            if ($('#excuse').is(':checked')) {
                form.push({
                    name: 'excuse',
                    value: 1
                });
            } else {
                form.push({
                    name: 'excuse',
                    value: 0
                });
            }
            form.push({
                name: 'start_datetime',
                value: start_datetime
            });
            form.push({
                name: 'finish_datetime',
                value: finish_datetime
            });
            form.push({
                name: 'rest_start',
                value: rest_start
            });
            form.push({
                name: 'rest_finish',
                value: rest_finish
            });
            console.log(form);
            $.ajax({
                url: '{{ route('storeWork') }}',
                type: 'POST',
                data: $.param(form),
                success: function(data) {
                    $('#addModal').modal('hide');
                    $('#calendar_table').DataTable().ajax.reload();
                },
                error: function(data) {
                    $('#addModal').modal('hide');
                    $('#calendar_table').DataTable().ajax.reload();
                }
            })
        });
        $('#filterBtn').on('click', function() {
            var url = document.location.protocol + "//" + document.location.hostname + document.location.pathname;
            if ($('#yearselect').val() != -1) {
                url += (url.indexOf('?') >= 0 ? '&' : '?') + 'year=' + $('#yearselect').val();
            }
            if ($('#monthselect').val() != -1) {
                url += (url.indexOf('?') >= 0 ? '&' : '?') + 'month=' + $('#monthselect').val();
            }
            if ($('#driverselect').val() != -1 && $('#driverselect').val() != undefined) {
                url += (url.indexOf('?') >= 0 ? '&' : '?') + ('driver=' + $('#driverselect').val());
            }
            if ($('#companyselect').val() != -1 && $('#companyselect').val() != undefined) {
                url += (url.indexOf('?') >= 0 ? '&' : '?') + ('company_id=' + $('#companyselect').val());
            }
            window.location.replace(url);
        });
        $('#clearBtn').on('click', function() {
            var url = document.location.protocol + "//" + document.location.hostname + document.location.pathname;
            window.location.replace(url);
        });

        var calendar_table = $('#calendar_table').DataTable({
            dom: '<"top"f<"clear">>rt<"bottom"ip<"clear">>',
            pageLength: 50,
            responsive: true,
            processing: true,
            serverSide: true,

            ajax: {
                url: "/api/jobs" + window.location.search,
                type: 'get',
            },

            columns: [

                {
                    data: 'id',
                    name: 'work_hours.id',
                    visible: false
                },
                {
                    data: 'date',
                    name: 'work_hours.date'
                },
                {
                    data: 'name',
                    name: 'users.name'
                },

                {
                    data: '6_18',
                    name: 'work_hours.6_18'
                },
                {
                    data: '18_22',
                    name: 'work_hours.18_22'
                },
                {
                    data: '22_0',
                    name: 'work_hours.22_0'
                },
                {
                    data: '0_6',
                    name: 'work_hours.0_6'
                },
                {
                    data: 'note',
                    name: 'work_notes.note'
                },
                {
                    data: 'edit',
                    name: 'edit',
                    orderable: false,
                    searchable: false
                }
            ],

        });

        function getParameterByName(name, url = window.location.href) {
            name = name.replace(/[\[\]]/g, '\\$&');
            var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
                results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, ' '));
        }
    </script>
@endsection
