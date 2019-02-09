// JavaScript Document
	function SpoilerToggle(id, link, showtext, hidetext)
	{
		var spoiler = document.getElementById(id);
    	if (spoiler.style.display != "none")
		{
           	spoiler.style.display = "none";
            link.innerHTML = showtext;
            link.className = "spoiler_link_show";
        }
		else
		{
	       	spoiler.style.display = "block";
            link.innerHTML = hidetext;
            link.className = "spoiler_link_hide";
        }
    }