<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	//Для вывода будем использовать html-таблицу
	$CI->load->library('table');
	
	$tmpl = array
				(
					'table_open'		  => '<table class="page tablesorter" border="0" width="99%" id="pagetable">',
					'row_alt_start'		  => '<tr class="alt">',
					'cell_alt_start'	  => '<td class="alt">',
					'heading_row_start'    => NR . '<thead><tr>',
					'heading_row_end'       => '</tr></thead>' . NR,
					'heading_cell_start'   => '<th style="cursor: pointer;">',
					'heading_cell_end'      => '</th>',
				);

	$CI->table->set_template($tmpl); //Шаблон таблицы

	//Заголовки
	$CI->table->set_heading(t('Название', __FILE__), t('Теги', __FILE__), ' ');

?>