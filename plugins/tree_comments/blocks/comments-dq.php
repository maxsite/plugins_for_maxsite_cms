<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

		if (!isset($options['tc_dq_id'])) $options['tc_dq_id'] = '';
		if (!isset($options['tc_dq_mobile'])) $options['tc_dq_mobile'] = '0';
	
		echo '<div class="tabs-box'; 
		if (!$options['tc_tabs']) echo 'tabs-visible';
		echo '">';

		echo "<div id='disqus_thread'></div>
			<script type='text/javascript'>
				var disqus_shortname = '".$options['tc_dq_id']."';
				
				(function() {
					var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
					dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
					(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
				})();
			</script>";
		echo '<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
			<a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>';
		echo '</div>';
?>