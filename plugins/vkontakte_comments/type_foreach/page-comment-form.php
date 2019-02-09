<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$options = mso_get_option('vkontakte_comments', 'plugins', array() );
if( !isset($options['width']) || $options['width'] = '' ) $options['width'] = 600;
if( !isset($options['limit']) || $options['limit'] = '' ) $options['limit'] = 10;

if( is_type('page') )
{
?>
<script type="text/javascript">
	VK.init({apiId: <?php echo $options['apiid'] ?>, onlyWidgets: true});
</script>

<div id="vk_comments"></div>
<script type="text/javascript">
	VK.Widgets.Comments("vk_comments", {limit: <?php echo $options['limit'] ?>, width: "<?php echo $options['width'] ?>", attach: "*"});
</script> 
<?
}
?>