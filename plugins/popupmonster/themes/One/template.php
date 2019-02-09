<div id = "wrappopup">
    <div id = "popupmaster">
        <div id = "txtpopup">
            <div id = "closepopup" title = "Закрыть окно"></div>
            <div class = "head">{TITLE}</div>
            <div class = "leftpopup">
                <div class = "slogan">{PARAGRAPH}</div>
				{SPISOK}
            </div>
            <div class = "rightpopup">
                <div class = "podpiska">{FORMHEADER}</div>
                <form style = "margin: 0; padding: 0;" action = "{FORMACTION}" method = "post"{TARGET}>
                    {HIDDEN}
                    <div class = "abzac">{TEXT1}</div>
                    <input type = text name = "{FIELDNAME}" value = "{VALUE1}" class = "nameuser">
                    <div class = "abzac">{TEXT2}</div>
                    <input type = text name = "{FIELDMAIL}" value = "{VALUE2}" class = "mailuser">
                    <input name = "{FIELDSUBMIT}" type = submit value = "{BUTTON}" class = "buttonok">
                </form>
                <div class = "lock">
                    {SECURITY}
                </div>
            </div>
        </div>
    </div>
</div>