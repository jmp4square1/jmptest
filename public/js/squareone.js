
function sq_Ajax(pURL,pData,pCallBack)
{
    $.ajax({
      url: pURL,
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },    
      global: false,
      type: 'POST',
      async: true,
      timeout: 0,
      cache: false,
      contentType: 'application/json; charset=utf-8',
      dataType: 'json',
      data: JSON.stringify(pData),
      error: sq_Ajax_Err,
      success: sq_Ajax_End,
      cb: pCallBack
    });    
}
function sq_Ajax_Err(XMLHttpRequest, textStatus, errorThrown)
{
    $('button:disabled').removeClass('disabled').prop('disabled',false);
    alert('¡¡Ajax error!!');
}
function sq_Ajax_End(pData)
{
    if(typeof this.cb === 'string') {
        eval(this.cb+'(pData);');
    }
    if(typeof this.cb === 'function') {
        this.cb(pData);
    }  
}
function sq_Notify(pType,pTxt)
{
    var vOptions = {
  	element: 'body',
  	position: null,
  	type: pType,
  	allow_dismiss: true,
  	newest_on_top: false,
  	showProgressbar: false,
  	placement: {
          from: "top",
          align: "right"
        },
  	spacing: 0,
        offset: {"y" : 0 },
  	z_index: 9999,
  	delay: 2000,
  	timer: 1000,
  	url_target: '_self',
  	animate: {
            enter: 'animated fadeInDown',
            exit: 'animated fadeOutUp'
  	}
    };

    $.notify({
          message: pTxt,
          target: '_self'
    },vOptions);
}



function sq_Init() {
  
    sq_Favourite_Ctrl();
}

function sq_Favourite_Ctrl()
{
    $(document).on('click','.blikeuser',function () {
        $(this).addClass('disabled').prop('disabled',true);
        sq_Ajax($(this).data('url'),{ 'id' : $(this).data('id') },sq_Favourite_CB)        
    });    
    $(document).on('click','.bnotlikeuser',function () {
        $(this).addClass('disabled').prop('disabled',true);
        sq_Ajax($(this).data('url'),{ 'id' : $(this).data('id'), "infav" : $(this).data('infav') },sq_NotFavourite_CB)        
    });        
}
function sq_Favourite_CB(pData)
{
    $('.blikeuser[data-id="'+pData.id+'"]').removeClass('disabled').prop('disabled',false);
    if(pData.result) {
        $('.blikeuser[data-id="'+pData.id+'"]').addClass('invisible');
        $('.bnotlikeuser[data-id="'+pData.id+'"]').removeClass('invisible');        
        sq_Notify('success',pData.m);
    }
    else {
        sq_Notify('danger',pData.m);
    }    
}
function sq_NotFavourite_CB(pData)
{
    $('.bnotlikeuser[data-id="'+pData.id+'"]').removeClass('disabled').prop('disabled',false);
    if(pData.result) {
        if(pData.from_favourites) {
            $('#ulfav li[data-id="'+pData.id+'"]').fadeOut('fast',function() {
                $(this).remove();
                $('#nfav').html( parseInt($('#nfav').html(),10)-1  );
                if($('#ulfav li').length==0) {
                    $('#ulfav').remove();
                }
            });
        }
        else {
            $('.bnotlikeuser[data-id="'+pData.id+'"]').addClass('invisible');        
            $('.blikeuser[data-id="'+pData.id+'"]').removeClass('invisible');            
        }
        sq_Notify('warning',pData.m);
    }
    else {
        sq_Notify('danger',pData.m);
    }    
}

sq_Init();