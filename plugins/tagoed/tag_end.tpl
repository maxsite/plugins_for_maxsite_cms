<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	echo mso_load_jquery('jquery.tablesorter.js');
	echo '
	<script type="text/javascript">
	$(function() {
	  $("table.tablesorter th").animate({opacity: 0.7});
	  $("table.tablesorter th").hover(function(){ $(this).animate({opacity: 1}); }, function(){ $(this).animate({opacity: 0.7}); });
	  $("#pagetable").tablesorter();
	});
	</script>
	';

	echo $CI->table->generate(); //Вывод подготовленной таблицы
	
?>