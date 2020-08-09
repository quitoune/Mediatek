function is_defined(element){
	if(element === null){
		return false;
	}
	if(element === undefined){
		return false;
	} 
	if(element = ""){
		return false;
	}
	return true;
}

function Ajax(url, id_done, callback, loading, method){
	if(!is_defined(loading)){
		loading = 'html';
	}
	
	if(!is_defined(method)){
		method = 'GET';
	}
	
	$.ajax({
        url: url,
        method: method
    }).done(function(html) {
        $(id_done).html(html);
        if(callback !== undefined){
            callback();
        }
        $(loading).hideLoading();
    });
}

function changePageAjax(id, url_base){
	$("html").showLoading();
	$(id).click(function(){
		event.preventDefault();
		
		$.ajax({
			method: 'GET',
			url: $(this).attr('href'),
		})
		.done(function(html) {
			$(url_base).html(html);
			$("html").hideLoading();
		});
	});
}

function SubmitModal(objet, type, url, id_modal, url_base, id_bloc){
    $("form[name*='" + objet + "']").on('submit', function(event){
        
    	$("html").showLoading();
        $("#" + objet + "_save").prop('disabled', true).html('Chargement...');
       
       event.preventDefault();
       
       $.ajax({
           url:url,
           method:'POST',
           data:$(this).serialize()
       }).done(function(reponse){
           if(reponse.statut){
               $(id_modal).modal('hide');
               if(typeof url_base == "object"){
            	   var i = 0;
            	   for(i = 0; i < url_base.length; i++){
            		   Ajax(url_base[i], id_bloc[i]);
            	   }
               } else {
                   Ajax(url_base, id_bloc);
               }
           } else {
               $('#modal_' + type).html(reponse);
           }
       });
    });
};

function deleteAjax(url, url_base, id_bloc){
	$("html").showLoading();
	$.ajax({
        url:url,
        method:'GET'
    }).done(function(reponse){
        if(reponse.statut){
            Ajax(url_base, id_bloc);
        }
    });
}
