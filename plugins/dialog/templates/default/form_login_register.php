<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

 //это будет выведено вместо формы, если никто не залогинен
 
 echo '<div class="login_register"><table>';
 echo '<tr><td class="info">' . $options['only_registered'] . '</td><td class="action">' . $link_login. '</td></tr>';
 echo '</table></div>';
	
?>


