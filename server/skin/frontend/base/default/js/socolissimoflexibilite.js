/**
 * Addonline_SoColissimoFlexibilite
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimoFlexibilite
 * @copyright   Copyright (c) 2011 Addonline
 * @author 	    Addonline (http://www.Addonline.fr)
 */


jQuery(function($) {
	
	$("input[id^=\"s_method\"]").live("change", function() {
		shippingRadioCheck(this);
	});
	
});

String.prototype.startWith = function(t, i) { if (i==false) { return
(t == this.substring(0, t.length)); } else { return (t.toLowerCase()
== this.substring(0, t.length).toLowerCase()); } } 

function shippingRadioCheck(element) {
	if (element.id.startWith("s_method_socolissimoflexibilite") && jQuery(element).attr("checked", "checked")){
		jQuery("#socolissimo-location").show();
	} else {
		jQuery("#socolissimo-location").hide();
	}
}

var myPositionSocolissimo;
var overlayApi;

function socolissimoRadioCheck(input) {
	//on commence par ré-initialiser le relais qui a pu être déjà choisi 
	jQuery("#socolissimo-location input[name=relais_socolissimo]").val("");
	jQuery("#socolissimo-location .nom_relais").text("");	
	relaisChoisi=undefined;
	//on vérifie si le champ telephone doit apparaitre
	checkDisplayPhone(input);
	if (input.value == "poste" || input.value == "cityssimo" || input.value == "commercant"){
		//on déplace le layer sur le body pour qu'il soit bien positionné au centre
		jQuery(jQuery(input).attr("rel")).appendTo("body");
		//initialisation de la liste déroulantes des villes "personnalisée"
		jQuery("#socolissimo_city_select").change(function() {
			jQuery(this).prevAll("span").eq(0).text(jQuery(this).find("option:selected").text());
		});
		//initilisation du rechargement de la liste déroulante des villes 
		jQuery("#socolissimo_postcode").change(function(){
				var postcode = this.value; 
				jQuery.ajax({
					  url: 'http://api.geonames.org/postalCodeSearchJSON?username=addonline&country=fr&postalcode='+postcode,
					  dataType:'jsonp',
					  jsonpCallback: 'reloadCities',
					  success: function(json){
						  var options = '<option selected >Choisissez une commune</option>';
						  for (i=0; i<json.postalCodes.length; i++){ 
							  commune = json.postalCodes[i].placeName;
							  options += '<option value="' + commune + '">' + commune + '</option>';
						  }
						  jQuery("#socolissimo_city_select").html(options);
						  jQuery("#socolissimo_city").text("Choisissez une commune");
					  }
				});
		});
		//mise à jour des checkbox de type de relais dans le layer selon le choix fait avant
		jQuery("#layer_socolissimo input:checkbox").each(function(index, element){
			check = jQuery(element);
			if (check.val() == input.value) {
				check.prop("checked", "checked");
			} else {
				check.prop("checked", "");
			}
		});
		//on localise l'adresse qui est préchargée (adresse de livraison par défaut du compte client) 
		geocodeAdresse();
		
		overlayApi = jQuery(input).overlay({
		    expose: { 
		        color: '#000', 
		        loadSpeed: 200, 
		        opacity: 0.5 
		    }, 
		    closeOnClick: false,
		    top: "center",
			onBeforeClose : function(event){
				//si on n'a pas choisi de relais on décoche le mode de livraison
				if (relaisChoisi == undefined || relaisChoisi == null) {
					jQuery("#socolissimo-location input[name=type_socolissimo]").attr("checked","");
				}
			},
			fixed: false,
		    api: true 
		  });
		overlayApi.load();

	}
}

function checkDisplayPhone(input) {
	if (jQuery(input).val() == "rdv" || jQuery(input).val() == "cityssimo" || jQuery(input).val() == "commercant" || jQuery(input).val() == "poste"){
		jQuery("#socolissimo-location label.portable").show().css("display:block;");
		jQuery("#socolissimo-location label.portable span").hide();
		jQuery("#socolissimo-location label.portable span."+jQuery(input).val()).show();
	} else {
		jQuery("#socolissimo-location label.portable").hide();
	}
}

function geocodeAdresse() {
	if (jQuery("#socolissimo_city_select option").length > 0 && jQuery("#socolissimo_city_select")[0].selectedIndex == 0) {
		alert("Veuillez sélectionner une commune");
		return;
	}
	if (jQuery("#socolissimo_postcode").val() == "") {
		alert("Veuillez saisir un code postal");
		return;
	}
	if (jQuery("#socolissimo_street").val() == "") {
		alert("Veuillez saisir une adresse");
		return;
	}
	var geocoder = new google.maps.Geocoder();
	var searchAdress = jQuery("#socolissimo_street").val() + ' ' + jQuery("#socolissimo_postcode").val() + ' ' + jQuery("#socolissimo_city").text();
	//console.log('Search adresse : ' + searchAdress);
	geocoder.geocode({'address': searchAdress}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
 			myPositionSocolissimo= results[0].geometry.location;
 			//on met à jour la carte avec cette position
 			changeMap();
		} else {
			alert('Adresse invalide '+searchAdress);
		}
    });
}

