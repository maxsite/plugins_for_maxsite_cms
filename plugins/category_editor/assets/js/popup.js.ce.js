function popup_ce_add_center(p){var o,e,c;(Index=$("body .ce-popup-center").size())?(o=$("#ce_popup_"+IdPopupBlock).offset().top-$(window).scrollTop(),e=$("#ce_popup_"+IdPopupBlock).height(),c=o+e+25,IdPopupBlock+=1):c=screen.height/3,$("body").append('<div id="ce_popup_'+IdPopupBlock+'" class="ce-popup-center">'+p+"</div>"),$("#ce_popup_"+IdPopupBlock).css({top:c+"px"}),$("#ce_popup_"+IdPopupBlock).popup_remove_element()}function popup_ce_add_top(p){var o,e,c;(Index=$("body .ce-popup-top").size())?(o=$("#ce_popuptop_"+IdPopupBlockTop).offset().top-$(window).scrollTop(),e=$("#ce_popuptop_"+IdPopupBlockTop).height(),c=o+e+25,IdPopupBlockTop+=1):c=10,$("body").append('<div id="ce_popuptop_'+IdPopupBlockTop+'" class="ce-popup-top">'+p+"</div>"),$("#ce_popuptop_"+IdPopupBlockTop).css({top:c+"px"}),$("#ce_popuptop_"+IdPopupBlockTop).popup_remove_element()}$.fn.popup_remove_element=function(){var p=$(this);setTimeout(function(){p.fadeOut(500,function(){p.remove()})},3e3)};var IdPopupBlock=1,IdPopupBlockTop=1;