/**
 * Addonline_SoColissimo
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2011 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */

/**
 * Fonction startWith sur l'objet String de javascript
 */
String.prototype.startWith = function(t, i) { if (i==false) { return
(t == this.substring(0, t.length)); } else { return (t.toLowerCase()
== this.substring(0, t.length).toLowerCase()); } } 

/**
 * Variables globales
 */

var glsMyPosition;
var glsListRelais=new Array();
var glsMap;
var glsOpenedInfowindow;
var glsRelaisChoisi;

/**
 * Initialisation au chargement de la page
 */
jQuery(function($) {	
	
	
	//Cas du onestep checkout, si on change l'adresse de livraison après avoir choisi gls
	/* jQuery('.onestepcheckout-index-index .address-select').live("change", function() {
		if(jQuery('#gls-location').size() <= 0 ){	
			$("#attentionSoColissimo").remove();
			$("label[for=\"billing-address-select\"]").parent().before('<p id="attentionSoColissimo" style="font-weight:bold;color:red;text-align:justify; padding-right:5px;">Suite à la modification de votre adresse et si votre mode de livraison est So Colissimo, veuillez séléctionner votre point de retrait en cliquant sur le mode de livraison.</p>');
		}
	}); */	
	
	/** 
	 * Sur l'événement change des radios boutons de choix de mode de livraison
	 */
	$("input[id^=\"s_method_gls\"]").live("click", function() {		
		shippingGLSRadioCheck(this);
	});		
	
	/* Seules les saisies numériques sont autorisées dans les champs textes */
	$("#layer_gls_wrapper #cp_recherche").keypress(function(e) {
		var charCode = (e.which) ? e.which : e.keyCode;
		if (charCode > 31 && (charCode < 48 || charCode > 57)) {
			return false;
		}
		return true;
	});

	initGlsLogos();
	
});

/**
 * Initialise les logos, descriptions, style sur le radio bouttons gls
 * ceci est fait en javascript pour ne pas surcharger le template available.phtml et ainsi éviter des conflits avec d'autres modules.
 * (appelé au chargement du DOM mais aussi au rechargement ajax (voir Checkout.prototype.setStepResponse dans  gls\additional.phtml)
 */
function initGlsLogos() {
	
	jQuery("input[id^=\"s_method_gls\"]").each(function(index, element){
		
		if(!jQuery("body").hasClass("onestepcheckout-index-index")) {
			jQuery(element).parents("dd").addClass("s_method_gls");
		} else {
			jQuery("input[id^=\"s_method_gls\"]").parents("dt").addClass("s_method_gls");
			var dd = jQuery("input[id^=\"s_method_gls\"]").eq(0).parents("dt").prev().addClass("s_method_gls-title");
		}
		
		jQuery(element).prop("checked", "");
		var typeSocolissimo =  getTypeGlsFromRadio(jQuery(element), false);
		if (typeSocolissimo) {
			var radioParent = jQuery(element).parent();
			radioParent.prepend('<img src="/skin/frontend/base/default/images/gls/picto_'+typeSocolissimo+'.png" >');
			var typeSocolissimoDesc =  getTypeGlsFromRadio(jQuery(element), true);
			jQuery("#gls_description_"+typeSocolissimoDesc).clone().appendTo(radioParent).attr("style","display:block;");
			if (typeSocolissimo=='rdv' || (typeSocolissimo=='domicile' && jQuery("input[id^=\"s_method_gls_rdv\"]").length==0)) {
				radioParent.addClass("first");
			}
		}
	});
	/*/if(!jQuery("body").hasClass("onestepcheckout-index-index")) {
		jQuery(".s_method_gls").prev().addClass("s_method_gls-title").append('<img src="/skin/frontend/base/default/images/gls/gls.png" >');
	} else {
		jQuery(".s_method_gls-title").append('<img src="/skin/frontend/base/default/images/gls/gls.png" >');
	}*/
}

function getTypeGlsFromRadio(radio, forDescription) {
	var shippingMethod = radio.attr("value");
	var typeGls = shippingMethod.replace("gls_","");
	if (typeGls.startWith("tohome")) {		
		return 'tohome';
	} else if (typeGls.startWith("toyou")) {
		return 'toyou';
	} else if (typeGls.startWith("relay")){ 
		return 'relay';
	} else {
		//Sinon c'est un type de livraison inconnu
		alert("Mauvaise configuration du module GLS : dans le champ configuration le code doit commencer par tohome, toyou ou relay");
		return false;
	}
}

