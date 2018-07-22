@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            @if(count($vd_favourites)>0)
            
                <div class="alert alert-info text-center">
                    {!! __('products.FriendFavourites',['NAME' => $vd_name , 'NREG' => count($vd_favourites)]) !!}
                </div>
                
                <ul id="ulfav" class="list-group">
                @foreach($vd_favourites as $v_favourite)
                    <li data-id="{{$v_favourite->id}}" class="list-group-item">                        
                        <div class="media">
                            <img class="img-responsive imgfav" src="{{h_getLocalProductImage($v_favourite,'base')}}" alt="{{$v_favourite->name}}">
                            <div class="media-body">
                                <h4 class="mt-0">
                                    {{ $v_favourite->name }}
                                    @if(floatval($v_favourite->price_previous)>0)
                                        <span class="badge badge-pill badge-success">
                                    @else
                                        <span class="badge badge-pill badge-dark">
                                    @endif
                                    {{ h_formatPrice($v_favourite->price_now) }} &euro;
                                    </span>                                                                        
                                </h4>
                                @if(strlen($v_favourite->description)>0 && json_decode($v_favourite->description))
                                  <p>
                                      <span class="badge badge-secondary">{!! implode('</span> <span class="badge badge-secondary">' ,  json_decode($v_favourite->description)) !!}</span>
                                  </p>
                                @endif                                       
                            </div>
                        </div>                                                                        
                    </li>
                @endforeach
                </ul>           
            
            @else
                <div class="row">
                    <div class="col-md-12">                            
                        <div class="alert alert-info">
                            {{ __('products.No_favourites') }}
                        </div>
                    </div>
                </div>            
            @endif
            
            
        </div>
    </div>
</div>
@endsection
