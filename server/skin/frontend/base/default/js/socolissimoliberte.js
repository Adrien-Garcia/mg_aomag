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
	
	jQuery('.address-select').live("change", function() {
		if(jQuery('#socolissimo-location').size() <= 0 ){	
			$("#attentionSoColissimo").remove();
			$("label[for=\"billing-address-select\"]").parent().before('<p id="attentionSoColissimo" style="font-weight:bold;color:red;text-align:justify; padding-right:5px;">Suite à la modification de votre adresse et si votre mode de livraison est So Colissimo, veuillez séléctionner votre point de retrait en cliquant sur le mode de livraison.</p>');
		}
	});	
	
	var $ma_div = jQuery("#socolissimo-location");
	jQuery("#s_method_socolissimoliberte_socolissimo").parents('dt').append($ma_div);
	jQuery("#socolissimo-location-orig").hide();
	
	$("input[id^=\"s_method\"]").live("change", function() {
		shippingRadioCheck(this);
	});		
	
	$("label[for=\"s_method_socolissimoliberte_socolissimo\"]").live("click",function(){
		reloadSocolissimo();
	});
	
});

String.prototype.startWith = function(t, i) { if (i==false) { return
(t == this.substring(0, t.length)); } else { return (t.toLowerCase()
== this.substring(0, t.length).toLowerCase()); } } 

function reloadSocolissimo(){		
	if(jQuery('#socolissimo-location').size() <= 0 ){		
		var ma_div_clone = jQuery("#socolissimo-location-orig").clone();	
		var cible = jQuery("#s_method_socolissimoliberte_socolissimo").parents('dt');
		cible.delay(100).append(ma_div_clone);		
		jQuery("#s_method_socolissimoliberte_socolissimo").next().next().attr("id","socolissimo-location");
		jQuery("#socolissimo-location").show();
	}
}

function shippingRadioCheck(element) {	
	if (element.id.startWith("s_method_socolissimoliberte") && jQuery(element).attr("checked", "checked")){		
		if(jQuery('#socolissimo-location').size() <= 0 ) { //cas onestepcheckout
			var $ma_div = jQuery("#socolissimo-location-orig").clone();		
			jQuery("#s_method_socolissimoliberte_socolissimo").parents('dt').append($ma_div);		
			jQuery("#s_method_socolissimoliberte_socolissimo").next().next().attr("id","socolissimo-location");
		} 
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
	if (jQuery(input).val() == "rdv" || jQuery(input).val() == "cityssimo" || jQuery(input).val() == "commercant" || jQuery(input).val() == "poste" || jQuery(input).val() == "livraison"){
		jQuery("#socolissimo-location label.portable").show().css("display:block;");
		jQuery("#socolissimo-location label.portable span").hide();
		jQuery("#socolissimo-location label.portable span."+jQuery(input).val()).show();
	} else {
		jQuery("#socolissimo-location label.portable").hide();
	}
}

function geocodeAdresse() {
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
	url = BASE_URL_SOCOLISSIMO + "socolissimoliberte/ajax/listrelais?"
	/*jQuery("#layer_socolissimo input:checkbox").each(function(index, element){
		check = jQuery(element);
		url = url + check.val() + "=" + check.attr("checked") + "&";
	});*/
	if(jQuery("#socolissimo_street").val() == "") {		
		url = url + "adresse=" + jQuery('#billing\\:street1').val() + ' ' +jQuery('#billing\\:street2').val() + "&zipcode=" + jQuery('#billing\\:postcode').val()+ "&ville=" + jQuery('#billing\\:city').val();
		jQuery("#socolissimo_street").val(jQuery('#billing\\:street1').val()+' '+jQuery('#billing\\:street2').val());
		jQuery("#socolissimo_postcode").val(jQuery('#billing\\:postcode').val());
		jQuery("#socolissimo_city").text(jQuery('#billing\\:city').val());
	} else{
		url = url + "adresse=" + jQuery("#socolissimo_street").val() + "&zipcode=" + jQuery("#socolissimo_postcode").val()+ "&ville=" + jQuery("#socolissimo_city").text();
	}	
	url = url + "&latitude=" + myPositionSocolissimo.lat() + "&longitude=" + myPositionSocolissimo.lng();
	jQuery.getJSON( url, function(response) {
		listRelaisSocolissimo = response.items;
		jQuery("#adresses_socolissimo").html(response.html);
		showMap();
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
			radio.prop("checked", "checked");
			radio.parent().next().html('<span>' + relaisChoisi.libelle + '</span>' + relaisChoisi.adresse + ' ' +relaisChoisi.code_postal + ' ' +relaisChoisi.commune);
			jQuery("#socolissimo-location input[name=relais_socolissimo]").val(relaisChoisi.id_relais);
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
    	    	var typeSocos = document.getElementsByName('type_socolissimo');
                for (var j=0; j<typeSocos.length; j++) {
                    if (typeSocos[j].checked) {
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