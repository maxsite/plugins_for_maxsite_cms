/* jQuery Sticky Plugin
 * =============
 * Author: Ilya Zemskov (ilya@vizr.ru)
 * Date: 22/01/2014
 * Update: 31/03/2015
 * Version: 0.7
 * Website: http://vizr.ru/page/jquery-sticky-plugin
 * Description: A jQuery plugin that keeps select DOM element(s) in view while scrolling the page.
 */
;(function($)
{
	$.fn.sticky = function( params )
	{
		var	options = $.extend(
		{
			topspacing: 20, // margin top
			marginbottom: 150, // margin bottom
			stopper: '.footer', // селектор для блокирующего снизу блока
			styler: '', // class для залипшего блока
			unlockwidth: false, // разблокировать ширину
			animduration: 500, // продолжительность анимации при afterall
			sticktype: 'afterall', // метод залипания «появиться после всех». Ещё доступен «directly» (залипать сразу и не ждать последнего виджета) и «alonemenu» (только для горизонтального меню в шапке)
			screenlimit: true, // прекращать работу на низких разрешениях экрана?
			minwidth: 1024, // минимальная ширина экрана для работы
			minheight: 500, // минимальная высота экрана для работы
       		stick: function (el) {}, // callback-метод для вызова при залипании блока
       		unstick: function (el) {}, // callback-метод для вызова при разлипании блока
		}, params), //console.log(options);
			blocks = this; // все залипающие блоки
			
		return this.each(function( indx )
		{
			// проверка на низкие разрешения
			if( options.screenlimit && (/android|webos|iphone|ipad|ipod|blackberry/i.test(navigator.userAgent.toLowerCase())) || $(window).width() <= options.minwidth || $(window).height() < options.minheight || screen.width <= options.minwidth ) {  return; }
				
			var widget = $(this), // текущий блок
				preW, // ссылка на заглушку
				sidebar = widget.parent(),
				widgetWidth = widget.width(), // запоминаем исходную ширину блока
				widgetOffset = options.topspacing, // для подсчёта отступа для afterall при нескольких виджетах;
				defParams = {position: 'relative', top: 'auto', width: 'auto'}, // параметры блока по-умолчанию при afterall
				stickNow = false; // залип ли блок в данный момент
			
			if( options.sticktype == 'afterall' || options.sticktype == 'alonemenu' )
			{
				blocks.slice(0, indx).each(function(key){ widgetOffset += blocks.eq(key).outerHeight(true); });
			}
				
			// устанавливаем заглушку
			widget.before($('<div class="placeholder"></div>').width(widgetWidth).height(0));
			preW = widget.prev('.placeholder');
				
			// функция запуска callback-методов
			function doIt( method, obj )
			{
				if( method == 'stick' && !stickNow )
				{
					if( options.stick !== undefined ) options.stick(obj);
					stickNow = true;
					if( options.styler != '' ){ obj.addClass(options.styler); }
				}
				if( method == 'unstick' && stickNow )
				{
					if( options.unstick !== undefined ) options.unstick(obj);
					stickNow = false;
					if( options.styler != '' ){ obj.removeClass(options.styler); }
				}
			}
				
			function stickControl()
			{
				var	stickLine = this.pageYOffset + options.topspacing, // Линия прилипания 
					stopTop = ( ( $(options.stopper).length > 0 ) ? Math.round($(options.stopper).offset().top) : $('body').height() ) - options.marginbottom; // нижняя граница остановки залипания
				
				if( options.screenlimit && ( $(window).width() <= options.minwidth || $(window).height() < options.minheight || screen.width <= options.minwidth ) ) {  return; } // проверка на размер экрана
					
				if( options.sticktype == 'directly' ) // directly - прямой способ залипания
				{
					if( 
						( stickLine + widget.outerHeight(true) < stopTop || Math.round(widget.offset().top) + widget.outerHeight(true) < stopTop ) && // если текущая нижняя граница блока выше нижней стоп-линии
						Math.round(widget.offset().top) < stickLine && // если верхняя грань блока выше линии прилипания
						( 
							( blocks.length - 1 >= indx && preW.height() < widget.height() && typeof( widget.next().offset() ) !== "undefined" &&  Math.round(widget.next().offset().top) > stickLine ) || // залипание длится высоту блока для непоследних блоков
							( blocks.length - 1 >= indx && typeof( widget.next().offset() ) === "undefined" ) // если блок последний среди залипающих и следом за ним в столбце любых других блоков нет
						)
					)
					{
						if( stickLine + widget.outerHeight(true) < stopTop )
						{
							preW.height( stickLine - Math.round(preW.offset().top) );
						}
					}
					// если нижний край заглушки стал находиться ниже линии прилипания
					else if( Math.round(preW.offset().top) + preW.outerHeight(true) > stickLine )
					{
						preW.height( stickLine - Math.round(preW.offset().top) );
					}
					
					// дополнительные действия при залипании/разлипании
					if( !stickNow && Math.round(widget.offset().top) >= stickLine && preW.height() > 0 )
					{
						doIt('stick', widget);
					}
					else if( stickNow && ( Math.round(widget.offset().top) < stickLine || ( Math.round(widget.offset().top) > stickLine && preW.height() == 0 ) ) )
					{
						doIt('unstick', widget);
					}
				}
				else // alonemenu || afterall
				{
					if( 
						this.pageYOffset + widgetOffset + widget.outerHeight(true) > stopTop || // если нижняя граница блока перешла нижнюю границу прилипания, то скрываем
						( 
							options.sticktype == 'alonemenu' &&
							blocks.length == 1 &&
							preW.height() > 0 &&
							this.pageYOffset <= preW.offset().top
						)
					)
					{
						if( preW.height() > 0 ) // высота заглушки больше нуля, значит блок в залипшем состоянии и его нужно разлипить
						{
							preW.height(0);
							widget.css(defParams);
							doIt('unstick', widget);
						}
					}
					else // если нижний край не достиг блока-остановки
					{
						if( 
							( 
								options.sticktype == 'alonemenu' &&
								blocks.length == 1 && // проверяем чтобы блок был один
								widget.offset().top <= stickLine // верхняя грань блока выше линии залипания
							) || 
							( 
								options.sticktype == 'afterall' && 
								sidebar.offset().top + sidebar.outerHeight(true) <= stickLine
							) 
						)
						{
							if( preW.height() == 0 )
							{
								preW.height(widget.outerHeight());
								widget.css({position: 'fixed', top: widgetOffset + 'px'});
									
								if( !options.unlockwidth )
								{
									widget.width(widgetWidth);
								}
									
								if( options.animduration > 0 && options.animduration != '' )
								{
									widget.animate({ opacity: 0 }, 0, function(){ widget.animate({ opacity: 1 }, options.animduration) });
								}
									
								doIt('stick', widget);
							}
						}
						else
						{
							if( preW.height() > 0  )
							{
								preW.height(0);
								widget.css(defParams);
								doIt('unstick', widget);
							}
						}
					}
				}
			}
				
			// привязываемся к событиям браузера
			$(window).bind('scroll resize', function() { stickControl() });
				
			setTimeout(function() {$(window).trigger('scroll')}, 0);
		});
	}
})(jQuery);