function changeMap() {
	if (myPositionSocolissimo!=undefined) {
		loadListeRelais();
	}
}
var listRelaisSocolissimo=new Array();
var skinUrl="";
function loadListeRelais() {
	jQuery(".loader-wrapper").fadeTo(300, 1);
	url = "/socolissimoflexibilite/ajax/listrelais?"
	jQuery("#layer_socolissimo input:checkbox").each(function(index, element){
		check = jQuery(element);
		url = url + check.val() + "=" + check.attr("checked") + "&";
	});
	url = url + "adresse=" + jQuery("#socolissimo_street").val() + "&zipcode=" + jQuery("#socolissimo_postcode").val()+ "&ville=" + jQuery("#socolissimo_city").text();
	jQuery.getJSON( url, function(response) {
		if (!response.error) {
			listRelaisSocolissimo = response.items;
			skinUrl = response.skinUrl;
			jQuery("#adresses_socolissimo").html(response.html);
			showMap();
		} else { 
			alert(response.error);
		}	
		jQuery(".loader-wrapper").fadeTo(300, 0).hide();

	});
	
	
}

var map;

function showMap() {
	var myOptions = {
	    	zoom: 15,
	    	center: myPositionSocolissimo,
	    	mapTypeId: google.maps.MapTypeId.ROADMAP
	}
	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	iconUrl = jQuery("#layer_socolissimo .ligne1").css("background-image");
	iconMatch = iconUrl.match("url\\(\"(.*)\"\\)");
	if (iconMatch == null) {
		//chrome
		iconMatch = iconUrl.match("url\\((.*)\\)");
	}
	iconUrl = iconMatch[1];
	var marker = new google.maps.Marker({
	    map: map, 
	    position: myPositionSocolissimo,
	    icon : iconUrl
	});
	var init = false;
	google.maps.event.addListener(map, 'tilesloaded', function () {
		if (!init){
			for (icounter=0; icounter<listRelaisSocolissimo.length; icounter++) {
				relaisSocolissimo = listRelaisSocolissimo[icounter];
				var relaisPosition =  new google.maps.LatLng(relaisSocolissimo.coordGeolocalisationLatitude,relaisSocolissimo.coordGeolocalisationLongitude);
				var iconUrl = skinUrl + "picto_poste.png";
				if (relaisSocolissimo.typeDePoint=="A2P") {
					iconUrl = skinUrl + "picto_commercant.png";
				} else if(relaisSocolissimo.typeDePoint=="CIT") {
					iconUrl = skinUrl + "picto_cityssimo.png";
				};
				marker = new google.maps.Marker({
				    map: map, 
				    position: relaisPosition,
				    title : relaisSocolissimo.nom,
				    icon : iconUrl 
				});
				if (!map.getBounds().contains(relaisPosition)){
					newBounds = map.getBounds().extend(relaisPosition);
					map.fitBounds(newBounds);
				}
				infowindow=infoBulleGenerator(relaisSocolissimo);
				attachClick(marker,infowindow, icounter);			
			}
		}
		init=true;
	});
}

