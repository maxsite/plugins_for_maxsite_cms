<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$options = mso_get_option('cackle_comments', 'plugins', array() );

$seg = mso_current_url() ;
if (substr($seg ,0 , 4) == 'page')	
{
    ?>
    <div id="mc-container"></div>
<script type="text/javascript">
var mcSite = '<? echo $options['cackle_shortname']; ?>';
(function() {
    var mc = document.createElement('script');
    mc.type = 'text/javascript';
    mc.async = true;
    mc.src = 'http://cackle.me/mc.widget-min.js';
    (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(mc);
})();
</script>
    <?php
}
?>