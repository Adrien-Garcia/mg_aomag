tinyMceWysiwygSetup.prototype.initialize = function(htmlId, config)
    {
        this.id = htmlId;
        this.config = config;
    this.config.content_css = "/js/jetpulp/tiny_mce/jetpulp-wysiwyg.css";
        varienGlobalEvents.attachEventHandler('tinymceChange', this.onChangeContent.bind(this));
        this.notifyFirebug();
        if(typeof tinyMceEditors == 'undefined') {
            tinyMceEditors = $H({});
        }
        tinyMceEditors.set(this.id, this);
    }
