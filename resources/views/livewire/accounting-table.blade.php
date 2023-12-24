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
                                    <a href="/accounting/{{ $transaction['id'] }}" >
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
        <div class="card">
            <div class="card-body">
                <div class="float-end">
                    <i class="mdi mdi-currency-usd widget-icon rounded-circle text-white"
                        style="background-color: rgb(49, 58, 70);"></i>
                </div>
                <h5 class="fw-normal mt-0" title="Total">TOTAL</h5>
                <h3 class="mt-3 mb-3">${{ $total }}</h3>
                <h5 class="fw-normal mt-0"><a href="/accountingdetail" >Detail</a></h3>

            </div>
        </div>
    </div>

</div>
