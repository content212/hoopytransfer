<div>
    
    @if ($car_type)
        @foreach ($car_type->prices as $i => $price)
            <div class="row align-items-end">

                <div class="form-group col-2 col-md-2">
                        <input type="text" class="form-control" wire:model.defer="car_type.prices.{{ $i }}.start_km">
                </div>

                <div class="form-group col-2 col-md-2">
                        <input type="text" class="form-control" wire:model.defer="car_type.prices.{{ $i }}.finish_km">
                </div>

                <div class="form-group col-2 col-md-2">
                        
                        <input type="text" class="form-control" wire:model.defer="car_type.prices.{{ $i }}.opening_fee">
                </div>

                <div class="form-group col-2 col-md-2">
                        
                        <input type="text" class="form-control" wire:model.defer="car_type.prices.{{ $i }}.km_fee">
                </div>
                <div class="form-group col-md-2 pb-1">
                        
                        <button class="btn btn-sm btn-danger" wire:click="delete({{ $i }})" style="color: white">
                            <i class="fa fa-minus-circle"></i>
                        </button>

                        @if ($loop->last && count($news) == 0)
                            <button class="btn btn-sm btn-success" wire:click="add" style="color: white">
                                <i class="fa fa-plus-circle"></i>
                            </button>
                        @endif
                        
                </div>
                

            </div>
        @endforeach
        @foreach ($news as $i => $price)
        <div class="row align-items-end">

            <div class="form-group col-2 col-md-2">
                    <input type="text" class="form-control" wire:model.defer="news.{{ $i }}.start_km">
            </div>

            <div class="form-group col-2 col-md-2">
                    <input type="text" class="form-control" wire:model.defer="news.{{ $i }}.finish_km">
            </div>

            <div class="form-group col-2 col-md-2">
                    
                    <input type="text" class="form-control" wire:model.defer="news.{{ $i }}.opening_fee">
            </div>

            <div class="form-group col-2 col-md-2">
                    
                    <input type="text" class="form-control" wire:model.defer="news.{{ $i }}.km_fee">
            </div>
            <div class="form-group col-md-2 pb-1">
                    
                    <button class="btn btn-sm btn-danger" wire:click="newsdelete({{ $i }})" style="color: white">
                        <i class="fa fa-minus-circle"></i>
                    </button>

                    @if ($loop->last)
                        <button class="btn btn-sm btn-success" wire:click="add" style="color: white">
                            <i class="fa fa-plus-circle"></i>
                        </button>
                    @endif
                    
            </div>
            

        </div>
        @endforeach
    @endif
    
    <div class="row">
        <div class="form-group col-2 col-md-2 offset-6">
            <button type="button" wire:click="save" class="btn btn-primary pull-right"><h6><strong>Save</strong> <i class="fa fa-floppy-o"></i></h5></button>
        </div>
    </div>
    <script>

    </script>
</div>
