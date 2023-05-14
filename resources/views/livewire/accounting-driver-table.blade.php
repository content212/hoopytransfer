<div class="row">
    <div class="col-md-9">
        <div class="card">
            <div class="card-body">
                <table class="table table-hover table-centered mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Amount</th>
                            @if ($driver_id != -1)
                                <th>Balance</th>
                            @endif
                            <th>Note</th>
                            @if ($driver_id == -1)
                                <th>Driver Name</th>
                            @endif
                            <th>Edit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->id }}</td>
                                <td>{{ $transaction->type }}</td>
                                <td>{{ $transaction->amount }}</td>
                                @if ($driver_id != -1)
                                    <td>{{ $transaction->balance }}</td>
                                @endif
                                <td>{{ $transaction->note }}</td>
                                @if ($driver_id == -1)
                                    <td>{{  $transaction->driver->user->name ?? 'KASA'}}</td>
                                @endif
                                <td>@if ($transaction->type == 'driver_payment' and $driver_id != -1)
                                    <a wire:click="delete({{ $transaction->id }})" class="btn btn-danger btn-sm"><i class="mdi mdi-delete-forever"></i></a>
                                @endif</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td>#</td>
                            <td>Opening</td>
                            <td>0.00</td>
                            <td>0.00</td>
                            <td>Opening</td>
                            @if ($driver_id == -1)
                                <td>{{  $transaction->driver->user->name ?? 'KASA'}}</td>
                            @endif
                        </tr>
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
                <h3 class="mt-3 mb-3">${{ number_format($total, 2) }}</h3>
            </div>
        </div>
        @if ($driver_id != -1 and $role == 'admin')
            <div class="card">
                <div class="d-flex card-header justify-content-between align-items-center">
                    <h4 class="header-title">Add Payment</h4>
                </div>
                <div class="card-body pt-1">
                    <div class="form-group">
                        <div class="form-group">
                            <label for="amount">Amount</label>
                            <input type="text" class="form-control  @error('amount') is-invalid @enderror"
                                wire:model.lazy="amount" placeholder="Amount">
                        </div>
                        <div class="form-group">
                            <label for="note">Note</label>
                            <textarea class="form-control @error('note') is-invalid @enderror" wire:model.lazy="note" rows="3"
                                placeholder="Note"></textarea>
                        </div>
                        <div class="form-group float-end">
                            <button type="button" class="btn btn-primary" wire:click="add">Add</button>
                        </div>
                    </div>
                </div> <!-- end card-body-->
            </div>
        @endif
    </div>

</div>
