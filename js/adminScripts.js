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
 