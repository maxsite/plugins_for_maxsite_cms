

$(document).ready(function(){

$(window).resize(displayDimensions);
displayDimensions();

function displayDimensions() {

          $('#resize-info').html(
          $(window).width() + 17 +'px'+' x '+$(window).height()+'px'
          );
          
}

});
