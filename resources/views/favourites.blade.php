@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            @if(count($vd_favourites)>0)
            
                <div class="row">
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            {!! __('products.NFavourites',['NREG' => count($vd_favourites)]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $vd_url_share['fb'] }}" class="pull-right mysocialmediaicons fa fa-facebook" target="_blank"></a>
                        <a href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{ $vd_url_share['ln'] }}" class="pull-right mysocialmediaicons fa fa-linkedin" target="_blank"></a>
                        <a href="https://plus.google.com/share?url={{ $vd_url_share['gp'] }}" class="pull-right mysocialmediaicons fa fa-google" target="_blank"></a>
                        <a href="https://twitter.com/intent/tweet?text={{ $vd_url_share['tw']  }}" class="pull-right mysocialmediaicons fa fa-twitter" target="_blank"></a>
                    </div>
                </div>                 
            
                <ul id="ulfav" class="list-group">
                @foreach($vd_favourites as $v_favourite)
                    <li data-id="{{$v_favourite->id}}" class="list-group-item">                        
                        <div class="media">
                            <img class="img-responsive imgfav" src="{{h_getLocalProductImage($v_favourite,'base')}}" alt="{{$v_favourite->name}}">
                            <div class="media-body">
                                <button type="button" data-infav="1" data-url="{{ route('ajax_remove') }}" data-id="{{$v_favourite->id}}" class="btn btn-warning blike bnotlikeuser float-right"><b>{{__('products.NotLike')}}</b></button>
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
