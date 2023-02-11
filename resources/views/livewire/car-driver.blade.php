<div class="row justify-content-start">
    <div class="col-lg-4 col-md-6">
        <div class="form-group">
            <label for="car_type">Car Type</label>
            <select name="car_type" wire:click="changeEvent($event.target.value)" id="car_type" class="form-control booking-form">
                @foreach($cartypes as $car_type)
                    <option value={{ $car_type->id }} >{{ $car_type->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    @if(count($drivers) > 0)
    <div class="col-lg-4 col-md-6">
        <div class="form-group">
            <label for="car_type">Car Type</label>
            <select name="driver_id" id="driver_id" class="form-control booking-form">
                <option value=''>Choose a Driver</option>
                @foreach($drivers as $driver)
                    <option value={{ $driver->id }} >{{ $driver->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    @endif
</div>