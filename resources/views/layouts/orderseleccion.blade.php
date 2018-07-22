<div class="dropdown">
  <button class="btn btn-primary btn-block dropdown-toggle" type="button" id="dropdownorderby" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      {{$vd_selection_text}}
  </button>
  <div class="dropdown-menu" aria-labelledby="dropdownorderby">
    <a class="dropdown-item {{ (($vd_order_key=='name_asc')?'active':'') }}" href="{{ route('home') }}?field=name&order=asc">{{__('products.OrderbyNameAsc')}}</a>
    <a class="dropdown-item {{ (($vd_order_key=='name_desc')?'active':'') }}" href="{{ route('home') }}?field=name&order=desc">{{__('products.OrderbyNameDesc')}}</a>
    <div class="dropdown-divider"></div>
    <a class="dropdown-item {{ (($vd_order_key=='price_now_asc')?'active':'') }}" href="{{ route('home') }}?field=price_now&order=asc">{{__('products.OrderbyPriceCheaper')}}</a>
    <a class="dropdown-item {{ (($vd_order_key=='price_now_desc')?'active':'') }}" href="{{ route('home') }}?field=price_now&order=desc">{{__('products.OrderbyPriceNotCheaper')}}</a>
  </div>
</div>  