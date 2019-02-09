<div id="wrappopup">
	<div id="popupmaster" class="lightbox-color-blue">
	<a class="lightbox-close" id="closepopup"><span>Close</span></a>
	<div class="lightbox-grey-panel">
		<p class="heading">{TITLE}</p>
			<div class="lightbox-contentxx">
				<p>{PARAGRAPH}</p>
				
				<div class="bullet-listx">
                    <ul class="bullet-list">
                        {SPISOK}
                    </ul>
					<div class="lightbox-clear"></div>
				</div>
			</div>
			<div class="lightbox-clear"></div>
		</div>

		
		<div class="lightbox-signup-panel">
			<p class="heading2">{FORMHEADER}</p>
			<p>{TEXT1}</p>
            <form method="post" action="{FORMACTION}"{TARGET}>
                <div>
                    {HIDDEN}
                    <input type = text name = "{FIELDNAME}" value = "{VALUE1}">
                    <input type = text name = "{FIELDMAIL}" value = "{VALUE2}" class = "email">					
                    <input name = "{FIELDSUBMIT}" type="submit" value="{BUTTON}" src="{PATH}images/trans.png" class="blue-button" />
                    <p class="secure">{SECURITY}</p>
                </div>
            </form>
		</div>
		<div class="lightbox-clear"></div>	
	</div>
</div>