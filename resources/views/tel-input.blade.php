@php
    $countries = \App\Models\Country::all();
@endphp
<select name="{{$name}}" id="{{$name}}" class="form-control">
    <option value="">Select Country</option>
    @foreach ($countries as $country)
        <option value="+{{$country->phonecode}}">{{$country->name}} +{{$country->phonecode}}</option>
    @endforeach
</select>