function shippingGLSRadioCheck(element) {	
	var glsRadio = jQuery(element);	
	var typeGls =  getTypeGlsFromRadio(glsRadio, false);				
	if(typeGls == "relay"){
		//on affiche le picto de chargement étape suivante du opc
		jQuery("#shipping-method-please-wait").show();		
		url = "/gls/ajax/selector/"			
			jQuery.ajax({
				url: url,
				success: function(data){					
					jQuery("#layer_gls").html(data);
					resetGLSShippingMethod();
					geocoder = new google.maps.Geocoder();
					geocodeGLSAdresse();					
				}
			});					
	}
}

function resetGLSShippingMethod() {
	jQuery("input[name='shipping_method']:checked").prop("checked","");
}

function geocodeGLSAdresse() {
		
	var searchAdress = jQuery('#cp_recherche').val();		
	if ((typeof google) != "undefined") {
		var geocoder = new google.maps.Geocoder();		
		geocoder.geocode({'address': searchAdress}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				glsMyPosition = results[0].geometry.location;				
	 			//on met à jour la carte avec cette position
				loadMap();				
				loadListePointRelais();
			} else {
				alert('Adresse invalide '+searchAdress);
			}
	    });
	} else {
		alert("Géolocalisation de l'adresse impossible, vérifiez votre connexion internet (Google inaccessible).");
	}
}

function changeMap() {
	if (glsMyPosition!=undefined) {		
		loadListePointRelais();
	}
}

function loadListePointRelais() {	
	if(jQuery("#cp_recherche").val()){
		url = "/gls/ajax/listPointsRelais"
		url = url + "/zipcode/" + jQuery("#cp_recherche").val() + "/country/" + "FR";
		jQuery.ajax({
			url: url,
			success: function(data){				
				jQuery("#col_droite_gls").html(data);	
				showGLSMap();
			}
		});		
	}
}

