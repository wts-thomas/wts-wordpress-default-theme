(function() {
   tinymce.create('tinymce.plugins.MyCustomPlugin', {
       init : function(ed, url) {
           ed.addButton('my_custom_class', {
               title : 'Add custom ID',
               icon: 'dashicon dashicons-admin-generic', // Using a dashicon
               onclick : function() {
                   var selectedText = ed.selection.getContent();
                   var elementId = prompt("Enter the ID", "");

                   if (elementId != null && elementId != '') {
                       // Ensure the ID is applied to a block element (like a div or span with display block)
                       ed.execCommand('mceReplaceContent', false, '<span id="' + elementId + '" style="display:block;">' + selectedText + '</span>');
                   }
               }
           });
       },
       createControl : function(n, cm) {
           return null;
       },
   });
   tinymce.PluginManager.add('my_custom_script', tinymce.plugins.MyCustomPlugin);
})();