<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
        
    <div class="card">
        
        <img class="card-img-top" src="{{h_getLocalProductImage($vd_product,'base')}}" alt="{{$vd_product->name}}">
        @if(h_getLocalProductImage($vd_product,'logo'))
            <a href="{{$vd_product->url_detail}}" target="_blank">
            <img class="imglogo" src="{{h_getLocalProductImage($vd_product,'logo')}}" alt="{{$vd_product->name}}">
            </a>
        @endif
        
        @guest
            <a type="button" href="{{ url('login') }}" class="btn blike btn-success float-right"><b>{{__('products.Like')}}</b></a>
        @else        
            <button type="button" data-url="{{ route('ajax_remove') }}" data-id="{{$vd_product->id}}" class="{{ ((in_array($vd_product->id,$vd_myfavourites))?'':'invisible') }} btn btn-warning blike bnotlikeuser float-right"><b>{{__('products.NotLike')}}</b></button>
            <button type="button" data-url="{{ route('ajax_add') }}" data-id="{{$vd_product->id}}" class="{{ ((in_array($vd_product->id,$vd_myfavourites))?'invisible':'') }} btn btn-success blike blikeuser float-right"><b>{{__('products.Like')}}</b></button>
        @endguest        

        <div class="card-body">
          <h5 class="card-title">{{ $vd_product->name }}</h5>
          @if(strlen($vd_product->description)>0 && json_decode($vd_product->description))
            <p class="card-text d-none d-sm-block">
                <span class="badge badge-secondary">{!! implode('</span> <span class="badge badge-secondary">' ,  json_decode($vd_product->description)) !!}</span>
            </p>
          @endif
            <h2 class="text-center">
                @if(floatval($vd_product->price_previous)>0)
                    <span class="badge badge-danger font-italic">{{ h_formatPrice( 100 - (($vd_product->price_previous*100)/$vd_product->price_now) , 0) }}% OFF!</span>
                
                    <span class="badge badge-success">
                @else
                    <span class="badge badge-dark">
                @endif
                {{ h_formatPrice($vd_product->price_now) }} &euro;
                </span>
            </h2>
          
        </div>
    </div>    

</div>
