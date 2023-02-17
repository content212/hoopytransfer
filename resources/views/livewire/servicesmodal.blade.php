<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="editmodalLabel">Edit Service</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="row justify-content-start">
            <div class="col-lg-4 col-md-6">
                <span class="overflow-hidden">
                    @if ($newImage)
                    <img src="{{ Storage::disk('images')->url('tmp/'. $newImage->getFilename()) }}" class="img-fluid rounded border" style="height:240px; width:100%" alt="image">
                    @else
                        <img src="{{ isset($car_type->image) ? $car_type->imageUrl() : Storage::disk('images')->url('a.jpg') }}" class="img-fluid rounded border" style="height:240px; width:100%" alt="image">
                    @endif
                </span>
                <input type="file" wire:model="newImage">
            </div>
            <div class="col-lg-8 col-md-6">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" wire:model.defer="car_type.name" placeholder="Name">
                </div>
                <div class="form-group">
                    <label>Person Capacity</label>
                    <input type="text" class="form-control" wire:model.defer="car_type.person_capacity" placeholder="Person Capacity">
                </div>
                <div class="form-group">
                    <label>Baggage Capacity</label>
                    <input type="text" class="form-control" wire:model.defer="car_type.baggage_capacity" placeholder="Baggage Capacity">
                </div>
                <div class="form-group">
                    <label>Discount Rate</label>
                    <input type="number" class="form-control" max="100" wire:model.defer="car_type.discount_rate" placeholder="Discount Rate">
                </div>
            </div>
        </div>
        <hr class="mt-2 mb-3" />
        <div class="row align-items-end">
            <div class="form-group col-2 col-md-2">
                <label>Start Km</label>
            </div>
            <div class="form-group col-2 col-md-2">
                <label for="name">Finish Km</label>
            </div>
            <div class="form-group col-2 col-md-2">
                <label for="name">Opening Fee</label>
            </div>
            <div class="form-group col-2 col-md-2">
                <label for="name">Km Fee</label>
            </div>
        </div>
        @if (isset($car_type->id))
            @foreach ($car_type->prices as $i => $price)
                <div class="row align-items-end">
                    <div class="form-group col-2 col-md-2">
                        <input type="text" class="form-control @error('car_type.prices.' . $i . '.start_km') is-invalid @enderror" {{ $loop->first ? "wire:model.layz" : "wire:model.defer" }}="car_type.prices.{{ $i }}.start_km" readonly>
                    </div>
                
                    <div class="form-group col-2 col-md-2">
                        <input type="text" class="form-control @error('car_type.prices.' . $i . '.finish_km') is-invalid @enderror" {{ $loop->first ? "wire:model.layz" : "wire:model.defer" }}="car_type.prices.{{ $i }}.finish_km">
                    </div>
                
                    <div class="form-group col-2 col-md-2">
                        <input type="text" class="form-control @error('car_type.prices.' . $i . '.opning_fee') is-invalid @enderror" {{ $loop->first ? "wire:model.layz" : "wire:model.defer" }}="car_type.prices.{{ $i }}.opening_fee">
                    </div>
                
                    <div class="form-group col-2 col-md-2">
                        <input type="text" class="form-control @error('car_type.prices.' . $i . '.km_fee') is-invalid @enderror" {{ $loop->first ? "wire:model.layz" : "wire:model.defer" }}="car_type.prices.{{ $i }}.km_fee">
                    </div>
                    <div class="form-group col-md-2 pb-1">
                            @if (!$loop->first && $loop->count > 1)
                                <button class="btn btn-sm btn-danger" wire:click="delete({{ $i }})" style="color: white">
                                    <i class="fa fa-minus-circle"></i>
                                </button>
                            @endif
                            
                        
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
                    <input type="text" class="form-control @error('news.' . $i . '.start_km') is-invalid @enderror" wire:model.layz="news.{{ $i }}.start_km" readonly>
                </div>
            
                <div class="form-group col-2 col-md-2">
                    <input type="text" class="form-control @error('news.' . $i . '.finish_km') is-invalid @enderror" wire:model.layz="news.{{ $i }}.finish_km">
                </div>
            
                <div class="form-group col-2 col-md-2">

                    <input type="text" class="form-control @error('news.' . $i . '.opening_fee') is-invalid @enderror" wire:model.layz="news.{{ $i }}.opening_fee">
                </div>
            
                <div class="form-group col-2 col-md-2">

                    <input type="text" class="form-control @error('news.' . $i . '.km_fee') is-invalid @enderror" wire:model.layz="news.{{ $i }}.km_fee">
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
        @else
            @foreach ($news as $i => $price)
                <div class="row align-items-end">
                    <div class="form-group col-2 col-md-2">
                        <input type="text" class="form-control @error('news.' . $i . '.start_km') is-invalid @enderror" wire:model.layz="news.{{ $i }}.start_km" readonly>
                            
                    </div>
                
                    <div class="form-group col-2 col-md-2">
                        <input type="text" class="form-control @error('news.' . $i . '.finish_km') is-invalid @enderror" wire:model.layz="news.{{ $i }}.finish_km">
                    </div>
                
                    <div class="form-group col-2 col-md-2">

                        <input type="text" class="form-control @error('news.' . $i . '.opening_fee') is-invalid @enderror" wire:model.layz="news.{{ $i }}.opening_fee">
                    </div>
                
                    <div class="form-group col-2 col-md-2">

                        <input type="text" class="form-control @error('news.' . $i . '.km_fee') is-invalid @enderror" wire:model.layz="news.{{ $i }}.km_fee">
                    </div>
                    <div class="form-group col-md-2 pb-1">
                            @if (!$loop->first && $loop->count > 1)
                                <button class="btn btn-sm btn-danger" wire:click="newsdelete({{ $i }})" style="color: white">
                                    <i class="fa fa-minus-circle"></i>
                                </button>
                            @endif
                        
                            @if ($loop->last)
                                <button class="btn btn-sm btn-success" wire:click="add" style="color: white">
                                    <i class="fa fa-plus-circle"></i>
                                </button>
                            @endif
                            
                    </div>

                
                </div>
            @endforeach
        @endif
        
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"> Close</button>
        <button type="button" class="btn btn-primary" wire:click="save">Save changes</button>
    </div>
</div>