function loadMap(){
	var latlng = new google.maps.LatLng(glsMyPosition.lat(),glsMyPosition.lng());
	mapOptions = {
	    zoom: 11,
	    center: latlng,
	    mapTypeId: google.maps.MapTypeId.ROADMAP,
	    disableDefaultUI: true,
		zoomControlOptions: {
			position: google.maps.ControlPosition.RIGHT_TOP
	    }
	}
	stylez =
		[
		  {
		    "stylers": [
		      { "saturation": -100 }
		    ]
		  },{
		    "featureType": "water",
		    "elementType": "geometry",
		    "stylers": [
		      { "color": "#808091" }
		    ]
		  },{
		    "featureType": "landscape",
		    "elementType": "geometry",
		    "stylers": [
		      { "color": "#ffffff" }
		    ]
		  },{
		    "featureType": "poi",
		    "stylers": [
		      { "visibility": "off" }
		    ]
		  },{
		    "featureType": "administrative.locality",
		    "elementType": "labels.text.fill",
		    "stylers": [
		      { "color": "#000000" },
		      { "visibility": "on" }
		    ]
		  },{
		    "featureType": "administrative.locality",
		    "elementType": "labels.text.stroke",
		    "stylers": [
		      { "color": "#ffffff" },
		      { "visibility": "off" }
		    ]
		  },{
		    "featureType": "road.highway",
		    "elementType": "geometry",
		    "stylers": [
		      { "visibility": "on" },
		      { "color": "#565656" }
		    ]
		  },{
		    "featureType": "road.highway.controlled_access",
		    "elementType": "geometry",
		    "stylers": [
		      { "color": "#000000" }
		    ]
		  },{
		    "featureType": "road.arterial",
		    "elementType": "geometry",
		    "stylers": [
		      { "visibility": "on" },
		      { "color": "#b3b3b3" }
		    ]
		  },{
		    "featureType": "road.local",
		    "elementType": "geometry",
		    "stylers": [
		      { "color": "#dddddd" }
		    ]
		  },{
		    "featureType": "transit.station.rail",
		    "elementType": "labels.icon",
		    "stylers": [
		      { "visibility": "on" },
		      { "gamma": 0.01 }
		    ]
		  },{
		    "featureType": "road.arterial",
		    "elementType": "labels.text.fill",
		    "stylers": [
		      { "visibility": "on" },
		      { "color": "#000000" }
		    ]
		  },{
		    "featureType": "landscape",
		    "stylers": [
		      { "visibility": "on" }
		    ]
		  },{
		    "featureType": "water",
		    "elementType": "labels.text.fill",
		    "stylers": [
		      { "visibility": "on" },
		      { "color": "#ffffff" }
		    ]
		  },{
		    "featureType": "water",
		    "elementType": "labels.text.stroke",
		    "stylers": [
		      { "color": "#919191" }
		    ]
		  }
		];
	
	glsRelayMap = new google.maps.Map(document.getElementById('map_gls'), mapOptions);
	//storePickupMap.setOptions({styles: stylez});	

	/*function buildZoomControl(map, div) {

		// Get the control DIV. We'll attach our control UI to this DIV.
		var controlDiv = jQuery(div);
		var zoomIn = jQuery("<div class='zoom-in' />");
		var zoomOut = jQuery("<div class='zoom-out' />");
		controlDiv.append(zoomIn);
		controlDiv.append(zoomOut);

		// Setup the click event listener for Set Home:
		// Set the control's home to the current Map center.
		google.maps.event.addDomListener(zoomIn[0], 'click', function() {
			map.setZoom(map.getZoom() + 1);
		});
		google.maps.event.addDomListener(zoomOut[0], 'click', function() {
			map.setZoom(map.getZoom() - 1);
		});
	}

	var zoomControlDiv = document.createElement('div');
	buildZoomControl(storePickupMap, zoomControlDiv);
	zoomControlDiv.index = 1;
	storePickupMap.controls[google.maps.ControlPosition.TOP_RIGHT].push(zoomControlDiv);*/
	
	
	if (jQuery("#layer_gls").data("overlay") == undefined) {
		jQuery("#layer_gls").overlay({
			mask: {
				color: '#000', 
				loadSpeed: 200, 
				opacity: 0.5 
			}, 
			load: true,
			closeOnClick: false,
			top: "center",	
//			onBeforeClose : function(event){
//				//si on n'a pas choisi de type de livraison socolissimo, on décoche le mode de livraison socolissimo 
//				var telephoneElt = jQuery("#socolissimo-hook input[name='tel_socolissimo']");
//				if (!telephoneElt || telephoneElt.val() == undefined) {
//					resetShippingMethod();
//				} else {
//					var shippingMethod = jQuery("input[name='shipping_method']:checked").val();
//					if (shippingMethod.startWith("socolissimo_poste") || shippingMethod.startWith("socolissimo_commercant") || shippingMethod.startWith("socolissimo_cityssimo")) {
//						var relaisElt = jQuery("#socolissimo-hook input[name='relais_socolissimo']");
//						if (!relaisElt || relaisElt.val() == undefined) {
//							resetShippingMethod();
//						}
//					}
//				}
//			}
			fixed: false
		});
	} else {
		jQuery("#layer_gls").data("overlay").load();
	}	
	
	jQuery("#shipping-method-please-wait").hide();

}

function showGLSMap() {	
	if ((typeof google)!="undefined") {
		var init = false;		
		//google.maps.event.addListener(glsRelayMap, 'tilesloaded', function () {			
			if (!init){				
				jQuery('.gls_point_relay').each(function(){	
					
					var relayPosition =  new google.maps.LatLng(jQuery(this).find('.GLS_relay_latitude').text(), jQuery(this).find('.GLS_relay_longitude').text());
					markerGLS = new google.maps.Marker({
					    map: glsRelayMap,
					    position: relayPosition,
					    title : jQuery(this).find('.GLS_relay_name').text(),
					    icon : '/skin/frontend/base/default/images/socolissimo/picto_cityssimo.png'
					});					
					infowindowGLS=infoGLSBulleGenerator(jQuery(this));
					attachGLSClick(markerGLS,infowindowGLS, jQuery('.gls_point_relay').size());
				});
			}
			init=true;
		//});
	}
}

