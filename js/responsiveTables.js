var t=jQuery.noConflict();t(document).ready((function(){function e(){t(window).width()<961?t(".table-responsive-stack").each((function(e){t(this).find(".table-responsive-stack-thead").show(),t(this).find("thead").hide()})):t(".table-responsive-stack").each((function(e){t(this).find(".table-responsive-stack-thead").hide(),t(this).find("thead").show()}))}t(".table-responsive-stack").each((function(e){var s=t(this).attr("id");t(this).find("th").each((function(e){t("#"+s+" td:nth-child("+(e+1)+")").prepend('<span class="table-responsive-stack-thead">'+t(this).text()+":</span> "),t(".table-responsive-stack-thead").hide()}))})),t(".table-responsive-stack").each((function(){var e=100/t(this).find("th").length+"%";t(this).find("th, td").css("flex-basis",e)})),e(),window.onresize=function(t){e()}}));