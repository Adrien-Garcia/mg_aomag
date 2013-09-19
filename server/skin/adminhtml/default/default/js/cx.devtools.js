if(typeof Devtools=='undefined') {
    var Devtools = {};
}

Devtools.Log = Class.create();
Devtools.Log.prototype = {
	initialize: function(formId, postUrl){
		this.postUrl = postUrl;
		//Event.observe($$('form#'+formId+' select')[0], 'change', this.changeFile.bindAsEventListener(this));
		//Event.observe($$('form#'+formId+' input')[0], 'keyup', this.searchLog.bindAsEventListener(this));
		Event.observe($(formId), 'submit', this.query.bindAsEventListener(this));
	},
	/*changeFile: function(event){
		var slFile = event.element().getValue();
		if(slFile != '-'){
			this.query(slFile);
		}
    },
    searchLog: function(event){
		var txtValue = event.element().getValue();
		if(txtValue.length > 2 || txtValue.length == 0){
			this.query(txtValue);
		}
   	},*/
   	refresh: function(){
		this.query();
   	},
   	purge: function(type){
   		if(confirm("ATTENTION: This operation will delete all files in the directory.\nAre you sure?")){
			new Ajax.Request(this.postUrl.replace('tailFile', 'purge'), {
			  method: 'get',
			  parameters: {dir:type},
			  onSuccess: function(transport) {
			    window.location.reload();
			  }
			});
   		}
   	},
   	query: function(ev){

		if(typeof ev != 'undefined'){
			Event.stop(ev);
		}

   		if($('file_inspector_dl').getValue() == '-'){
   			alert('Please select a file.');
   			return false;
   		}

   		var lines = $$('input[name="devtools_show"]')[0].getValue();
   		if(!parseInt(lines)){
   			alert('Please enter a valid number of lines.');
   			return false;
   		}

		new Ajax.Request(this.postUrl, {
		  method: 'get',
		  //loaderArea: false,
		  parameters: {file: $('file_inspector_dl').getValue(), grep:$$('input[name="grep"]')[0].getValue(), devtools_show:lines },
		  onSuccess: function(transport) {
		    $('log-console').update(transport.responseText);
		  }
		});
   	}
}