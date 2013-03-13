
var Autocomplete = function(el, options){
  this.el = $(el);
  this.id = this.el.identify();
  this.el.setAttribute('autocomplete','off');
  this.suggestions = [];
  this.suggestionsPaths = [];
  this.suggestionsPrice = [];
  this.suggestionsProductIds = [];
  this.suggestBrands = [];
  this.suggestCategories = [];
  this.suggestionsImages = [];
  this.updateSeachFaces = false;
  this.manager = options.manager;
  this.ajaxBaseUrl = null;
  this.mediaUrl = null;
  this.queryFields = null;  
  this.incorrectkeywords = [];
  this.Autocompletemessage = null;
  this.didyoumeantext= '';
  this.timestamp = 0;
  this.categoriesFilter = [];
  this.data = [];
  this.badQueries = [];
  this.selectedIndex = -1;
  this.selectedItemIndex = 0;
  this.selectedProductId = null;
  this.currentValue = this.el.value;
  this.currentKeyword = null;
  this.intervalId = 0;
  this.cachedResponse = [];
  this.instanceId = null;
  this.onChangeInterval = null;
  this.ignoreValueChange = false;
  this.serviceUrl = options.serviceUrl;
  this.options = {
    autoSubmit:false,
    minChars:1,
    maxHeight:300,
    deferRequestBy:0,
    width:0,
    container:null,
    allowFilter:0,
	currencySign: '$',
    displayThumb:0,
  };
  if(options){ Object.extend(this.options, options); }
  if(Autocomplete.isDomLoaded){
    this.initialize();
  }else{
    Event.observe(document, 'dom:loaded', this.initialize.bind(this), false);
  }
};

Autocomplete.instances = [];
Autocomplete.isDomLoaded = false;

Autocomplete.getInstance = function(id){
  var instances = Autocomplete.instances;
  var i = instances.length;
  while(i--){ if(instances[i].id === id){ return instances[i]; }}
};

Autocomplete.highlight = function(value, re){
	value = value.toString();
	return value.replace(re, function(match){ return '<strong>' + match + '<\/strong>' });
};

