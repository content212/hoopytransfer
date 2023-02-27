<div class="row">
    <div class="col-md-9">
        <div class="card">
            <div class="card-body">
                <table class="table table-hover table-centered mb-0">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>


                        @foreach ($transactions as $transaction)
                            <tr>
                                <td>
                                    <a @if (0 or $transaction['balance'] != 0) href="/accounting/{{ $transaction['id'] }}" @endif>
                                        {{ $transaction['name'] }}
                                    </a>
                                </td>
                                <td>{{ $transaction['balance'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xxl-3 col-sm-6">
        <div class="card widget-flat bg-primary text-white">
            <div class="card-body">
                <div class="float-end">
                    <i class="mdi mdi-currency-usd widget-icon bg-light-lighten rounded-circle text-white"></i>
                </div>
                <h5 class="fw-normal mt-0" title="Revenue">TOTAL</h5>
                <h3 class="mt-3 mb-3 text-white">${{ number_format($total, 2) }}</h3>
            </div>
        </div>
    </div>

</div>
