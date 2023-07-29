<div class="row" id="cardriver-container">
    <div class="col-md-4">
        <div class="form-group">
            <label for="cartype">Service</label>
            <select name="cartype" id="cartype" class="form-control">
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
            <select name="car" id="car" class="form-control">
                <option value="">Choose a Car</option>
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="driver">Driver</label>
            <select name="driver" class="form-control">
                <option value="">Choose a Driver</option>
                @foreach (App\Models\Driver::select('drivers.id','users.name','users.surname')->join('users', 'users.id', '=', 'drivers.user_id')->get() as $driver)
                    <option value="{{$driver->id}}">{{ $driver->name }} {{$driver->surname}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

@section('script')
    @parent
    <script>
        $("#cartype").change(function() {
            var id = parseInt($(this).val()) || 0;
            if (id>0) {
                var token = Cookies.get('token');
                $.ajax({
                    url: "/api/carsbytype/" + id,
                    type: "GET",
                    headers: {
                        "accept": "application/json",
                        "content-type": "application/json",
                        "authorization": "Bearer " + token
                    },
                    success: function(data) {
                        $("#car").empty();
                        $("#car").append('<option>Choose a Car</option>');
                        data.forEach(function(item) {
                            $("#car").append('<option value="' + item.id + '">'+ item.plate +'</option>');
                        });
                    }
                });
            } else {
                $("#car").empty();
                $("#car").append('<option>Choose a Car</option>');
            }
        });
    </script>
@stop