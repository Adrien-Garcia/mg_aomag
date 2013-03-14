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

var socolissimoMyPosition;
var socolissimoOverlayApi;
var socolissimoListRelais=new Array();
var socolissimoMap;
var socolissimoOpenedInfowindow;
var socolissimoRelaisChoisi;

/**
 * Initialisation au chargement de la page
 */
jQuery(function($) {
	
	//Cas du onestep checkout, si on change l'adresse de livraison après avoir choisi socolissimo
	jQuery('.address-select').live("change", function() {
		if(jQuery('#socolissimo-location').size() <= 0 ){	
			$("#attentionSoColissimo").remove();
			$("label[for=\"billing-address-select\"]").parent().before('<p id="attentionSoColissimo" style="font-weight:bold;color:red;text-align:justify; padding-right:5px;">Suite à la modification de votre adresse et si votre mode de livraison est So Colissimo, veuillez séléctionner votre point de retrait en cliquant sur le mode de livraison.</p>');
		}
	});	
	
	/** pour onestep checkout ...
	var $ma_div = jQuery("#socolissimo-location");
	jQuery("input[id^='s_method_socolissimo']").parents('dt').append($ma_div);
	jQuery("#socolissimo-location-orig").hide();
	**/
	
	/** 
	 * Sur l'événement change des radios boutons de choix de mode de livraison
	 */
	$("input[id^=\"s_method\"]").live("change", function() {
		shippingRadioCheck(this);
	});		
	
	/** 
	 * Sur l'événement click des radios boutons de choix de type de livraison socolissimo
	$("label[for^='s_method_socolissimo']").live("click",function(){
		reloadSocolissimo();
	});
	 */
	
});

function shippingRadioCheck(element) {	
	var socoRadio = jQuery(element);
	if (element.id.startWith("s_method_socolissimo") && socoRadio.attr("checked", "checked")){		
		/*
		if(jQuery('#socolissimo-location').size() <= 0 ) { //cas onestepcheckout
			var $ma_div = jQuery("#socolissimo-location-orig").clone();		
			jQuery("input[id^='s_method_socolissimo']").parents('dt').append($ma_div);		
			jQuery("input[id^='s_method_socolissimo']").next().next().attr("id","socolissimo-location");
		} 
		*/
		if (jQuery("#socolissimo-location").size()==0) {
			socoRadio.parent().append("<div id=\"socolissimo-location\" ></div>");
		}
		jQuery("#socolissimo-location").append("<img src=\"ajax-loader.gif\" />");
		url = "/socolissimo/ajax/selector?"
		jQuery.ajax({
			  url: url,
			  success: function(data){
				  jQuery("#socolissimo-location").append(data);
			  }
		});
		//TODO : mettre l'attribut rel qui va bien sur le element ? 
		socolissimoOverlayApi = jQuery(element).overlay({
		    expose: { 
		        color: '#000', 
		        loadSpeed: 200, 
		        opacity: 0.5 
		    }, 
		    closeOnClick: false,
		    top: "center",
			onBeforeClose : function(event){
				//si on n'a pas choisi de type de livraison socolissimo, on décoche le mode de livraison socolissimo ?
			},
			fixed: false,
		    api: true 
		  });
		socolissimoOverlayApi.load();
		
	} else {		
		socolissimoOverlayApi.close();
		jQuery("#socolissimo-location").hide();
	}
}

/*
function reloadSocolissimo(){		
	if(jQuery('#socolissimo-location').size() <= 0 ){		
		var ma_div_clone = jQuery("#socolissimo-location-orig").clone();	
		var cible = jQuery("input[id^='s_method_socolissimo']").parents('dt');
		cible.delay(100).append(ma_div_clone);		
		jQuery("input[id^='s_method_socolissimo']").next().next().attr("id","socolissimo-location");
		jQuery("#socolissimo-location").show();
	}
}
*/

function socolissimoRadioCheck(input) {
	//on commence par ré-initialiser le relais qui a pu être déjà choisi 
	jQuery("#socolissimo-location input[name=relais_socolissimo]").val("");
	jQuery("#socolissimo-location input[name=type_socolissimo_choisi]").val("");
	jQuery("#socolissimo-location .nom_relais").text("");	
	socolissimoRelaisChoisi=undefined;
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
	
		/* TODO : le layer est déjà chargé : gérer le non choix du relais
		socolissimoOverlayApi = jQuery(input).overlay({
		    expose: { 
		        color: '#000', 
		        loadSpeed: 200, 
		        opacity: 0.5 
		    }, 
		    closeOnClick: false,
		    top: "center",
			onBeforeClose : function(event){
				//si on n'a pas choisi de relais on décoche le mode de livraison
				if (socolissimoRelaisChoisi == undefined || socolissimoRelaisChoisi == null) {
					jQuery("#socolissimo-location input[name=type_socolissimo]").attr("checked","");
				}
			},
			fixed: false,
		    api: true 
		  });
		socolissimoOverlayApi.load();
		*/
	} else {
		jQuery("#socolissimo-location input[name=type_socolissimo_choisi]").val(input.value);
	}
}

