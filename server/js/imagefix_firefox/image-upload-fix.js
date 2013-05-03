Event.observe(window, 'load', function() {
  Mediabrowser.prototype.getTargetElement = Mediabrowser.prototype.getTargetElement.wrap(function(originalMethod) {
    if (typeof(tinyMCE) != 'undefined' && tinyMCE.get(this.targetElementId)) {
      var opener = this.getMediaBrowserOpener();
      if (opener) {
        var targetElementId = tinyMceEditors.get(this.targetElementId).getMediaBrowserTargetElementId();
        return opener.document.getElementById(targetElementId);
      } else {
        return null;
      }
    } else {
      return document.getElementById(this.targetElementId);
    }
  });
});