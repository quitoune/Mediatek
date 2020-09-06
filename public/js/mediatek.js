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
//    $(loading).showLoading();
	
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

function createNumberDropdown(id, name, start, end, defaut, step){
	if(step === undefined){
		step = 1;
	}
	
	if(defaut === undefined){
		defaut = true;
	}
	
	var select = '<select id="' + id + '" name="' + name + '" class="form-control">';
	if(defaut){
		select += '<option value=""></option>';
	}
	for(var i = start; i <= end; i += step){
		select += '<option value="' + i + '">' + addZero(i) +'</option>';
	}
	select += '</select>';
	
	return select;
}

function createInputDate(id, name, label){
	var date = (new Date()).getFullYear();
	
	var input = '<fieldset class="form-group">';
	input += "<legend class=\"col-form-label\">" + label + "</legend>";
	input += '<div id="' + id + '" class="form-inline">';
	input += '<div class="sr-only">';
	input += '<label class="" for="' + id + '_year">Year</label>';
	input += '<label class="" for="' + id + '_month">Month</label>';
	input += '<label class="" for="' + id + '_day">Day</label>';
	input += '</div>';

	input += createNumberDropdown(id + '_year', name + '[day]', 1, 31);
	input += '/';
	input += createNumberDropdown(id + '_month', name + '[month]', 1, 12);
	input += '/';
	input += createNumberDropdown(id + '_day', name + '[year]', date - 15, date);
	
	input += '</div>';
	input += '</fieldset>';
	
	return input;
}

function createInputSelect(id, name, label, array){
	var input = '<div class="form-group">';
	input += '<label for="' + id + '">' + label + '</label>';
	input += '<select id="' + id + '" name="' + name + '" class="form-group">';
	jQuery.each(array, function(index, value){
		input += '<option value="' + index + '">' + value + '</option>';
	});
	input += '</select>';
	input += '</div>';
	
	return input;
}

function addZero(value){
	if(0 < value && value < 10){
		return ("0" + value).slice(-2);
	}
	return value;
}

function readURL(input, id) {
	if (input.files && input.files[0]) {
		$('#' + id + '_nom').val(input.files[0].name);
		$("label[for=" + id + "]").html(input.files[0].name);
		var reader = new FileReader();
		
		reader.onload = function(e) {
			$('img[data-id=' + id + ']').show();
			$('img[data-id=' + id + ']').attr('src', e.target.result);
		}
		
		reader.readAsDataURL(input.files[0]); // convert to base64 string
	} else {
		$('img[data-id=' + id + ']').hide();
		$('img[data-id=' + id + ']').attr('src', '');
		$('#' + id + '_nom').val('');
		$("label[for=" + id + "]").html("Choisir la photo");
	}
}