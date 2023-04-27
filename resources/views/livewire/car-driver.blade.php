<div>
    <div class="row justify-content-start">
        <div class="col-lg-4 col-md-6">
            <div class="form-group">
                <label for="car_type">Car Type</label>
                <select name="car_type" wire:click="carTypeChange($event.target.value)" id="car_type"
                    class="form-control booking-form" @if ($test) disabled @endif>
                    <option @if ($car_type == 'request') selected @endif value='request'>REQUEST</option>
                    @if ($car_type != 'request')
                        @foreach ($cartypes as $car_type2)
                            <option @if ($car_type == $car_type2->id) selected @endif value={{ $car_type2->id }}>{{ $car_type2->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="form-group">
                <label for="car_type">Driver</label>
                <select name="driver_id" id="driver_id" class="form-control booking-form"
                    @if ($test) disabled @endif>
                    <option value=''>Choose a Driver</option>
                    @foreach ($drivers as $driver)
                        <option value={{ $driver->id }}>{{ $driver->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="form-group">
                <label for="car_type">Car</label>
                <select name="car_id" id="car_id" class="form-control booking-form"
                    @if ($test) disabled @endif>
                    <option selected value=''>Choose a Car</option>
                    @foreach ($cars as $car)
                        <option value={{ $car->id }}>{{ $car->plate }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    @if ($car_type == 'request')
        <hr class="mt-2 mb-3" />
        <div class="row justify-content-start">
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="text" class="form-control" id="price" name="price" value="{{ $booking->data->discount_price }}">
                </div>
            </div>
        </div>
    @endif
</div>