function checkDisplayPhone(input) {
	if (jQuery(input).val() == "rdv" || jQuery(input).val() == "cityssimo" || jQuery(input).val() == "commercant" || jQuery(input).val() == "poste" || jQuery(input).val() == "livraison"){
		jQuery("#socolissimo-location label.portable").show().css("display:block;");
		jQuery("#socolissimo-location label.portable span").hide();
		jQuery("#socolissimo-location label.portable span."+jQuery(input).val()).show();
	} else {
		jQuery("#socolissimo-location label.portable").hide().css("display:hide;");;
	}
}

function geocodeAdresse() {

	//TODO : charger les infos de l'adresse directement dans le template, vu qu'on le recharge en ajax à chaque fois
	if (jQuery("#socolissimo_city_select option").length > 0 && jQuery("#socolissimo_city_select")[0].selectedIndex == 0) {
		if(jQuery('#billing\\:city').val() == ""){
			alert("Veuillez sélectionner une commune");
			return;
		}
	}
	if (jQuery("#socolissimo_postcode").val() == "") {
		if(jQuery('#billing\\:postcode').val() == ""){
			alert("Veuillez saisir un code postal");
			return;
		}
	}
	if (jQuery("#socolissimo_street").val() == "") {
		if(jQuery('#billing\\:street1').val() == ""){
			alert("Veuillez saisir une adresse");
			return;
		}
	}
	var geocoder = new google.maps.Geocoder();
	if(jQuery("#socolissimo_street").val() == "") {
		var searchAdress = jQuery('#billing\\:street1').val() + ' ' +jQuery('#billing\\:street2').val() + ' ' + jQuery('#billing\\:postcode').val() + ' ' + jQuery('#billing\\:city').val();
	} else{
		var searchAdress = jQuery("#socolissimo_street").val() + ' ' + jQuery("#socolissimo_postcode").val() + ' ' + jQuery("#socolissimo_city").text();
	}
	//console.log('Search adresse : ' + searchAdress);
	geocoder.geocode({'address': searchAdress}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
 			socolissimoMyPosition= results[0].geometry.location;
 			//on met à jour la carte avec cette position
 			changeMap();
		} else {
			alert('Adresse invalide '+searchAdress);
		}
    });
}

function changeMap() {
	if (socolossimoMyPosition!=undefined) {
		loadListeRelais();
	}
}

function loadListeRelais() {
	jQuery(".loader-wrapper").fadeTo(300, 1);
	url = "/socolissimo/ajax/listrelais?"
	jQuery("#layer_socolissimo input:checkbox").each(function(index, element){
		check = jQuery(element);
		url = url + check.val() + "=" + check.attr("checked") + "&";
	});
	if(jQuery("#socolissimo_street").val() == "") {		
		url = url + "adresse=" + jQuery('#billing\\:street1').val() + ' ' +jQuery('#billing\\:street2').val() + "&zipcode=" + jQuery('#billing\\:postcode').val()+ "&ville=" + jQuery('#billing\\:city').val();
		jQuery("#socolissimo_street").val(jQuery('#billing\\:street1').val()+' '+jQuery('#billing\\:street2').val());
		jQuery("#socolissimo_postcode").val(jQuery('#billing\\:postcode').val());
		jQuery("#socolissimo_city").text(jQuery('#billing\\:city').val());
	} else{
		url = url + "adresse=" + jQuery("#socolissimo_street").val() + "&zipcode=" + jQuery("#socolissimo_postcode").val()+ "&ville=" + jQuery("#socolissimo_city").text();
	}	
	url = url + "&latitude=" + socolissimoMyPosition.lat() + "&longitude=" + socolissimoMyPosition.lng();
	jQuery.getJSON( url, function(response) {
		socolissimoListRelais = response.items;
		jQuery("#adresses_socolissimo").html(response.html);
		showMap();
		jQuery(".loader-wrapper").fadeTo(300, 0).hide();
	});
	
	
}

