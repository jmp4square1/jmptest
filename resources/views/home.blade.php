@extends('layouts.app')

@section('content')
<div class="container">

            @if(count($vd_products)>0)
            
            <div class="row">
                <div class="col-md-9">
                    <div class="alert alert-info">
                        {{ __('products.NProducts',['NREG' => $vd_total_products])}}
                    </div>
                </div>
                <div class="col-md-3">
                    @include('layouts.orderseleccion',['vd_params'=>$vd_params,'vd_selection_text'=>$vd_selection_text,'vd_order_key'=>$vd_order_key])  
                </div>
            </div>            
            
            
            <div class="row">
                @foreach ($vd_products as $v_product)
                    @include('layouts.product',['vd_product'=>$v_product,'vd_myfavourites'=>$vd_myfavourites])                    
                @endforeach  
            </div>
            
            <div class="row">
                <div class="col">
                        <hr>
                        {{ $vd_pagination_links }}
                </div>                
            </div>
            
            @else
                <div class="row">
                    <div class="col-md-12">                            
                        <div class="alert alert-info">
                            <b>{{ __('products.Welcome') }}</b>
                            <br>
                            {{ __('products.No_products') }}
                        </div>
                    </div>
                </div>            
            @endif
            
</div>
@endsection
