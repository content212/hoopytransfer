<div>
    <div class="form-group">
        <label for="country">Country</label>
        <select name="country" wire:click="changeEvent($event.target.value)" id="country" class="form-control">
            <option value=''>Choose a country</option>
            @foreach($countries as $country)
                <option value={{ $country->id }} >{{ $country->name }}</option>
            @endforeach
        </select>
    </div>
    @if(count($states) > 0)
        <div class="form-group">
            <label for="state">State</label>
            <select name="state" id="state" class="form-control">
                <option value=''>Choose a state</option>
                @foreach($states as $state)
                    <option value={{ $state->id }}>{{ $state->name }}</option>
                @endforeach
            </select>
        </div>
    @endif
</div>