function showMap() {
	var myOptions = {
	    	zoom: 15,
	    	center: socolissimoMyPosition,
	    	mapTypeId: google.maps.MapTypeId.ROADMAP
	}
	socolissimoMap = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	iconUrl = jQuery("#layer_socolissimo .ligne1").css("background-image");
	iconMatch = iconUrl.match("url\\(\"(.*)\"\\)");
	if (iconMatch == null) {
		//chrome
		iconMatch = iconUrl.match("url\\((.*)\\)");
	}
	iconUrl = iconMatch[1];
	var marker = new google.maps.Marker({
	    map: socolissimoMap, 
	    position: socolissimoMyPosition,
	    icon : iconUrl
	});
	var init = false;
	google.maps.event.addListener(socolissimoMap, 'tilesloaded', function () {
		if (!init){			
			for (icounter=0; icounter<socolissimoListRelais.length; icounter++) {					
				relaisSocolissimo = socolissimoListRelais[icounter];				
				var relaisPosition =  new google.maps.LatLng(relaisSocolissimo.latitude,relaisSocolissimo.longitude);				
				marker = new google.maps.Marker({
				    map: socolissimoMap, 
				    position: relaisPosition,
				    title : relaisSocolissimo.libelle,
				    icon : relaisSocolissimo.urlPicto
				});								
				if (!socolissimoMap.getBounds().contains(relaisPosition)){
					newBounds = socolissimoMap.getBounds().extend(relaisPosition);
					socolissimoMap.fitBounds(newBounds);
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
	contentString = '<div class="adresse">'+
    '<b>'+relaisSocolissimo.libelle+ '</b><br/>'+
    '<b>'+relaisSocolissimo.adresse+ ' ' + relaisSocolissimo.code_postal + ' ' + relaisSocolissimo.commune + '</b><br/>'+
    '<b>Horaires d\'ouverture :</b>'+
    '<p>';
    if (relaisSocolissimo.horaire_lundi!='00:00-00:00 00:00-00:00') {contentString += '<b>Lundi:</b> '+ relaisSocolissimo.horaire_lundi + '<br/>'}
    if (relaisSocolissimo.horaire_mardi!='00:00-00:00 00:00-00:00') {contentString += '<b>Mardi:</b> '+ relaisSocolissimo.horaire_mardi + '<br/>'}
    if (relaisSocolissimo.horaire_mercredi!='00:00-00:00 00:00-00:00') {contentString += '<b>Mercredi:</b> '+ relaisSocolissimo.horaire_mercredi + '<br/>'}
    if (relaisSocolissimo.horaire_jeudi!='00:00-00:00 00:00-00:00') {contentString += '<b>Jeudi:</b> '+ relaisSocolissimo.horaire_jeudi + '<br/>'}
    if (relaisSocolissimo.horaire_vendredi!='00:00-00:00 00:00-00:00') {contentString += '<b>Vendredi:</b> '+ relaisSocolissimo.horaire_vendredi + '<br/>'}
    if (relaisSocolissimo.horaire_samedi!='00:00-00:00 00:00-00:00') {contentString += '<b>Samedi:</b> '+ relaisSocolissimo.horaire_samedi + '<br/>'}
    if (relaisSocolissimo.horaire_dimanche!='00:00-00:00 00:00-00:00') {contentString += '<b>Dimanche:</b> '+ relaisSocolissimo.horaire_dimanche}
    if (relaisSocolissimo.indicateur_acces) { contentString += '<img src="/skin/frontend/base/default/images/socolissimo/picto_handicap.png" />'; }
    if (relaisSocolissimo.fermetures.totalRecords>0) { contentString += '<br/><b>Periodes de fermeture :</b>'; 
		for (i=0; i<relaisSocolissimo.fermetures.items.length; i++) {
			fermeture = relaisSocolissimo.fermetures.items[i];
			datedu = fermeture.deb_periode_fermeture;
			dateau = fermeture.fin_periode_fermeture;
			contentString += '<br/>du ' + datedu.substring(8,10) + '/' + datedu.substring(5,7) + '/' + datedu.substring(0,4) + ' au ' + dateau.substring(8,10) + '/' + dateau.substring(5,7) + '/' + dateau.substring(0,4);
		}
	}
    contentString += '</p></div>';
    contentString = contentString.replace(new RegExp(' 00:00-00:00', 'g'),''); //on enlève les horaires de l'après midi si ils sont vides
    
	infowindow = new google.maps.InfoWindow({
		content: contentString
	});
	
	return infowindow;
}

function attachClick(marker,infowindow, index){
	//Clic sur le relais dans la colonne de gauche
	jQuery("#point_retrait_"+index).click(function() {
			//fermer la derniere infobulle ouverte
			if(socolissimoOpenedInfowindow) {
				socolissimoOpenedInfowindow.close();
		    }
			//ouvrir l'infobulle
		   infowindow.open(socolissimoMap,marker);
		   socolissimoOpenedInfowindow=infowindow;
		   
		});
		
	//Clic sur le marqueur du relais dans la carte
	google.maps.event.addListener(marker, 'click', function() {
			//fermer la derniere infobulle ouverte
			if(socolissimoOpenedInfowindow) {
				socolissimoOpenedInfowindow.close();
		    }
			//ouvrir l'infobulle
		   infowindow.open(socolissimoMap,marker);
		   socolissimoOpenedInfowindow=infowindow;
		   
		});
}

function choisirRelais(index) {
	socolissimoRelaisChoisi = socolissimoListRelais[index];
	//on - resélectionne le radio correspondant au type du relais choisi.
	//   - affiche son nom
	//   - positionne son l'identifiant dans le champ input
	//   - on affiche eventuellement le champ téléphone
	jQuery("#socolissimo-location input:radio").each(function(index, element){
		radio = jQuery(element);	
		if (radio.val() == socolissimoRelaisChoisi.type) {
			checkDisplayPhone(radio);
			radio.parent().next().html('<span>' + socolissimoRelaisChoisi.libelle + '</span>' + socolissimoRelaisChoisi.adresse + ' ' +socolissimoRelaisChoisi.code_postal + ' ' +socolissimoRelaisChoisi.commune);
			jQuery("#socolissimo-location input[name=relais_socolissimo]").val(socolissimoRelaisChoisi.id_relais);
			jQuery("#socolissimo-location input[name=type_socolissimo_choisi]").val(socolissimoRelaisChoisi.type);
		} else {
			radio.parent().next().text("");
		}
	});

	socolissimoOverlayApi.close(); 
	return false;
}

/** ajout de la fonction de validation numéro de téléphone portable */
Validation.add('valid-telephone-portable', 'Veuillez saisir un numéro de téléphone portable correct', function(v) {
    return (/^0(6|7)\d{8}$/.test(v) && !(/^0(6|7)(0{8}|1{8}|2{8}|3{8}|4{8}|5{8}|6{8}|7{8}|8{8}|9{8}|12345678)$/.test(v)));
});

//On surcharge la méthode validate de ShippingMethod définie dans opcheckout.js (dans le cas du onepagecheckout seulement)
if ((typeof ShippingMethod) != "undefined")  {
ShippingMethod.prototype.validate = function() {
    var methods = document.getElementsByName('shipping_method');
    if (methods.length==0) {
        alert(Translator.translate('Your order cannot be completed at this time as there is no shipping methods available for it. Please make necessary changes in your shipping address.').stripTags());
        return false;
    }

    if(!this.validator.validate()) {
        return false;
    }

    //SOCOLISSIMO
    for (var i=0; i<methods.length; i++) {
        if (methods[i].checked) {
    	    if (methods[i].value.startWith("socolissimo")) {
    	    	var typeSocosChoisi = document.getElementsByName('type_socolissimo_choisi');
    	    	for (var j=0; j<typeSocosChoisi.length; j++) {
    	    		if (typeSocosChoisi[j].value!='') {
                        return true;
                    }
                }
                alert('Socolissimo : ' + Translator.translate('Please specify shipping method.'));
                return false;
            } else {
                return true;
            }
        }
    }
    alert(Translator.translate('Please specify shipping method.').stripTags());
    return false;
}
}
//On surcharge la méthode setStepResponse de Checkout définie dans opcheckout.js (dans le cas du onepagecheckout seulement)
if ((typeof Checkout) != "undefined") {
	Checkout.prototype.setStepResponse = function(response){
        if (response.update_section) {
            //SOCOLISSIMO
        	$$('body #layer_socolissimo').each(function(e){ e.remove(); });
        	//FIN SOCOLISSIMO
        	$('checkout-'+response.update_section.name+'-load').update(response.update_section.html);
        }
        if (response.allow_sections) {
            response.allow_sections.each(function(e){
                $('opc-'+e).addClassName('allow');
            });
        }

        if(response.duplicateBillingInfo)
        {
            shipping.setSameAsBilling(true);
        }

        if (response.goto_section) {
            this.gotoSection(response.goto_section);
            return true;
        }
        if (response.redirect) {
            location.href = response.redirect;
            return true;
        }
        return false;
    }	
}