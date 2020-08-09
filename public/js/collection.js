function addNewActeurFilm(element, principal, index, role, id) {
	var start_id = "acteur_acteurFilms_INDEX_";
	var start_name = "acteur[acteurFilms][INDEX]";
	
	var select = '<div id="acteurFilms_' + index + '" class="collection">';
	select += '<div class="row w-100">';
	select += '<div class="col-sm-4">';
	if(id !== undefined){
		select += '<input type="hidden" id="' + start_id + 'id" name="' + start_name + '[id]" value="' + id + '">';
	}
	
	select += '<input type="hidden" class="supprimer" id="' + start_id + 'delete" name="' + start_name + '[delete]" value="0">';
	
	select += element;
	select += '</div>';
	
	select += '<div class="col-sm-4">';
	select += principal;
	select += '</div>';
	
	select += '<div class="col-sm-3">';
	select += '<input type="text" class="form-control" id="' + start_id + 'role" name="' + start_name + '[role]"';
	if(role){
		select += ' value="' + role + '"';
	}
	select += '>';
	select += '</div>';
	
	select += '<div class="col-sm-1">';
	if(id !== undefined){
		select += '<span onclick="enableElement(\'#acteurFilms_' + index + '\')" class="enable"><i class="fas fa-plus-circle"></i></span>';
		select += '<span onclick="disableElement(\'#acteurFilms_' + index + '\')" class="disable"><i class="fas fa-minus-circle"></i></span>';
	} else {
		select += '<span onclick="deleteElement(\'#acteurFilms_' + index + '\')" class="delete"><i class="fas fa-times"></i></span>';
	}
	select += '</div>';
	
	select += '</div>';
	select += '</div>';
	
	$("#acteurFilms").append(select.replace(/INDEX/g, index));
	
	$("#acteur_acteurFilms_" + index + "_film").select2({
	    minimumResultsForSearch: -1
	});
	$("#acteur_acteurFilms_" + index + "_principal").select2({
	    minimumResultsForSearch: -1
	});
	
	index++;
	
	return index;
}

function addNewFilmActeur(element, principal, index, role, id) {
	var start_id = "film_acteurFilms_INDEX_";
	var start_name = "film[acteurFilms][INDEX]";
	
	var select = '<div id="acteurFilms_' + index + '" class="collection">';
	select += '<div class="row w-100">';
	select += '<div class="col-sm-4">';
	if(id !== undefined){
		select += '<input type="hidden" id="' + start_id + 'id" name="' + start_name + '[id]" value="' + id + '">';
	}
	
	select += '<input type="hidden" class="supprimer" id="' + start_id + 'delete" name="' + start_name + '[delete]" value="0">';
	
	select += element;
	select += '</div>';
	
	select += '<div class="col-sm-4">';
	select += principal;
	select += '</div>';
	
	select += '<div class="col-sm-3">';
	select += '<input type="text" class="form-control" id="' + start_id + 'role" name="' + start_name + '[role]"';
	if(role){
		select += ' value="' + role + '"';
	}
	select += '>';
	select += '</div>';
	
	select += '<div class="col-sm-1">';
	if(id !== undefined){
		select += '<span onclick="enableElement(\'#acteurFilms_' + index + '\')" class="enable"><i class="fas fa-plus-circle"></i></span>';
		select += '<span onclick="disableElement(\'#acteurFilms_' + index + '\')" class="disable"><i class="fas fa-minus-circle"></i></span>';
	} else {
		select += '<span onclick="deleteElement(\'#acteurFilms_' + index + '\')" class="delete"><i class="fas fa-times"></i></span>';
	}
	select += '</div>';
	
	select += '</div>';
	select += '</div>';
	
	$("#acteurFilms").append(select.replace(/INDEX/g, index));
	
	$("#film_acteurFilms_" + index + "_acteur").select2({
	    minimumResultsForSearch: -1
	});
	$("#film_acteurFilms_" + index + "_principal").select2({
	    minimumResultsForSearch: -1
	});
	
	index++;
	
	return index;
}

