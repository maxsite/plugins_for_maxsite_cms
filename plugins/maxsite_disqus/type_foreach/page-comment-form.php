<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$options = mso_get_option('maxsite_disqus', 'plugins', array() );
?>

<div class="comment-form">
<div id="disqus_thread"></div>
<script type="text/javascript">
    /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
    var disqus_shortname = '<? echo $options['disqus_shortname']; ?>';

    // The following are highly recommended additional parameters. Remove the slashes in front to use.
    // var disqus_identifier = 'unique_dynamic_id_1234';
    // var disqus_url = 'http://example.com/permalink-to-page.html';

    /* * * DON'T EDIT BELOW THIS LINE * * */
    (function() {
        var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
        dsq.src = 'http://' + disqus_shortname + '.disqus.com/embed.js';
        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
    })();
</script>

</div><!-- div class=comment-form -->
