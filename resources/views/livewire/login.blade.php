<div class="form-signin">
    {{ Html::image(asset('img/hoopy-transfer-admin-logo.png'), 'logo',  array( 'width' => 300, 'height' => 60 )) }}
    <h1 class="h3 mb-3 fw-bold mx-auto"></h1>
    <span id="form_result">
        @if (session()->has('message'))
            <div class="alert alert-success">
                <p>{{ session('message') }}</p>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger">
                <p>{{ session('error') }}</p>
            </div>
        @endif
    </span>

    
    @if (!$user_id)
        <form wire:submit.prevent="generate">

            <input type="text" name="phone" wire:model.lazy ="phone" class="form-control mb-2" placeholder="Phone Number"
                required autofocus>

            <button class="w-100 btn btn-lg" type="submit"
                style="background-color: #00b9ff;color: #202020">Sign in</button>
        </form>
    @else
        <form wire:submit.prevent="otplogin">

            <input type="text" name="otp" wire:model.lazy ="otp" class="form-control mb-2" placeholder="Verification Number"
                required autofocus>

            <button class="w-100 btn btn-lg" type="submit"
                style="background-color: #00b9ff;color: #202020">Sign in</button>
        </form>
    @endif
    
</div>