function addNewActeurSaison(element, principal, index, role, id) {
	var start_id = "acteur_acteurSaisons_INDEX_";
	var start_name = "acteur[acteurSaisons][INDEX]";
	
	var select = '<div id="acteurSaisons_' + index + '" class="collection">';
	select += '<div class="row w-100">';
	select += '<div class="col-sm-4">';
	if(id !== undefined){
		select += '<input type="hidden" id="' + start_id + 'id" name="' + start_name + '[id]" value="' + id + '">';
	}
	
	select += '<input type="hidden" class="supprimer" id="' + start_id + 'delete" name="' + start_name + '[delete]" value="0">';
	
	select += element;
	select += '</div>';
	
	select += '<div class="col-sm-4">';
	select += principal;
	select += '</div>';
	
	select += '<div class="col-sm-3">';
	select += '<input type="text" class="form-control" id="' + start_id + 'role" name="' + start_name + '[role]"';
	if(role){
		select += ' value="' + role + '"';
	}
	select += '>';
	select += '</div>';
	
	select += '<div class="col-sm-1">';
	if(id !== undefined){
		select += '<span onclick="enableElement(\'#acteurSaisons_' + index + '\')" class="enable"><i class="fas fa-plus-circle"></i></span>';
		select += '<span onclick="disableElement(\'#acteurSaisons_' + index + '\')" class="disable"><i class="fas fa-minus-circle"></i></span>';
	} else {
		select += '<span onclick="deleteElement(\'#acteurSaisons_' + index + '\')" class="delete"><i class="fas fa-times"></i></span>';
	}
	select += '</div>';
	
	select += '</div>';
	select += '</div>';
	
	$("#acteurSaisons").append(select.replace(/INDEX/g, index));
	
	$("#acteur_acteurSaisons_" + index + "_saison").select2({
	    minimumResultsForSearch: -1
	});
	$("#acteur_acteurSaisons_" + index + "_principal").select2({
	    minimumResultsForSearch: -1
	});
	
	index++;
	
	return index;
}

function addNewSaisonActeur(element, principal, index, role, id) {
	var start_id = "saison_acteurSaisons_INDEX_";
	var start_name = "saison[acteurSaisons][INDEX]";
	
	var select = '<div id="acteurSaisons_' + index + '" class="collection">';
	select += '<div class="row w-100">';
	select += '<div class="col-sm-4">';
	if(id !== undefined){
		select += '<input type="hidden" id="' + start_id + 'id" name="' + start_name + '[id]" value="' + id + '">';
	}
	
	select += '<input type="hidden" class="supprimer" id="' + start_id + 'delete" name="' + start_name + '[delete]" value="0">';
	
	select += element;
	select += '</div>';
	
	select += '<div class="col-sm-4">';
	select += principal;
	select += '</div>';
	
	select += '<div class="col-sm-3">';
	select += '<input type="text" class="form-control" id="' + start_id + 'role" name="' + start_name + '[role]"';
	if(role){
		select += ' value="' + role + '"';
	}
	select += '>';
	select += '</div>';
	
	select += '<div class="col-sm-1">';
	if(id !== undefined){
		select += '<span onclick="enableElement(\'#acteurSaisons_' + index + '\')" class="enable"><i class="fas fa-plus-circle"></i></span>';
		select += '<span onclick="disableElement(\'#acteurSaisons_' + index + '\')" class="disable"><i class="fas fa-minus-circle"></i></span>';
	} else {
		select += '<span onclick="deleteElement(\'#acteurSaisons_' + index + '\')" class="delete"><i class="fas fa-times"></i></span>';
	}
	select += '</div>';
	
	select += '</div>';
	select += '</div>';
	
	$("#acteurSaisons").append(select.replace(/INDEX/g, index));
	
	$("#saison_acteurSaisons_" + index + "_acteur").select2({
	    minimumResultsForSearch: -1
	});
	$("#saison_acteurSaisons_" + index + "_principal").select2({
	    minimumResultsForSearch: -1
	});
	
	index++;
	
	return index;
}

function addNewSerieSaisons(index) {
	var start_id = "serie_saisons_INDEX_";
	var start_name = "serie[saisons][INDEX]";
	
	var select = '<div id="serie_saisons_' + index + '" class="collection">';
	select += '<div class="row">';
	select += '<div class="col-sm-3">';
	
	select += '<input type="text" id="' + start_id + 'nom" name="' + start_name + '[nom]" class="form-control">';
	select += '</div>';
	
	select += '<div class="col-sm-4">';
	select += '<input type="number" id="' + start_id + 'numero_saison" name="' + start_name + '[numero_saison]" required="required" class="form-control">';
	select += '</div>';
	
	select += '<div class="col-sm-4">';
	select += '<input type="number" id="' + start_id + 'nombre_episode" name="' + start_name + '[nombre_episode]" required="required" class="form-control">';
	select += '</div>';
	
	select += '<div class="col-sm-1">';
	select += '<span onclick="deleteElement(\'#serie_saisons_' + index + '\')" class="delete"><i class="fas fa-times"></i></span>';
	select += '</div>';
	
	select += '</div>';
	select += '</div>';
	
	$("#serie_saisons").append(select.replace(/INDEX/g, index));
	
	index++;
	
	return index;
}

function deleteElement(id){
	$(id).remove();
}

function disableElement(id){
	$(id + " select, " + id + " input[type=text]").prop('disabled', true);
	$(id + " .supprimer").val(1);
	$(id + " .enable").show();
	$(id + " .disable").hide();
}

function enableElement(id){
	$(id + " select, " + id + " input[type=text]").prop('disabled', false);
	$(id + " .supprimer").val(0);
	$(id + " .enable").hide();
	$(id + " .disable").show();
}

