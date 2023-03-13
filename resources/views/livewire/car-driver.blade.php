<div class="row justify-content-start">
    <div class="col-lg-4 col-md-6">
        <div class="form-group">
            <label for="car_type">Car Type</label>
            <select name="car_type" wire:click="change($event.target.value)" id="car_type" class="form-control booking-form" @if($test) disabled @endif>
                @foreach($cartypes as $car_type)
                    <option value={{ $car_type->id }} >{{ $car_type->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="form-group">
            <label for="car_type">Driver</label>
            <select name="driver_id" id="driver_id" class="form-control booking-form" @if($test) disabled @endif>
                <option value=''>Choose a Driver</option>
                @foreach($drivers as $driver)
                    <option value={{ $driver->id }} >{{ $driver->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
        <div class="col-lg-4 col-md-6">
            <div class="form-group">
                <label for="car_type">Car</label>
                <select name="car_id" id="car_id" class="form-control booking-form" @if($test) disabled @endif>
                    <option selected value=''>Choose a Car</option>
                    @foreach($cars as $car)
                        <option value={{ $car->id }} >{{ $car->plate }}</option>
                    @endforeach
                </select>
            </div>
        </div>
</div>