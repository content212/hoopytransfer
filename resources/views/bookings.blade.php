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
                                    <input type="text" class="form-control booking-form" name="delivery_date"
                                        id="delivery_date" placeholder="Delivery Date">
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label for="delivery_time">Delivery Time</label>
                                    <input type="text" class="form-control booking-form" name="delivery_time"
                                        id="delivery_time" placeholder="Delivery Time">
                                </div>
                            </div>

                        </div>
                        <hr class="mt-2 mb-3" />
                        <div class="row justify-content-start">

                            <div class="col-lg-4 col-md-12">
                                <div class="form-group">
                                    <label for="sender_name">Sender Name</label>
                                    <input type="text" class="form-control booking-form" name="sender_name" id="sender_name"
                                        placeholder="Sender Name">
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label for="sender_phone">Sender Phone</label>
                                    <input type="text" class="form-control booking-form" name="sender_phone"
                                        id="sender_phone" placeholder="Sender Phone">
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label for="sender_mail">Sender Mail</label>
                                    <input type="text" class="form-control booking-form" name="sender_mail" id="sender_mail"
                                        placeholder="Sender Mail">
                                </div>
                            </div>

                        </div>
                        <hr class="mt-2 mb-3" />
                        <div class="row justify-content-start">

                            <div class="col-lg-4 col-md-12">
                                <div class="form-group">
                                    <label for="customer_name">Customer Name</label>
                                    <input type="text" class="form-control booking-form" name="customer_name"
                                        id="customer_name" placeholder="Customer Name">
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label for="customer_phone">Customer Phone</label>
                                    <input type="text" class="form-control booking-form" name="customer_phone"
                                        id="customer_phone" placeholder="Customer Phone">
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label for="customer_mail">Customer Mail</label>
                                    <input type="text" class="form-control booking-form" name="customer_mail"
                                        id="customer_mail" placeholder="Customer Mail">
                                </div>
                            </div>

                        </div>
                        <hr class="mt-2 mb-3" />
                        <div class="row justify-content-start">

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="company_name">Company Name</label>
                                    <input type="text" class="form-control booking-form" name="company_name"
                                        id="company_name" placeholder="Company Name">
                                </div>
                            </div>

                        </div>
                        <hr class="mt-2 mb-3" />
                        <div class="row">
                            <div class="col-9">
                                <div class="row d-flex justify-content-start">


                                    <label for="bp_table">Box or Pocket</label>
                                    <div class="table-responsive">
                                        <table id="bp_table" class="table table-striped table-sm" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Width</th>
                                                    <th>Size</th>
                                                    <th>Height</th>
                                                    <th>Weight</th>
                                                    <th>Edit</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="6" style="text-align:left"></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>


                                </div>
                                <div class="row justify-content-start">


                                    <label for="lastpall_table">Lastpall</label>
                                    <div class="table-responsive">
                                        <table id="lastpall_table" class="table table-striped table-sm" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Width</th>
                                                    <th>Size</th>
                                                    <th>Height</th>
                                                    <th>Weight</th>
                                                    <th>Edit</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="6" style="text-align:left"></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>


                                </div>
                                <div class="row justify-content-start">


                                    <label for="af_table">Annat Format</label>
                                    <div class="table-responsive">
                                        <table id="af_table" class="table table-striped table-sm" style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Width</th>
                                                    <th>Size</th>
                                                    <th>Height</th>
                                                    <th>Weight</th>
                                                    <th>Edit</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="6" style="text-align:left"></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="card border border-2 card-body" style="height: 50%;width: 100%;font-size: 18px"
                                    id="sideCard">
                                    <div class="row align-self-center text-left">
                                        <b>YOUR ORDER</b>
                                    </div>
                                    <div class="row align-self-center text-left">
                                        Sub total 0
                                    </div>
                                    <div class="row align-self-center text-left">
                                        Discount 0
                                    </div>
                                    <div class="row align-self-center text-left">
                                        Tax 0
                                    </div>
                                    <div class="row align-self-center text-left">
                                        <b>TOTAL 0</b>
                                    </div>
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
    <div class="modal top fade" id="packetDetailEditModal" tabindex="-1" aria-labelledby="packetEditModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="true">
        <div class="modal-dialog  ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="packetEditModalLabel">Edit Packet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="packetdetail_result"></span>
                    <form id="packetdetail_form" method="POST">
                        <input type="hidden" name="id" id="packetdetailid">
                        <div class="row ">
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label for="width">Width</label>
                                    <input type="text" class="form-control dimension" name="width" id="width">
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label for="size">Size</label>
                                    <input type="text" class="form-control dimension" name="size" id="size">
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label for="height">Height</label>
                                    <input type="text" class="form-control dimension" name="height" id="height">
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <label for="cubic_meters">Cubic Meters</label>
                                    <input type="text" class="form-control" name="cubic_meters" id="cubic_meters"
                                        readonly>
                                </div>
                            </div>

                        </div>
                        <hr class="mt-2 mb-3" />
                        <div class="row ">

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="weight">Weight</label>
                                    <input type="text" class="form-control" name="weight" id="weight">
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" id="packetDetailSaveBtn">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal top fade" id="packetPriceEditModal" tabindex="-1" aria-labelledby="packetPriceEditModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="true">
        <div class="modal-dialog  ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="packetPriceEditModalLabel">Edit Packet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="packet_result"></span>
                    <form id="packet_form" method="POST">
                        <input type="hidden" name="id" id="packetid">
                        <div class="row ">

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="price">Sub Total</label>
                                    <input type="text" class="form-control price" name="price" id="price">
                                </div>
                            </div>

                        </div>
                        <div class="row ">
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="discount_rate">Discount Rate</label>
                                    <input type="text" class="form-control price" name="discount_rate" id="discount_rate">
                                </div>
                            </div>
                            <div class="col-9">
                                <div class="form-group">
                                    <label for="discount">Discount</label>
                                    <input type="text" class="form-control" id="discount" disabled>
                                </div>
                            </div>
                        </div>
                        <hr class="mt-2 mb-3" />
                        <div class="row ">
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="tax_rate">Tax Rate</label>
                                    <input type="text" class="form-control price" name="tax_rate" id="tax_rate">
                                </div>
                            </div>
                            <div class="col-9">
                                <div class="form-group">
                                    <label for="tax">Tax</label>
                                    <input type="text" class="form-control" id="tax" disabled>
                                </div>
                            </div>
                        </div>
                        <hr class="mt-2 mb-3" />
                        <div class="row ">

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="final_price">TOTAL</label>
                                    <input type="text" class="form-control" id="final_price" disabled>
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
            var sumsubTotal = 0;
            var sumDiscount = 0;
            var sumTax = 0;
            var sumTotal = 0;
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
                    var bp_dataTable = $('#bp_table').DataTable({
                        buttons: [{
                            footer: true
                        }],
                        "dom": 't',
                        "bDestroy": true,
                        "processing": true,
                        "serverSide": true,
                        "responsive": true,
                        "ajax": {
                            url: "/api/bookingspackets/" + id + "/box",
                            type: "GET",
                            headers: {
                                "accept": "application/json",
                                "content-type": "application/json",
                                "authorization": "Bearer " + token
                            },
                        },
                        "fnDrawCallback": function() {
                            var api = this.api()
                            var json = api.ajax.json();

                            sumsubTotal = json.subtotal ? parseFloat(json.subtotal, 10) : 0;
                            sumDiscount = json.discount ? parseFloat(json.discount, 10) : 0;
                            sumTax = json.tax ? parseFloat(json.tax, 10) : 0;
                            sumTotal = json.total ? parseFloat(json.total, 10) : 0;
                            var html =
                                '<div class="row">' +
                                '<div class="col-lg-2 col-md-12"> Subtotal : ' + json
                                .subtotal +
                                '</div>' +
                                '<div class="col-lg-2 col-md-6">Discount : ' + json
                                .discount +
                                '</div>' +
                                '<div class="col-lg-2 col-md-6">Tax : ' + json.tax +
                                '</div>' +
                                '<div class="col-lg-2 col-md-6">TOTAL : ' + json.total +
                                '</div>';
                            if (json.packet_id != -1)
                                html += '<div class="col-lg-2 col-md-6"><a data-id="' + json
                                .packet_id +
                                '" class="packet-price-edit booking-form m-1 btn btn-primary btn-sm">View</a></div>';
                            $(api.column(1).footer()).html(html + '</div>');
                        },
                        columns: [{
                                data: 'id',
                                name: 'id'
                            },
                            {
                                data: 'width',
                                name: 'width'
                            },
                            {
                                data: 'size',
                                name: 'size'
                            },
                            {
                                data: 'height',
                                name: 'height'
                            },
                            {
                                data: 'weight',
                                name: 'weight'
                            },
                            {
                                data: 'edit',
                                name: 'edit'
                            }
                        ]

                    });
                    var lastpall_dataTable = $('#lastpall_table').DataTable({
                        buttons: [{
                            footer: true
                        }],
                        "dom": 't',
                        "bDestroy": true,
                        "processing": true,
                        "serverSide": true,
                        "responsive": true,
                        "order": [],
                        "ajax": {
                            url: "/api/bookingspackets/" + id + "/lastpall",
                            type: "GET",
                            headers: {
                                "accept": "application/json",
                                "content-type": "application/json",
                                "authorization": "Bearer " + token
                            },
                        },
                        "fnDrawCallback": function() {
                            var api = this.api()
                            var json = api.ajax.json();
                            sumsubTotal += json.subtotal ? parseFloat(json.subtotal, 10) :
                                0;
                            sumDiscount += json.discount ? parseFloat(json.discount, 10) :
                                0;
                            sumTax += json.tax ? parseFloat(json.tax, 10) : 0;
                            sumTotal += json.total ? parseFloat(json.total, 10) : 0;
                            var html =
                                '<div class="row">' +
                                '<div class="col-lg-2 col-md-12"> Subtotal : ' + json
                                .subtotal +
                                '</div>' +
                                '<div class="col-lg-2 col-md-6">Discount : ' + json
                                .discount +
                                '</div>' +
                                '<div class="col-lg-2 col-md-6">Tax : ' + json.tax +
                                '</div>' +
                                '<div class="col-lg-2 col-md-6">TOTAL : ' + json.total +
                                '</div>';
                            if (json.packet_id != -1)
                                html += '<div class="col-lg-2 col-md-6"><a data-id="' + json
                                .packet_id +
                                '" class="packet-price-edit booking-form m-1 btn btn-primary btn-sm">View</a></div>';
                            $(api.column(1).footer()).html(html + '</div>');
                        },
                        columns: [{
                                data: 'id',
                                name: 'id'
                            },
                            {
                                data: 'width',
                                name: 'width'
                            },
                            {
                                data: 'size',
                                name: 'size'
                            },
                            {
                                data: 'height',
                                name: 'height'
                            },
                            {
                                data: 'weight',
                                name: 'weight'
                            },
                            {
                                data: 'edit',
                                name: 'edit'
                            }
                        ]

                    });
                    var af_dataTable = $('#af_table').DataTable({
                        buttons: [{
                            footer: true
                        }],
                        "dom": 't',
                        "bDestroy": true,
                        "processing": true,
                        "serverSide": true,
                        "responsive": true,
                        "order": [],
                        "ajax": {
                            url: "/api/bookingspackets/" + id + "/annat_format",
                            type: "GET",
                            headers: {
                                "accept": "application/json",
                                "content-type": "application/json",
                                "authorization": "Bearer " + token
                            },
                        },
                        "fnDrawCallback": function() {
                            var api = this.api()
                            var json = api.ajax.json();
                            sumsubTotal += json.subtotal ? parseFloat(json.subtotal, 10) :
                                0;
                            sumDiscount += json.discount ? parseFloat(json.discount, 10) :
                                0;
                            sumTax += json.tax ? parseFloat(json.tax, 10) : 0;
                            sumTotal += json.total ? parseFloat(json.total, 10) : 0;
                            var sideHtml =
                                '<div class="row align-self-center text-left"> <b> YOUR ORDER </b> </div> <div class = "row align-self-center text-left" >Sub total ' +
                                sumsubTotal +
                                '</div> <div class = "row align-self-center text-left" >Discount ' +
                                sumDiscount +
                                '</div> <div class = "row align-self-center text-left" >Tax ' +
                                sumTax +
                                '</div> <div class = "row align-self-center text-left" ><b> TOTAL ' +
                                sumTotal + '</b> </div>';
                            $('#sideCard').html(sideHtml);
                            var html =
                                '<div class="row">' +
                                '<div class="col-lg-2 col-md-12"> Subtotal : ' + json
                                .subtotal +
                                '</div>' +
                                '<div class="col-lg-2 col-md-6">Discount : ' + json
                                .discount +
                                '</div>' +
                                '<div class="col-lg-2 col-md-6">Tax : ' + json.tax +
                                '</div>' +
                                '<div class="col-lg-2 col-md-6">TOTAL : ' + json.total +
                                '</div>';
                            if (json.packet_id != -1)
                                html += '<div class="col-lg-2 col-md-6"><a data-id="' + json
                                .packet_id +
                                '" class="packet-price-edit booking-form m-1 btn btn-primary btn-sm">View</a></div>';
                            $(api.column(1).footer()).html(html + '</div>');
                        },
                        columns: [{
                                data: 'id',
                                name: 'id'
                            },
                            {
                                data: 'width',
                                name: 'width'
                            },
                            {
                                data: 'size',
                                name: 'size'
                            },
                            {
                                data: 'height',
                                name: 'height'
                            },
                            {
                                data: 'weight',
                                name: 'weight'
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

        $(document).on('input', '.dimension', function() {
            var width = $('#width') ? parseInt($('#width').val(), 10) : 0;
            var size = $('#size') ? parseInt($('#size').val(), 10) : 0;
            var height = $('#height') ? parseInt($('#height').val(), 10) : 0;

            var cubicmeters = (width * size * height) / parseFloat(1000000);

            $('#cubic_meters').val(cubicmeters);
        });
        $(document).on('input', '.price', function() {
            var subtotal = $('#price') ? parseInt($('#price').val(), 10) : 0;
            var d_rate = $('#discount_rate') ? parseInt($('#discount_rate').val(), 10) : 0;
            var t_rate = $('#tax_rate') ? parseInt($('#tax_rate').val(), 10) : 0;

            var discount = (subtotal * d_rate) / 100.00;
            var tax = ((subtotal - discount) * t_rate) / 100.00;

            var total = subtotal - discount + tax;

            $('#discount').val('-' + discount);
            $('#tax').val(tax);
            $('#final_price').val(total);
        });
        $(document).on('click', ".packet-edit", function() {
            $(this).addClass('packet-item-trigger-clicked');

            $('#packetDetailEditModal').modal('show');
        });
        $(document).on('click', ".packet-price-edit", function() {
            $(this).addClass('packet-price-trigger-clicked');

            $('#packetPriceEditModal').modal('show');
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
        $('#packetPriceEditModal').on('show.bs.modal', function() {
            var el = $(".packet-price-trigger-clicked");
            var id = el.data('id');
            var token = Cookies.get('token');
            $('#packetid').val(id);
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
                    $('#price').val(obj.price);
                    $('#tax').val(obj.tax);
                    $('#tax_rate').val(obj.tax_rate);
                    $('#discount').val(obj.discount);
                    $('#discount_rate').val(obj.discount_rate);
                    $('#final_price').val(obj.final_price);

                }
            });
        });
        $('#packetDetailEditModal').on('show.bs.modal', function() {
            var el = $(".packet-item-trigger-clicked");

            var id = el.data('id');
            var token = Cookies.get('token');
            $('#packetdetailid').val(id);
            $.ajax({
                url: "/api/packetdetail/" + id,
                type: "GET",
                headers: {
                    "accept": "application/json",
                    "content-type": "application/json",
                    "authorization": "Bearer " + token
                },
                success: function(data) {
                    obj = JSON.parse(data);
                    $('#width').val(obj.width);
                    $('#size').val(obj.size);
                    $('#height').val(obj.height);
                    $('#weight').val(obj.weight);
                    $('#cubic_meters').val(obj.cubic_meters);

                }
            });
        });
        $('#packetDetailEditModal').on('hide.bs.modal', function() {
            $('.packet-item-trigger-clicked').removeClass('packet-item-trigger-clicked')
            $("#packetdetail_form").trigger("reset");
            $('#modal_result').empty();
        });

        $('#edit-modal').on('hide.bs.modal', function() {
            $('.edit-item-trigger-clicked').removeClass('edit-item-trigger-clicked')
            $("#edit-form").trigger("reset");
            $('#packets_table').DataTable().clear().destroy();
            $('#modal_result').empty();
        });

        $('#packetSaveBtn').on('click', function() {
            var form = $('#packet_form').serialize();
            var id = $('#packetid').val();
            console.log(id);
            $.ajax({
                url: '/api/packet/' + id,
                type: 'POST',
                data: form,
                headers: {
                    "Authorization": "Bearer " + Cookies.get('token')
                },
                success: function(data) {
                    $('#bp_table').DataTable().ajax.reload();
                    $('#lastpall_table').DataTable().ajax.reload();
                    $('#af_table').DataTable().ajax.reload();
                    $('#packetPriceEditModal').modal('hide');
                },
                error: function(data) {
                    if (data.responseJSON.message) {

                        var errors = $.parseJSON(data.responseText);
                        html = '<div class="alert alert-danger">';
                        $.each(data.responseJSON.message, function(key, value) {
                            html += '<p>' + value[0] + '</p>';
                        });
                        html += '</div>';
                        $('#packet_result').html(html);
                    }
                }
            });
        })
        $('#packetDetailSaveBtn').on('click', function() {
            var form = $('#packetdetail_form').serialize();
            var id = $('#packetdetailid').val();
            $.ajax({
                url: '/api/packetdetail/' + id,
                type: 'POST',
                data: form,
                headers: {
                    "Authorization": "Bearer " + Cookies.get('token')
                },
                success: function(data) {
                    $('#bp_table').DataTable().ajax.reload();
                    $('#lastpall_table').DataTable().ajax.reload();
                    $('#af_table').DataTable().ajax.reload();
                    $('#packetDetailEditModal').modal('hide');
                },
                error: function(data) {
                    if (data.responseJSON.message) {

                        var errors = $.parseJSON(data.responseText);
                        html = '<div class="alert alert-danger">';
                        $.each(data.responseJSON.message, function(key, value) {
                            html += '<p>' + value[0] + '</p>';
                        });
                        html += '</div>';
                        $('#packetdetail_result').html(html);
                    }
                }
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
                    $.get('{{ route('count', '0') }}').then(function(response) {
                        response = response.replace(/\s/g, '');
                        $('#count0').html('Waiting for confirmation(' +
                            response + ')')
                    });
                    $.get('{{ route('count', '1') }}').then(function(response) {
                        response = response.replace(/\s/g, '');
                        $('#count1').html('Order confirmed(' +
                            response + ')')
                    });
                    $.get('{{ route('count', '2') }}').then(function(response) {
                        response = response.replace(/\s/g, '');
                        $('#count2').html('To be delivered(' +
                            response + ')')
                    });
                    $.get('{{ route('count', '3') }}').then(function(response) {
                        response = response.replace(/\s/g, '');
                        $('#count3').html('Will be delivered(' +
                            response + ')')
                    });
                    $.get('{{ route('count', '4') }}').then(function(response) {
                        response = response.replace(/\s/g, '');
                        $('#count4').html('Delivered(' +
                            response + ')')
                    });
                    $.get('{{ route('count', '5') }}').then(function(response) {
                        response = response.replace(/\s/g, '');
                        $('#count5').html('Cancelled(' +
                            response + ')')
                    });
                    $.get('{{ route('count', '6') }}').then(function(response) {
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
