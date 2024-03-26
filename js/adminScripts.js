document.addEventListener('DOMContentLoaded', function() {
   // Query for all <h2> elements that might contain the desired text.
   document.querySelectorAll('h2').forEach(function(h2) {
     // Check if the text content of the <h2> element matches the target text.
     if (h2.textContent.trim() === 'Upgrade to Yoast SEO Premium') {
       // Find the parent <div> of this <h2>
       var parentDiv = h2.closest('div');
       if (parentDiv) {
         // Add a class to the parent <div> for CSS targeting
         parentDiv.classList.add('yoast-seo-premium-upgrade');
       }
     }
   });
 });
 
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