//générateur d'infobulle
function infoGLSBulleGenerator(relay) {	

	contentString = '<div class="info-window">'

	contentString += '<span class="store-name">' + relay.find('.GLS_relay_name').text() + '</span>';

	contentString += '<div class="col-left">' +
					relay.find('.GLS_relay_address').text() + '<br/>' +
    				relay.find('.GLS_relay_zipcode').text() + ' ' + relay.find('.GLS_relay_city').text() + '<br/>';

	
	contentString += "<br/><br/><a href='#' class='choose-relay-point' data-relayid="+relay.find('.GLS_relay_id').text()+">Choisir ce lieu</a>";
	contentString += "</div>"
	contentString += "<div class='col-right'>"+relay.find('.GLS_relay_hours').text();	
	contentString += 'Dimanche: fermé<br>';


	contentString += "</div>";
	contentString += "</div>";

	infowindow = new google.maps.InfoWindow({
		content: contentString
	});

	return infowindow;
}


function attachGLSClick(markerGLS,infowindowGLS, index){
	//Clic sur le relais dans la colonne de gauche
	jQuery("#gls_point_relay_"+index).click(function() {
			//fermer la derniere infobulle ouverte
			if(glsOpenedInfowindow) {
				glsOpenedInfowindow.close();
		    }
			//ouvrir l'infobulle
		   infowindow.open(glsMap,marker);
		   glsOpenedInfowindow=infowindowGLS;
		   
		});
		
	//Clic sur le marqueur du relais dans la carte
	google.maps.event.addListener(markerGLS, 'click', function() {			
			//fermer la derniere infobulle ouverte
			if(glsOpenedInfowindow) {
				glsOpenedInfowindow.close();
		    }			
			//ouvrir l'infobulle
			infowindowGLS.open(glsRelayMap,markerGLS);
		    glsOpenedInfowindow=infowindowGLS;		   
		});
}

function choisirRelais(index) {
	
	glsRelaisChoisi = glsListRelais[index];
	jQuery("#gls-hook").html('<input type="hidden" name="relais_gls" value="'+glsRelaisChoisi.identifiant+'" />'+
									'<input type="hidden" name="reseau_gls" value="'+glsRelaisChoisi.code_reseau+'" />');
	
	jQuery("input[id^=\"s_method_gls\"]").each(function(index, element){
		//on sélectionne le bon radio, si on a changé de type de relais sur la carte, et on change le texte du numéro de téléphone
		var radio = jQuery(element);
		var types = new Array('poste','commercant','cityssimo');
		var len=types.length;
		for (var index=0; index<len; index++) {
			var glsType = types[index];
			if (radio.val().startWith("gls_"+glsType)) {
				if (glsRelaisChoisi.type==glsType) {
					radio.prop("checked", "checked");	//on utilise prop au lieu de attr pour que le radio soit bien mis à jour
					jQuery("#gls-telephone span."+glsType).attr("style","display:block;");
				} else {
					radio.prop("checked", "");	//on utilise prop au lieu de attr pour que le radio soit bien mis à jour
					jQuery("#gls-telephone span."+glsType).attr("style","display:none;");
				}
			}
		}
	});
	
	jQuery("#gls-map").hide();
	jQuery("#gls-telephone").show();
	
	return;
}

function validerTelephone() {
	
	 if(glsTelephoneForm.validator && glsTelephoneForm.validator.validate()){
    	var telephone = jQuery("#gls-telephone input[name='tel_gls']").val();
    	jQuery("#gls-hook").append('<input type="hidden" name="tel_gls" value="'+telephone+'" />');
    	jQuery("#layer_gls").data("overlay").close();
    }
	return false;
}

/** ajout de la fonction de validation numéro de téléphone portable */
Validation.add('valid-telephone-portable', 'Veuillez saisir un numéro de téléphone portable correct', function(v) {
    return (/^0(6|7)\d{8}$/.test(v) && !(/^0(6|7)(0{8}|1{8}|2{8}|3{8}|4{8}|5{8}|6{8}|7{8}|8{8}|9{8}|12345678)$/.test(v)));
});

Validation.add('valid-telephone-portable-belgique', 'Veuillez saisir un numéro de téléphone portable correct', function(v) {
	//Pour les destinataires belges, le numéro de téléphone portable doit commencer par le caractère + suivi de 324, suivi de 8 chiffres
	return (/^\+324\d{8}$/.test(v) && !(/^\+324(0{8}|1{8}|2{8}|3{8}|4{8}|5{8}|6{8}|7{8}|8{8}|9{8}|12345678)$/.test(v)));
});