//générateur d'infobulle
function infoBulleGenerator(relaisSocolissimo) {
	contentString = '<div class="adresse">';
	contentString += '<b>'+relaisSocolissimo.nom+ '</b><br/>';
	contentString += '<b>'+relaisSocolissimo.adresse1+ ' ' + relaisSocolissimo.codePostal + ' ' + relaisSocolissimo.localite + '</b><br/>';
	if (relaisSocolissimo.congesTotal) {
		contentString += '<b>En congés </b>';
	    contentString += '<p>';
	} else {
		contentString += '<b>Horaires d\'ouverture :</b><br/>';
		contentString += '(valables du ' + relaisSocolissimo.periodeActiviteHoraireDeb + ' au '+ relaisSocolissimo.periodeActiviteHoraireFin +')';
	    contentString += '<p>';
	    if (relaisSocolissimo.horairesOuvertureLundi!='00:00-00:00 00:00-00:00') {contentString += '<b>Lundi:</b> '+ relaisSocolissimo.horairesOuvertureLundi + '<br/>'}
	    if (relaisSocolissimo.horairesOuvertureMardi!='00:00-00:00 00:00-00:00') {contentString += '<b>Mardi:</b> '+ relaisSocolissimo.horairesOuvertureMardi + '<br/>'}
	    if (relaisSocolissimo.horairesOuvertureMercredi!='00:00-00:00 00:00-00:00') {contentString += '<b>Mercredi:</b> '+ relaisSocolissimo.horairesOuvertureMercredi + '<br/>'}
	    if (relaisSocolissimo.horairesOuvertureJeudi!='00:00-00:00 00:00-00:00') {contentString += '<b>Jeudi:</b> '+ relaisSocolissimo.horairesOuvertureJeudi + '<br/>'}
	    if (relaisSocolissimo.horairesOuvertureVendredi!='00:00-00:00 00:00-00:00') {contentString += '<b>Vendredi:</b> '+ relaisSocolissimo.horairesOuvertureVendredi + '<br/>'}
	    if (relaisSocolissimo.horairesOuvertureSamedi!='00:00-00:00 00:00-00:00') {contentString += '<b>Samedi:</b> '+ relaisSocolissimo.horairesOuvertureSamedi + '<br/>'}
	    if (relaisSocolissimo.horairesOuvertureDimanche!='00:00-00:00 00:00-00:00') {contentString += '<b>Dimanche:</b> '+ relaisSocolissimo.horairesOuvertureDimanche}
	}
    if (relaisSocolissimo.accesPersonneMobiliteReduite) { contentString += '<img src="/skin/frontend/base/default/images/socolissimo/picto_handicap.png" />'; }
    if (relaisSocolissimo.listeConges) { contentString += '<br/><b>Periodes de fermeture :</b>'; 
		for (i=0; i<relaisSocolissimo.listeConges.length; i++) {
			fermeture = relaisSocolissimo.listeConges[i];
			datedu = fermeture.deb_periode_fermeture;
			dateau = fermeture.fin_periode_fermeture;
			contentString += '<br/>du ' + datedu.substring(8,10) + '/' + datedu.substring(5,7) + '/' + datedu.substring(0,4) + ' au ' + dateau.substring(8,10) + '/' + dateau.substring(5,7) + '/' + dateau.substring(0,4);
		}
	}
    
    contentString += '</p></div>';
    contentString = contentString.replace(new RegExp(' 00:00-00:00', 'g'),''); //on enl�ve les horaires de l'apr�s midi si ils sont vides
    
	infowindow = new google.maps.InfoWindow({
		content: contentString
	});
	
	return infowindow;
}

var openedInfowindow;
function attachClick(marker,infowindow, index){
	//Clic sur le relais dans la colonne de gauche
	jQuery("#point_retrait_"+index).click(function() {
			//fermer la derniere infobulle ouverte
			if(openedInfowindow) {
				openedInfowindow.close();
		    }
			//ouvrir l'infobulle
		   infowindow.open(map,marker);
		   openedInfowindow=infowindow;
		   
		});
		
	//Clic sur le marqueur du relais dans la carte
	google.maps.event.addListener(marker, 'click', function() {
			//fermer la derniere infobulle ouverte
			if(openedInfowindow) {
				openedInfowindow.close();
		    }
			//ouvrir l'infobulle
		   infowindow.open(map,marker);
		   openedInfowindow=infowindow;
		   
		});
}

var relaisChoisi;
function choisirRelais(index) {
	relaisChoisi = listRelaisSocolissimo[index];
	//on - resélectionne le radio correspondant au type du relais choisi.
	//   - affiche son nom
	//   - positionne son l'identifiant dans le champ input
	//   - on affiche eventuellement le champ téléphone
	jQuery("#socolissimo-location input:radio").each(function(index, element){
		radio = jQuery(element);
		var type = "poste";
		if (relaisChoisi.typeDePoint=="A2P") {
			type = "commercant";
		} else if(relaisChoisi.typeDePoint=="CIT") {
			type = "cityssimo";
		};
		if (radio.val() == type) {
			checkDisplayPhone(radio);
			radio.prop("checked", "checked");
			radio.parent().next().html('<span>' + relaisChoisi.nom + '</span>' + relaisChoisi.adresse1 + ' ' +relaisChoisi.codePostal + ' ' +relaisChoisi.localite);
			jQuery("#socolissimo-location input[name=relais_socolissimo]").val(relaisChoisi.identifiant);
		} else {
			radio.prop("checked", "");
			radio.parent().next().text("");
		}
	});

	overlayApi.close(); 
	return false;
}

Validation.add('valid-telephone-portable', 'Veuillez saisir un numéro de téléphone portable correct', function(v) {
    return (/^0(6|7)\d{8}$/.test(v) && !(/^0(6|7)(0{8}|1{8}|2{8}|3{8}|4{8}|5{8}|6{8}|7{8}|8{8}|9{8}|12345678)$/.test(v)));
});