Autocomplete.prototype = {

  killerFn: null,

  initialize: function() {
    var me = this;
    this.killerFn = function(e) {
      if (!$(Event.element(e)).up('.autocomplete')) {
        me.killSuggestions();
        me.disableKillerFn();
      }
    } .bindAsEventListener(this);

    if (!this.options.width) { this.options.width = this.el.getWidth(); }
    
    //Create a div element
    var div = new Element('div', { style: 'position:absolute;' });
    //Put some children div into parent div
    div.update('<div class="autocomplete-w1" id="solr_search_autocomplete_box" style="display:none;width:400px"><div id="didyoumean"  style="float:left;display:none" class="txt_suggestion didyoumean" >No results found for <span id="didyoumean_text" style="font-weight:bold"></span> - showing results for <span id="didyoumean_text_2" style="font-weight:bold"></span> instead.</div><div style="clear:both"></div><div><div class="autocomplete" id="solr_'+this.id+'_autocomplete_right' + '" style="display:none;padding:5px; width: 98%"></div><div class="left-side-bar" style="display:none" id="solr_search_autocomplete_left">&nbsp;</div></div><div id="solr_search_closed_button" class="closed-button" style="position:absolute;display:none">&nbsp;</div></div>');
    
    //Append all div to body tag
    this.options.container = $(this.options.container);
    if (this.options.container) {
      //this.options.container.appendChild(div);
      //this.fixPosition = function() { };
    } else {
      document.body.appendChild(div);
    }
    document.body.appendChild(div);
    //$('didyoumean').hide();
    //Get the div ID
    this.mainContainerId = div.identify();
    this.container = $('solr_'+this.id+'_autocomplete_right');
    this.box = $('solr_search_autocomplete_box');
    this.closebutton = $('solr_search_closed_button');
    this.leftContainer = $('solr_search_autocomplete_left');
    
    //if (this.options.sideBarWidth) { this.leftContainer.setStyle({width:(this.options.sideBarWidth)+'px'}); }
    if (this.options.sideBarWidth) { this.leftContainer.setStyle({width:'100%'}); }
    if (this.options.boxWidth) { this.box.setStyle({width:(this.options.boxWidth)+'px'}); }
    this.box.setStyle({padding:'5px'});
    
    if(this.options.allowFilter == 1){
    	this.leftContainer.show();
    }else{
    	this.leftContainer.remove();
    	//this.box.setStyle({width:(this.options.boxWidth - this.options.sideBarWidth)+'px'});
    }
    
    //This function called to set some css attributes to the parent div
    this.fixPosition();
    
    Event.observe(this.el, window.opera ? 'keypress':'keydown', this.onKeyPress.bind(this));
    Event.observe(this.el, 'keyup', this.onKeyUp.bind(this));
    Event.observe(this.el, 'click', this.onClick.bind(this));
    Event.observe(this.el, 'blur', this.enableKillerFn.bind(this));
    Event.observe(this.el, 'focus', this.fixPosition.bind(this));
    Event.observe(this.closebutton, 'click', this.closeAll.bind(this));
    this.instanceId = Autocomplete.instances.push(this) - 1;
  },
  closeAll: function(){
	this.box.hide();
	this.closebutton.hide();
  	this.container.hide();
  	this.leftContainer.hide();
  },
  hideAll: function(){
	  this.closebutton.hide();
	  this.container.hide();
	  this.leftContainer.hide();
	  this.box.hide();
  },
  fixPosition: function() {
    var offset = this.el.cumulativeOffset();
    var top = offset.top + this.el.getHeight();
    var left = offset.left - this.options.boxWidth + this.el.getWidth() - 10;
    
    if(this.options.allowFilter != 1){
    	//left += this.options.sideBarWidth - 10;
    }
    
	//var left = offset.left;
    //$(this.mainContainerId).setStyle({ top: (top) + 3 + 'px', left: (left - 32) + 'px' });
	$(this.mainContainerId).setStyle({ top: (top) + 'px', left: (left) + 'px' });
    this.closebutton.setStyle({ top: '-10px', left: (this.options.boxWidth - 12)+ 'px' });
    if(this.options.allowFilter != 1){
    	//this.closebutton.setStyle({top: '-10px', left: (this.options.boxWidth - this.options.sideBarWidth - 12)+ 'px'});
    }
  },

  enableKillerFn: function() {
    Event.observe(document.body, 'click', this.killerFn);
  },

  disableKillerFn: function() {
    //Event.stopObserving(document.body, 'click', this.killerFn);
  },

  killSuggestions: function() {
    this.stopKillSuggestions();
    this.intervalId = window.setInterval(function() { this.hide(); this.stopKillSuggestions(); } .bind(this), 1);
  },

  stopKillSuggestions: function() {
    window.clearInterval(this.intervalId);
  },
  catInputClick: function (e){
	  
	  var fqString = "";
	  var index = 0;
	  
	  $$('#'+this.leftContainer.identify()+' input.category').each(function(item){
		  if(item.checked === true){
			  if(index === 0){
				  fqString += 'category:"'+item.value+'"';
			  }else{
				  fqString += " OR "+'category:"'+item.value+'"';
			  }
			  index++;
		  }
	  })
	  if(fqString.length > 0){
		  //alert(this.options.container.identify());
		  $(this.options.container.identify()).appendChild(new Element('input',{type:'hidden',name:'fq',value:(fqString)}));
		  //alert('appended');
	  }
	 // $('search_filters').value = fqString;
	  this.manager.store.remove('fq');
	  this.manager.store.addByValue('fq', fqString);
	  this.updateSeachFaces = false;
	  this.getSuggestions();
  },
  brandInputClick: function (e){
	  var fqString = "";
	  var index = 0;
	  
	  $$('#'+this.leftContainer.identify()+' input.brand').each(function(item){
		  if(item.checked === true){
			  if(index === 0){
				  fqString += 'oemname_facets:"'+item.value+'"';
			  }else{
				  fqString += " OR "+'oemname_facets:"'+item.value+'"';
			  }
			  index++;
		  }
	  })
	  if(fqString.length > 0){
		  //alert(this.options.container.identify());
		  $(this.options.container.identify()).appendChild(new Element('input',{type:'hidden',name:'fq',value:(fqString)}));
		  //alert('appended');
	  }
	 // $('search_filters').value = fqString;
	  this.manager.store.remove('fq');
	  this.manager.store.addByValue('fq', fqString);
	  this.updateSeachFaces = false;
	  this.getSuggestions();
  }
  ,
  onKeyPress: function(e) {
    if (!this.enabled) { return; }
    // return will exit the function
    // and event will not fire
    switch (e.keyCode) {
      case Event.KEY_ESC:
        this.el.value = this.currentValue;
        this.hide();
        break;
      case Event.KEY_TAB:
      case Event.KEY_RETURN:
    	  if (this.selectedIndex === -1) {
          this.hide();
          return;
        }
        this.enterSelect(this.selectedItemIndex);
        if (e.keyCode === Event.KEY_TAB) { return; }
        break;
      case Event.KEY_UP:
        this.moveUp();
        break;
      case Event.KEY_DOWN:
        this.moveDown();
        break;
      default:
        return;
    }
    Event.stop(e);
  },

  onKeyUp: function(e) {
    switch (e.keyCode) {
      case Event.KEY_UP:
      case Event.KEY_DOWN:
        return;
    }
    clearInterval(this.onChangeInterval);
    if (this.currentValue !== this.el.value) {
      if (this.options.deferRequestBy > 0) {
        // Defer lookup in case when value changes very quickly:
        this.onChangeInterval = setInterval((function() {
          this.onValueChange();
        }).bind(this), this.options.deferRequestBy);
      } else {
        this.onValueChange();
      }
    }
  },

  onValueChange: function() {
    clearInterval(this.onChangeInterval);
    this.currentValue = this.el.value;
    this.selectedIndex = -1;
    if (this.ignoreValueChange) {
      this.ignoreValueChange = false;
      return;
    }
    this.suggestions = [];
    //this.hideAll();
    if (this.currentValue === '' || this.currentValue.length < this.options.minChars) {
    	this.hide();
    } else {
    	this.updateSeachFaces = true;
    	this.manager.store.remove('fq');
    	this.getSuggestions();
    }
  },
  onClick: function(){
	  this.suggestions = [];
	  //this.hideAll();
	  if (this.currentValue === '' || this.currentValue.length < this.options.minChars) {
	    	this.hide();
	    } else {
	    	this.updateSeachFaces = true;
	    	this.manager.store.remove('fq');
	    	this.getSuggestions();
	    }
  },
  getSuggestions: function() {
	  	this.manager.store.addByValue('q', this.currentValue);
	  	this.manager.store.addByValue('json.nl', 'map');
	  	this.manager.store.addByValue('rows', '5');
	  	this.manager.store.addByValue('fl', 'name_varchar,products_id,price_decimal,special_price_decimal,image_varchar,url_path_varchar');
	  	
		this.manager.store.addByValue('qf', 'textSearch');
	  	
	  	this.manager.store.addByValue('spellcheck', 'true');
	  	this.manager.store.addByValue('autocompletez', 'true');
	  	this.manager.store.addByValue('spellcheck.collate', 'true');
	
	  	this.manager.store.addByValue('facet', 'true');
	  	this.manager.store.addByValue('facet.field', 'category_path,manufacturer_facet');
//	  	this.manager.store.addByValue('facet.field', 'manufacturer_facet');
	
	  	this.manager.store.addByValue('facet.limit', '5');
	  	var timestamp = new Date().getTime();
	  	this.manager.store.addByValue('timestamp', timestamp);
	  	
	  	this.manager.store.addByValue('defType', 'dismax');
	  	this.timestamp = timestamp;
	  	this.manager.doRequest();	  
  },

  isBadQuery: function(q) {
    var i = this.badQueries.length;
    while (i--) {
      if (q.indexOf(this.badQueries[i]) === 0) { return true; }
    }
    return false;
  },

  hide: function() {
    this.enabled = false;
    this.selectedIndex = -1;
    this.hideAll();
    this.box.hide();
  },  
  suggest: function() {
    if (this.suggestions.length === 0 && this.currentValue.length == 0) {
      this.hide();
      this.box.hide();
      return;
    }
    var content = [];
    //var re = new RegExp('\\b' + this.currentValue.match(/\w+/g).join('|\\b'), 'gi');
    
    var re = new RegExp('\\b' + this.currentKeyword.match(/\w+/g).join('|\\b'), 'gi');

    var i = -1;
    for(key in this.suggestions)
	{
    	if(!isNaN(key)){
    		value = this.suggestions[key];
    		price = parseFloat(this.suggestionsPrice[key]);
    		price = price.toFixed(2);
    		image = this.suggestionsImages[key];
        	if(this.options.displayThumb == 1){
        		content.push((this.selectedIndex === i ? '<div id="solr_suggest_index_'+key+'" style="padding:2px;" class="product selected suggested-item"' : '<div id="solr_suggest_index_'+key+'" class="product suggested-item"'), ' style="padding:2px;" title="', value, '" onclick="Autocomplete.instances[', this.instanceId, '].select(', key, ');" onmouseout="$(this).removeClassName(\'selected\').addClassName(\'suggested-item\')" onmouseover="$(this).addClassName(\'selected\')"><div class="solr_search_suggest_thumb"><img src="'+this.options.mediaUrl+'catalog/product/sb_thumb/'+this.suggestionsProductIds[key]+'.jpg" style="" /></div>', '<span class="solr_search_suggest_item_title">'+Autocomplete.highlight(value, re)+'</span><span class="solr_search_suggest_item_subtitle">'+price+' '+this.options.currencySign+'</span>', '</div>');
        	}else{
        		content.push((this.selectedIndex === i ? '<div id="solr_suggest_index_'+key+'" style="padding:2px;" class="product selected suggested-item"' : '<div id="solr_suggest_index_'+key+'" class="product suggested-item"'), ' style="padding:2px;" title="', value, '" onclick="Autocomplete.instances[', this.instanceId, '].select(', key, ');" onmouseout="$(this).removeClassName(\'selected\').addClassName(\'suggested-item\')" onmouseover="$(this).addClassName(\'selected\')">','<span class="solr_search_suggest_item_title">'+Autocomplete.highlight(value, re)+'</span><span class="solr_search_suggest_item_subtitle">'+price+' '+this.options.currencySign+'</span>', '</div>');
        	}
    		
        	i++;
        	if(i>= 20){
        		break;
        	}
    	}    	
	}

    this.enabled = true;
    this.box.setStyle('display:block');
    if(this.suggestions.length > 0){
    	this.container.update('<div class="result_items">'+this.Autocompletemessage+'</div>'+content.join('')).show();
    	
    	this.container.appendChild(new Element('div',{style: 'font-family:Arial, Helvetica, sans-serif;font-size:22px;color:#a7c6dd;text-transform:uppercase;clear:both;', 'class':'with-sep'}).update('Marques'));
    	for(key_brand in this.suggestBrands)
    	{
    		if(!isNaN(key_brand)){
    			var brandLi = new Element('span',{id:'solr_suggest_index_'+key_brand,style:'cursor:pointer;',onclick:'Autocomplete.instances['+this.instanceId+'].select('+key_brand+')',onmouseover:'$(this).addClassName("selected")',onmouseout:'$(this).removeClassName("selected")'}).addClassName('brand');
        		//brandLi.update(this.suggestBrands[key][0]);
        		brandLi.update('<span class="solr_search_suggest_item_title suggest-full-width">'+this.suggestBrands[key_brand][0]+'</span> <span class="solr_search_suggest_item_subtitle suggest-nb-product">('+this.suggestBrands[key_brand][1]+'), </span>');
        		this.container.appendChild(brandLi);
    		}
    	}
    	this.container.appendChild(new Element('div',{style: 'font-family:Arial, Helvetica, sans-serif;font-size:22px;color:#a7c6dd;text-transform:uppercase;clear:both;', 'class':'with-sep'}).update('Cat&eacute;gories'));
    	for(key_cat in this.suggestCategories)
    	{
    		if(!isNaN(key_cat)){
    			var categoryString = this.suggestCategories[key_cat][0];
    			
    			var catPath = categoryString.substring(categoryString.indexOf("/") + 1,categoryString.lastIndexOf("/"));
    			
    	    	var currentCat = categoryString.substring(categoryString.lastIndexOf("/") + 1,categoryString.length);
    	    	
    	    	var catLink = this.options.ajaxBaseUrl+'search/?fq=category:"'+currentCat+'"&q='+this.currentKeyword;    	    	
    	    	
    	    	catPath = catPath.split('/');
    	    	
    	    	catPathLast = catPath[catPath.length-1];
    	    	
    			var categoryItem = new Element('span',{id:'solr_suggest_index_'+key_cat,style:'cursor:pointer;',onclick:'Autocomplete.instances['+this.instanceId+'].select('+key_cat+')',onmouseover:'$(this).addClassName("selected")',onmouseout:'$(this).removeClassName("selected")'}).addClassName('category');
	    		categoryItem.update('<span class="solr_search_suggest_item_title suggest-full-width">'+catPathLast+'</span> <span class="solr_search_suggest_item_subtitle suggest-nb-product">('+this.suggestCategories[key_cat][1]+'), </span>');
	    			    		
	    		this.container.appendChild(categoryItem);
    		}
    	}
    	var bottomDiv = new Element('div').addClassName('solr_search_autocomplete_box_bottom');
    	bottomDiv.update('<span id="solr_search_view_all_link"></span>');
    	this.container.appendChild(bottomDiv);
    	
    	var viewAllLink = this.options.ajaxBaseUrl+'search/?q='+this.currentKeyword;
    	  
    	$('solr_search_view_all_link').update('<a href="'+encodeURI(viewAllLink)+'">Voir tous les r&eacute;sultats</a>');
    	
    }else{
    	this.hideAll();
		return;
    }
    
    this.el.setStyle({style:"border-bottom:1px solid #D0D3D8;background:#D0D3D8"});
    this.closebutton.show();
  },

  processResponse: function(response) {
	  this.suggestions = [];
	  if(typeof response === 'undefined' && this.currentValue.length == 0){
		  this.hideAll();
		  return;
	  }
	  if(response && (response.response.docs.length < 1 || this.currentValue.length == 0 || response.responseHeader.params.timestamp != this.timestamp)){
		  this.hideAll();
		  return;
	  }
	  
	  /**
	   * Loop to push doc name into suggestions array
	   */
	  var i = 0;
	  if(response && response.responseHeader.params.q){	  
		  var keyword = response.responseHeader.params.q;
		  //var keyword = keyword.substring(keyword.indexOf('"') + 1,keyword.lastIndexOf('"'));
	      /*
		  if(keyword !== this.currentValue){
	    	  return false;
	      }
	      */
		  //Collect product list
		  for (var index = 0; index < response.response.docs.length; ++index) {
						
			product_id = response.response.docs[index].products_id;			
			this.suggestions[i] = response.response.docs[index].name_varchar;
			if(typeof response.response.docs[index].special_price_decimal !== 'undefined'){
				this.suggestionsPrice[i] = response.response.docs[index].special_price_decimal;
			}else{
				this.suggestionsPrice[i] = response.response.docs[index].price_decimal;
			}					
			this.suggestionsImages[i] = response.response.docs[index].image_varchar;
			this.suggestionsPaths[i] = response.response.docs[index].url_path_varchar;
			this.suggestionsProductIds[i] = product_id;
			i++;
			if(i >= 20){
				break;
			}
		  }
		  this.Autocompletemessage = 'Produits';
		  if(response.responseHeader.params.q.toLowerCase() != this.currentValue.toLowerCase()){
			  this.Autocompletemessage = 'Autres propositions';
		  }
		  this.currentKeyword = response.responseHeader.params.q;
		  		  
		  //Collect brands
		  this.suggestBrands = [];
		  var brands = this.manager.response.facet_counts.facet_fields.manufacturer_facet;
		  //var brandsFace = new Element('ul', { style: 'text-align:left;padding:5px' });
		  var index = 0;
		  for(key in brands) {
			 
	    	if(brands[key] < 1){
	    		continue;
	    	}
	    	if(key.length > 0){
	    		this.suggestBrands[i] = [key,brands[key]];
		    	i++;
	    	}
	    	
	    	if(index >= 5){
	    		break;
	    	}
	    	index++;
		  }

		  this.suggestCategories = [];
		  var cats = this.manager.response.facet_counts.facet_fields.category_path;		  
		  var index = 0;		  
		  var re = new RegExp('\\b' + this.currentKeyword.match(/\w+/g).join('|\\b'), 'gi');
		  
		  for(key in cats) {
	    	if(cats[key] < 1){
	    		continue;
	    	}  
	    	this.suggestCategories[i] = [key,cats[key]];
	    	i++;
	    	
	    	if(index >= 5){
	    		break;
	    	}
	    	index++;
		  }
		  
	  }
	  
	  this.suggest();
	  if(this.updateSeachFaces === true){	  
		  
	  }
  },
  redirectToUrl: function(url){
	  window.location = url;
  },
  redirectToBrand: function(brand){
	  var brandLink = this.options.ajaxBaseUrl+'search/?fq%5Bmanufacturer%5D='+brand+'&q='+this.currentKeyword;
	  window.location = brandLink;
  },
  redirectToCategory: function(category){
	  var currentCatId = category.substring(category.lastIndexOf("/") + 1,category.length);
	  window.location=this.options.ajaxBaseUrl+'search/ajax/category?cat_id='+currentCatId;
	  return true;
  },
  activate: function(index) {
    var divs = this.container.childNodes;
    var activeItem;
    // Clear previous selection:
    if (this.selectedIndex !== -1 && (divs.length - 1) > this.selectedIndex) {
    	//alert(divs[this.selectedIndex].className);
    	var classnames = divs[this.selectedIndex].className + ' suggested-item';
    	classnames = classnames.split(' ');
    	classnames = classnames.uniq();
    	divs[this.selectedIndex].className = classnames.join(' ');
    	$(divs[this.selectedIndex]).removeClassName('selected');    	
    }
    if(divs[index] === 'undefined'){
    	return;
    }
	
	if(divs[index].id){
		this.selectedItemIndex = index;
	}
	this.selectedIndex = index;
    
    
    if (this.selectedIndex !== -1 && divs.length > this.selectedIndex) {
      activeItem = divs[this.selectedIndex]
      var tempclassnames = activeItem.className + ' selected';
      tempclassnames = tempclassnames.split(' ');
      tempclassnames = tempclassnames.uniq();
      activeItem.className = tempclassnames.join(' ');
      //$(activeItem.id).addClassName('selected');
    }
    return activeItem;
  },

  deactivate: function(div, index) {
    div.removeClassName('selected');
    
    if (this.selectedIndex === index) { this.selectedIndex = -1; }
  },

  select: function(i) {
	var divs = this.container.childNodes;
	var index = parseInt(i)+1;	
	var selectedValue = this.suggestions[i];
	
	var itemId = 'solr_suggest_index_'+i;	
	
	if ($(itemId).hasClassName('product')){
		var productPath = this.suggestionsPaths[i];
		this.redirectToUrl(productPath);
	}else if ($(itemId).hasClassName('category')){
		
		var selectedValue = this.suggestCategories[i][0];
		this.redirectToCategory(selectedValue);
	}else if ($(itemId).hasClassName('brand')){
		
		var selectedValue = this.suggestBrands[i][0];
		this.redirectToBrand(selectedValue);
	}else{
		if($(itemId) != undefined) {
			var productPath = this.suggestionsPaths[i];
			this.redirectToUrl(productPath);
			return;
		}else{
			return;
		}		
	}
	return true;
    
  },

  enterSelect: function(i) {
		var divs = this.container.childNodes;
		var index = parseInt(i)+1;	
		var selectedValue = this.suggestions[i];
		//alert(divs[i].id+':'+i);
		if(divs[i] === 'undefined'){
			return ;
		}
		var elementID = divs[i].id;
		if(!elementID) {
			return;
		}
		i = elementID.substring(elementID.lastIndexOf("_") + 1,elementID.length);
		
		var itemId = elementID;
		
		if ($(itemId).hasClassName('product')){
			var productPath = this.suggestionsPaths[i];
			this.redirectToUrl(productPath);
		}else if ($(itemId).hasClassName('category')){
			//i = parseInt(i) - 2;
			//alert('category:'+i);
			var selectedValue = this.suggestCategories[i][0];
			this.redirectToCategory(selectedValue);
		}else if ($(itemId).hasClassName('brand')){			
			var selectedValue = this.suggestBrands[i][0];
			this.redirectToBrand(selectedValue);
		}else{
			return;
			if($(itemId) != 'undefined') {
				var productPath = this.suggestionsPaths[i];
				this.redirectToUrl(productPath);
				return;
			}else{
				return;
			}		
		}
		return true;	    
  },
  
  moveUp: function() {
	var productSuggestCount = 0;
	for(key_product in this.suggestions)
  	{  		
  		if(!isNaN(key_product)){
  			productSuggestCount = productSuggestCount + 1;
  		}
  	}
	var suggestBrandsCount = 0;
	for(key_brand in this.suggestBrands)
  	{  		
  		if(!isNaN(key_brand)){
  			suggestBrandsCount = suggestBrandsCount + 1;
  		}
  	}
	var suggestCategoriesCount = 0;
	for(key_cat in this.suggestCategories)
  	{  		
  		if(!isNaN(key_cat)){
  			suggestCategoriesCount = suggestCategoriesCount + 1;
  		}
  	}
	var num = productSuggestCount + suggestBrandsCount + suggestCategoriesCount + 2;
	  
	  
    if (this.selectedIndex === 0) { return; }
    if (this.selectedIndex === 0) {
      this.container.childNodes[0].className = '';
      this.selectedIndex = -1;
      //this.el.value = this.currentValue;
      return;
    }
    this.adjustScroll(this.selectedIndex - 1);
    //alert(this.selectedProductId);
  },

  moveDown: function() {
	var productSuggestCount = 0;
	for(key_product in this.suggestions)
  	{  		
  		if(!isNaN(key_product)){
  			productSuggestCount = productSuggestCount + 1;
  		}
  	}
	var suggestBrandsCount = 0;
	for(key_brand in this.suggestBrands)
  	{  		
  		if(!isNaN(key_brand)){
  			suggestBrandsCount = suggestBrandsCount + 1;
  		}
  	}
	var suggestCategoriesCount = 0;
	for(key_cat in this.suggestCategories)
  	{  		
  		if(!isNaN(key_cat)){
  			suggestCategoriesCount = suggestCategoriesCount + 1;
  		}
  	}
	var num = productSuggestCount + suggestBrandsCount + suggestCategoriesCount +2;
    if (this.selectedIndex === num) { return; }
    this.adjustScroll(this.selectedIndex + 1);
    //alert(this.selectedIndex);
  },

  adjustScroll: function(i) {
		
    var container = this.container;
    var activeItem = this.activate(i);
    var offsetTop = activeItem.offsetTop;
    var upperBound = container.scrollTop;
    var lowerBound = upperBound + this.options.maxHeight - 25;
    if (offsetTop < upperBound) {
      container.scrollTop = offsetTop;
    } else if (offsetTop > lowerBound) {
      container.scrollTop = offsetTop - this.options.maxHeight + 25;
    }
    //this.el.value = this.suggestions[i];
  },

  onSelect: function(i) {
    (this.options.onSelect || Prototype.emptyFunction)(this.suggestions[i], this.data[i]);
  }

};

Event.observe(document, 'dom:loaded', function(){ Autocomplete.isDomLoaded = true; }, false);
