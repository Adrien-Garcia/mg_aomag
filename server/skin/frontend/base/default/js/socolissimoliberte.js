/**
 * Addonline_SoColissimoLiberte
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimoLiberte
 * @copyright   Copyright (c) 2011 Addonline
 * @author 	    Addonline (http://www.Addonline.fr)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
	if (element.id.startWith("s_method_socolissimoliberte") && jQuery(element).attr("checked", "checked")){
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
					  url: 'http://api.geonames.org/postalCodeSearchJSON?username=sylvain.pras&country=fr&postalcode='+postcode,
					  dataType:'jsonp',
					  jsonpCallback: 'reloadCities',
					  success: function(json){
						  var options = '';
						  for (i=0; i<json.postalCodes.length; i++){ 
							  commune = json.postalCodes[i].placeName;
							  options += '<option value="' + commune + '">' + commune + '</option>';
						  }
						  jQuery("#socolissimo_city_select").html(options);
						  jQuery("#socolissimo_city_select").attr("selectedIndex", -1);		
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
	if (jQuery("#socolissimo_city_select option").length > 0 && jQuery("#socolissimo_city_select").attr("selectedIndex") == -1) {
		alert("Veuillez sélectionner une commune");
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
function loadListeRelais() {
	jQuery(".loader-wrapper").fadeTo(300, 1);
	url = "/socolissimoliberte/ajax/listrelais?"
	jQuery("#layer_socolissimo input:checkbox").each(function(index, element){
		check = jQuery(element);
		url = url + check.val() + "=" + check.attr("checked") + "&";
	});
	url = url + "latitude=" + myPositionSocolissimo.lat() + "&longitude=" + myPositionSocolissimo.lng();
	jQuery.getJSON( url, function(response) {
		listRelaisSocolissimo = response.items;
		jQuery("#adresses_socolissimo").html(response.html);
		showMap();
		jQuery(".loader-wrapper").fadeTo(300, 0);
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
			for (i=0; i<listRelaisSocolissimo.length; i++) {
				relaisSocolissimo = listRelaisSocolissimo[i];
				var relaisPosition =  new google.maps.LatLng(relaisSocolissimo.latitude,relaisSocolissimo.longitude);
				marker = new google.maps.Marker({
				    map: map, 
				    position: relaisPosition,
				    title : relaisSocolissimo.libelle,
				    icon : relaisSocolissimo.urlPicto
				});
				if (!map.getBounds().contains(relaisPosition)){
					newBounds = map.getBounds().extend(relaisPosition);
					map.fitBounds(newBounds);
				}
				infowindow=infoBulleGenerator(relaisSocolissimo);
				attachClick(marker,infowindow, i);			
			}
		}
		init=true;
	});
}

//générateur d'infobulle
function infoBulleGenerator(relaisSocolissimo) {
	contentString = '<div class="adresse">'+
    '<b>'+relaisSocolissimo.libelle+ '</b><br/>'+
    '<b>'+relaisSocolissimo.adresse+ ' ' + relaisSocolissimo.code_postal + ' ' + relaisSocolissimo.commune + '</b><br/>'+
    '<b>Horaires d\'ouverture et de fermeture :</b>'+
    '<p>';
    if (relaisSocolissimo.horaire_lundi!='00:00-00:00 00:00-00:00') {contentString += '<b>Lundi:</b> '+ relaisSocolissimo.horaire_lundi + '<br/>'}
    if (relaisSocolissimo.horaire_mardi!='00:00-00:00 00:00-00:00') {contentString += '<b>Mardi:</b> '+ relaisSocolissimo.horaire_mardi + '<br/>'}
    if (relaisSocolissimo.horaire_mercredi!='00:00-00:00 00:00-00:00') {contentString += '<b>Mercredi:</b> '+ relaisSocolissimo.horaire_mercredi + '<br/>'}
    if (relaisSocolissimo.horaire_jeudi!='00:00-00:00 00:00-00:00') {contentString += '<b>Jeudi:</b> '+ relaisSocolissimo.horaire_jeudi + '<br/>'}
    if (relaisSocolissimo.horaire_vendredi!='00:00-00:00 00:00-00:00') {contentString += '<b>Vendredi:</b> '+ relaisSocolissimo.horaire_vendredi + '<br/>'}
    if (relaisSocolissimo.horaire_samedi!='00:00-00:00 00:00-00:00') {contentString += '<b>Samedi:</b> '+ relaisSocolissimo.horaire_samedi + '<br/>'}
    if (relaisSocolissimo.horaire_dimanche!='00:00-00:00 00:00-00:00') {contentString += '<b>Dimanche:</b> '+ relaisSocolissimo.horaire_dimanche}
    contentString += '</p></div>';
    
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
		if (radio.val() == relaisChoisi.type) {
			checkDisplayPhone(radio);
			radio.attr("checked", "checked");
			radio.parent().next().html('<span>' + relaisChoisi.libelle + '</span>' + relaisChoisi.adresse + ' ' +relaisChoisi.code_postal + ' ' +relaisChoisi.commune);
			jQuery("#socolissimo-location input[name=relais_socolissimo]").val(relaisChoisi.id_relais);
		} else {
			radio.attr("checked", "");
			radio.parent().next().text("");
		}
	});

	overlayApi.close(); 
	return false;
}

