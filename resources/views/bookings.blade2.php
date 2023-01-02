@extends('test')

@if (app('request')->input('status') != '')
@if (app('request')->input('status') == 0)
@section('title', 'Bookings - Waiting for confirmation')
@elseif (app('request')->input('status') == 1)
@section('title', 'Bookings - Order confirmed')
@elseif (app('request')->input('status') == 2)
@section('title', 'Bookings - To be delivered')
@elseif (app('request')->input('status') == 3)
@section('title', 'Bookings - Will be delivered')
@elseif (app('request')->input('status') == 4)
@section('title', 'Bookings - Delivered')
@elseif (app('request')->input('status') == 5)
@section('title', 'Bookings - Cancelled')
@elseif (app('request')->input('status') == 6)
@section('title', 'Bookings - Rejected')
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
                <th>Sender</th>
                <th>Customer</th>
                <th>Created At</th>
                <th>Edit</th>
            </tr>
        </thead>
    </table>
</div>
<form role="form" action="" id="bookings_form">
    <div class="modal top fade" id="edit-modal" tabindex="-1" aria-labelledby="editmodalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="true">
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
                                <input type="text" class="form-control" id="track_code" placeholder="Track Code" readonly>
                            </div>
                        </div>
                    </div>
                    <hr class="mt-2 mb-3" />
                    <div class="row gx-1 justify-content-start">

                        <div class="col-lg-2 col-md-6">
                            <div class="form-group">
                                <label for="from">From Zip Code</label>
                                <input type="text" class="form-control" id="from" placeholder="From Zip Code" readonly>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <div class="form-group">
                                <label for="from_lat">From Latitude</label>
                                <input type="text" class="form-control" id="from_lat" placeholder="From Latitude" readonly>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <div class="form-group">
                                <label for="from_lng">From Longitude</label>
                                <input type="text" class="form-control" id="from_lng" placeholder="From Longitude" readonly>
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
                                <input type="text" class="form-control" id="to_lat" placeholder="To Latitude" readonly>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <div class="form-group">
                                <label for="to_lng">To Longitude</label>
                                <input type="text" class="form-control" id="to_lng" placeholder="To Longitude" readonly>
                            </div>
                        </div>

                    </div>
                    <hr class="mt-2 mb-3" />
                    <div class="row justify-content-start">

                        <div class="col-lg-2 col-md-6">
                            <div class="form-group">
                                <label for="from_name">From Name</label>
                                <input type="text" class="form-control" id="from_name" placeholder="From Name" readonly>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-6">
                            <div class="form-group">
                                <label for="from_address">From Address</label>
                                <input type="text" class="form-control" id="from_address" placeholder="From Address" readonly>
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
                                <input type="text" class="form-control" id="to_address" placeholder="To Address" readonly>
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
                                <label for="delivery_type">Delivery Type</label>
                                <select class="form-control booking-form" name="delivery_type" id="delivery_type">
                                    <option value="time_courier">Time Courier</option>
                                    <option value="express_courier">Express Courier</option>
                                    <option value="two_hour_delivery">Two Hour Delivery</option>
                                    <option value="three_hour_delivery">Three Hour Delivery</option>
                                    <option value="six_hour_delivery">Six Hour Delivery</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <label for="delivery_date">Delivery Date</label>
                                <input type="text" class="form-control booking-form" name="delivery_date" id="delivery_date" placeholder="Delivery Date">
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <label for="delivery_time">Delivery Time</label>
                                <input type="text" class="form-control booking-form" name="delivery_time" id="delivery_time" placeholder="Delivery Time">
                            </div>
                        </div>

                    </div>
                    <hr class="mt-2 mb-3" />
                    <div class="row justify-content-start">

                        <div class="col-lg-4 col-md-12">
                            <div class="form-group">
                                <label for="sender_name">Sender Name</label>
                                <input type="text" class="form-control booking-form" name="sender_name" id="sender_name" placeholder="Sender Name">
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <label for="sender_phone">Sender Phone</label>
                                <input type="text" class="form-control booking-form" name="sender_phone" id="sender_phone" placeholder="Sender Phone">
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <label for="sender_mail">Sender Mail</label>
                                <input type="text" class="form-control booking-form" name="sender_mail" id="sender_mail" placeholder="Sender Mail">
                            </div>
                        </div>

                    </div>
                    <hr class="mt-2 mb-3" />
                    <div class="row justify-content-start">

                        <div class="col-lg-4 col-md-12">
                            <div class="form-group">
                                <label for="customer_name">Customer Name</label>
                                <input type="text" class="form-control booking-form" name="customer_name" id="customer_name" placeholder="Customer Name">
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <label for="customer_phone">Customer Phone</label>
                                <input type="text" class="form-control booking-form" name="customer_phone" id="customer_phone" placeholder="Customer Phone">
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <label for="customer_mail">Customer Mail</label>
                                <input type="text" class="form-control booking-form" name="customer_mail" id="customer_mail" placeholder="Customer Mail">
                            </div>
                        </div>

                    </div>
                    <hr class="mt-2 mb-3" />
                    <div class="row justify-content-start">

                        <div class="col-12">
                            <div class="form-group">
                                <label for="company_name">Company Name</label>
                                <input type="text" class="form-control booking-form" name="company_name" id="company_name" placeholder="Company Name">
                            </div>
                        </div>

                    </div>
                    <hr class="mt-2 mb-3" />
                    <div class="row justify-content-start">

                        <div class="col-12">
                            <div class="table-responsive">
                                <table id="packets_table" class="table table-striped table-sm" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Cubic Meters</th>
                                            <th>KG</th>
                                            <th>Type</th>
                                            <th>Price</th>
                                            <th>Edit</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th colspan="1" style="text-align:left">Toplamlar:</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row justify-content-between">
                        <div class="col-lg-1 mr-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" value="0">
                                <label class="form-check-label" for="0">
                                    Waiting for confirmation
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-1">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" value="1">
                                <label class="form-check-label" for="1">
                                    Order confirmed
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-1">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" value="2">
                                <label class="form-check-label" for="2">
                                    To be delivered
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-1">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" value="3">
                                <label class="form-check-label" for="3">
                                    Will be delivered
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-1">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" value="4">
                                <label class="form-check-label" for="4">
                                    Delivered
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-1">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" value="5">
                                <label class="form-check-label" for="5">
                                    Cancelled
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-1">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" value="6">
                                <label class="form-check-label" for="6">
                                    Rejected
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
<div class="modal top fade" id="packetEditModal" tabindex="-1" aria-labelledby="packetEditModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="true">
    <div class="modal-dialog  ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="packetEditModalLabel">Edit Packet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <span id="modal_result"></span>
                <form id="packet_form" method="POST">
                    <input type="hidden" name="id" id="packetid">
                    <div class="row ">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="cubic_meters">Cubic Meters</label>
                                <input type="text" class="form-control" name="cubic_meters" id="cubic_meters">
                            </div>
                        </div>

                    </div>
                    <div class="row ">

                        <div class="col-12">
                            <div class="form-group">
                                <label for="kg">KG</label>
                                <input type="text" class="form-control" name="kg" id="kg">
                            </div>
                        </div>

                    </div>
                    <div class="row ">

                        <div class="col-12">
                            <div class="form-group">
                                <label for="type">Type</label>
                                <select class="form-select" name="type" id="type">
                                    <option value="box">Box</option>
                                    <option value="lastpall">LastPall</option>
                                    <option value="annat_format">Annat Format</option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="row ">

                        <div class="col-12">
                            <div class="form-group">
                                <label for="price">Price</label>
                                <input type="text" class="form-control" name="price" id="price">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Close
                </button>
                <button type="button" class="btn btn-primary" id="packetSaveBtn">Save changes</button>
            </div>
        </div>
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
                <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true" aria-label="Close">&times;</button>
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
                $('#delivery_type').val(obj.delivery_type)
                $('#delivery_date').val(obj.delivery_date)
                $('#delivery_time').val(obj.delivery_time)
                $('#sender_name').val(obj.sender_name)
                $('#sender_phone').val(obj.sender_phone)
                $('#sender_mail').val(obj.sender_mail)
                $('#customer_name').val(obj.customer_name)
                $('#customer_phone').val(obj.customer_phone)
                $('#customer_mail').val(obj.customer_mail)
                $('#company_name').val(obj.company_name)
                var dataTable = $('#packets_table').DataTable({
                    "footerCallback": function(row, data, start, end, display) {
                        var api = this.api(),
                            data;

                        var intVal = function(i) {
                            return typeof i === 'string' ?
                                i.replace(/[\$,]/g, '') * 1 :
                                typeof i === 'number' ?
                                i : 0;
                        };

                        cubicmeter_total = api.column(1)
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);

                        kg_total = api.column(2)
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);
                        price_total = api.column(4)
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);
                        $(api.column(1).footer()).html(cubicmeter_total);
                        $(api.column(2).footer()).html(kg_total);
                        $(api.column(4).footer()).html(price_total);

                    },
                    buttons: [{
                        footer: true
                    }],
                    "dom": 'tp',
                    "bDestroy": true,
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "order": [],
                    "ajax": {
                        url: "/api/bookingspackets/" + id,
                        type: "GET",
                        headers: {
                            "accept": "application/json",
                            "content-type": "application/json",
                            "authorization": "Bearer " + token
                        },
                    },
                    "fnDrawCallback": function(oSettings) {
                        if (obj.status == 4 || obj.status == 5 || obj.status == 6) {
                            $('.booking-form').addClass('disabled');
                            $('.booking-form').prop('disabled', true);

                        }
                    },
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'cubic_meters',
                            name: 'cubic_meters'
                        },
                        {
                            data: 'kg',
                            name: 'kg'
                        },
                        {
                            data: 'type',
                            name: 'type'
                        },
                        {
                            data: 'price',
                            name: 'price'
                        },
                        {
                            data: 'edit',
                            name: 'edit'
                        }
                    ]

                });

            }
        });
    });

    $(document).on('click', ".packet-edit", function() {
        $(this).addClass('packet-item-trigger-clicked');

        $('#packetEditModal').modal('show');
    });
    $(document).on('click', ".packet-delete", function() {
        $(this).addClass('packet-delete-item-trigger-clicked');

        $('#confirm_modal').modal('show');
    });
    $('.delete-packet').on('click', function() {
        var el = $(".packet-delete-item-trigger-clicked");
        var id = el.data('id');
        $.ajax({
            url: '/api/bookingspacketsaction',
            type: "POST",
            data: 'id=' + id + '&action=delete',
            success: function(data) {
                $('#packets_table').DataTable().ajax.reload();
                $('#confirm_modal').modal('hide');
            },
        });
    });
    $('#packetEditModal').on('show.bs.modal', function() {
        var el = $(".packet-item-trigger-clicked");
        var row = el.closest(".data-row");

        var id = el.data('id');
        var token = Cookies.get('token');
        console.log(el);
        document.getElementById('packet_form').action = "/api/bookingspacketsaction";
        $.ajax({
            url: "/api/packet/" + id,
            type: "GET",
            headers: {
                "accept": "application/json",
                "content-type": "application/json",
                "authorization": "Bearer " + token
            },
            success: function(data) {
                obj = JSON.parse(data);
                $('#cubic_meters').val(obj.cubic_meters)
                $('#kg').val(obj.kg)
                $('#type').val(obj.type)
                $('#price').val(obj.price)
                $('#packetid').val(obj.id)

            }
        });
    });
    $('#packetEditModal').on('hide.bs.modal', function() {
        $('.packet-item-trigger-clicked').removeClass('packet-item-trigger-clicked')
        $("#packet_form").trigger("reset");
        $('#modal_result').empty();
    });

    $('#edit-modal').on('hide.bs.modal', function() {
        $('.edit-item-trigger-clicked').removeClass('edit-item-trigger-clicked')
        $("#edit-form").trigger("reset");
        $('#packets_table').DataTable().clear().destroy();
        $('#modal_result').empty();
    });

    $('#packetSaveBtn').on('click', function() {
        var formdata = $('#packet_form').serialize() + '&action=edit';
        console.log(formdata);
        $.ajax({
            headers: {
                "Authorization": "Bearer " + Cookies.get('token')
            },
            url: document.getElementById('packet_form').action,
            type: "POST",
            data: formdata,
            success: function(data) {
                $('#packets_table').DataTable().ajax.reload();
                $('#packetEditModal').modal('hide');
            },
        });
    })
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
                $.get('{{ route('
                    count ', '
                    0 ') }}').then(function(response) {
                    response = response.replace(/\s/g, '');
                    $('#count0').html('Waiting for confirmation(' +
                        response + ')')
                });
                $.get('{{ route('
                    count ', '
                    1 ') }}').then(function(response) {
                    response = response.replace(/\s/g, '');
                    $('#count1').html('Order confirmed(' +
                        response + ')')
                });
                $.get('{{ route('
                    count ', '
                    2 ') }}').then(function(response) {
                    response = response.replace(/\s/g, '');
                    $('#count2').html('To be delivered(' +
                        response + ')')
                });
                $.get('{{ route('
                    count ', '
                    3 ') }}').then(function(response) {
                    response = response.replace(/\s/g, '');
                    $('#count3').html('Will be delivered(' +
                        response + ')')
                });
                $.get('{{ route('
                    count ', '
                    4 ') }}').then(function(response) {
                    response = response.replace(/\s/g, '');
                    $('#count4').html('Delivered(' +
                        response + ')')
                });
                $.get('{{ route('
                    count ', '
                    5 ') }}').then(function(response) {
                    response = response.replace(/\s/g, '');
                    $('#count5').html('Cancelled(' +
                        response + ')')
                });
                $.get('{{ route('
                    count ', '
                    6 ') }}').then(function(response) {
                    response = response.replace(/\s/g, '');
                    $('#count6').html('Rejected(' +
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
                name: 'id',
                visible: false
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
                data: 'sender_name',
                name: 'sender_name'
            },
            {
                data: 'customer_name',
                name: 'customer_name'
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
