<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// вывод блока для управления закладками

 if ($comuser = is_login_comuser())
 {
   $get_ajax_path = getinfo('ajax') . base64_encode('plugins/bookmaker/get-ajax.php');
   $edit_ajax_path = getinfo('ajax') . base64_encode('plugins/bookmaker/edit-ajax.php');
   $b_c_id = $comuser['comusers_id'];
   $b_e_id = $page_id;
   $b_e_t_id = 'bp'; // meta_slug
 
  ?>
  <input type="hidden" id="b_get_ajax_path" value="<?= $get_ajax_path ?>">
  <input type="hidden" id="b_edit_ajax_path" value="<?= $edit_ajax_path ?>">
  <input type="hidden" id="b_c_id" value="<?= $b_c_id ?>">
  <input type="hidden" id="b_e_id" value="<?= $b_e_id ?>">
  <input type="hidden" id="b_e_t_id" value="<?= $b_e_t_id ?>">
  <span class="bookmaker" id="bookmaker_block0"></span>
<?php
 }
 
# end file 