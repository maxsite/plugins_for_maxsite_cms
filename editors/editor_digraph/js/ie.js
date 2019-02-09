
DiGraph.prototype.forIE = function()
	{

	this.e.selection = null;
	this.e.selectionStart = -1;
	this.e.selectionEnd = -1;

	this.e.storeSelection = function(noRecurse)
		{
		// Иногда document.selection устанавливается правильно лишь
		// спустя некоторое время, приходится это учитывать
		if (!noRecurse)
			{
			_self = this;
			setTimeout(function() {_self.storeSelection(1)}, 100); // 500
			}

		if (this.document.selection.type != "None" && this.document.selection.type != "Text")
			{return}

		var range = this.document.selection.createRange();

		if (range.parentElement() != this)
			{return}

		this.selection = range.duplicate();
		}

	this.e.getSelectionStart = function()
		{
		this.updateVars();
		return this.selectionStart;
		}

	this.e.getSelectionEnd = function()
		{
		this.updateVars();
		return this.selectionEnd;
		}
	this.e.updateVars = function()
		{
		if (!this.selection){return}

		this.selectionStart = this.selectionEnd = -1;

		// Поправка на странный глюк при определении позиции в конце однострочного текста
		if (this.value.indexOf("\r") < 0)
			{
			var wholeRange = this.document.body.createTextRange();
			wholeRange.moveToElementText(this);
			if (!this.selection.compareEndPoints("startToEnd", wholeRange))
				{this.selectionStart++}
			if (!this.selection.compareEndPoints("endToEnd", wholeRange))
				{this.selectionEnd++}
			}

		this.selectionStart -= this.selection.moveStart("character", -this.value.length);
		this.selectionEnd -= this.selection.moveEnd("character", -this.value.length);
		this.selection = null;

		// Вводим поправку на то, что знак \r не учитывается
		var pos = -1;
		do
			{
			pos = this.value.indexOf("\r", pos + 1);
			if (pos >= 0 && this.selectionStart > pos)
				{this.selectionStart++}
			if (pos >= 0 && this.selectionEnd > pos)
				{this.selectionEnd++}
			}
		while (pos >= 0 && pos < this.selectionEnd);
		}

	this.e.setSelectionRange = function(startPos, endPos)
		{
		if (startPos > endPos)
			{startPos = endPos}

		// Вводим поправку на то, что знак \r не учитывается
		var startCorrection = this.value.substr(0, startPos).match(/\r/g);
		startCorrection = startCorrection ? startCorrection.length : 0;
		var endCorrection = this.value.substr(0, endPos).match(/\r/g);
		endCorrection = endCorrection ? endCorrection.length : 0;

		this.selection = this.createTextRange();
		this.selection.collapse(true);
		this.selection.moveEnd("character", endPos - endCorrection);
		this.selection.moveStart("character", startPos - startCorrection);

		this.selection.select();

		// Это нужно для случая, когда startPos == endPos
		this.storeSelection();
		}


	_s = this.e;

	$(_s).focus(function(){_s.storeSelection()});
	$(_s).select(function(){_s.storeSelection()});
	$(_s).keyup(function(){_s.storeSelection()});
	}