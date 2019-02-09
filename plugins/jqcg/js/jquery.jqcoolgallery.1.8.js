/*
 * jqCoolGallery
 * 
 * Version: 1.8
 * Requires jQuery 1.5+
 * Docs: http://www.jqcoolgallery.com
 * 
 * (c) copyright 2011-2012, Redtopia, LLC (http://www.redtopia.com). All rights reserved.
 * 
 * Licensed under the GNU General Public License, version 3 (GPL-3.0)
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 */
(function($){
	"use strict";
	
	var jqCoolGallery = function (element, options) {
		
		/*
		PRIVATE METHODS
		*/
		
		// PRIVATE: debug ()
		var debug = function (msg) {
			if (!$opts.debug) {
				return;
			}
			if (window.console) {
				var type = typeof (msg);
				if (type === 'string') {
					if (window.console.log) {
						window.console.log ('jqCoolGallery: ' + msg);
					}
				}
				else if (type === 'object') {
					if (window.console.dir) {
						window.console.dir (msg);
					}
					else if (window.console.log) {
						window.console.log ('jqCoolGallery: ' + msg);
					}
				}
			}
		};
		
		// PRIVATE: scaleRect ()
		var scaleRect = function (rect, maxWidth, maxHeight) {
			var rWidth = rect.width,
				rHeight = rect.height,
				deltaX = 0,
				deltaY = 0,
				percentDelta = 0,
				ix;
				
			for (ix = 1; ix <= 2; ix++) {
				if (maxWidth > 0) {
					deltaX = rWidth - maxWidth;
				}
				else {
					deltaX = 0;
				}
				if (maxHeight > 0) {
					deltaY = rHeight - maxHeight;
				}
				else {
					deltaY = 0;
				}
				if (deltaX <= 0 && deltaY <= 0) {
					break;
				}
				else {
					if (deltaX >= deltaY) {	// more tall than wide
						percentDelta = Math.round ((100 * deltaX) / rWidth);
						deltaY = Math.round ((rHeight * percentDelta) / 100);
					}
					else {
						percentDelta = Math.round ((100 * deltaY) / rHeight);
						deltaX = Math.round ((rWidth * percentDelta) / 100);
					}
					rWidth = rWidth - deltaX;
					rHeight = rHeight - deltaY;
				}
			}
			
			//debug ('scaleRect (), maxWidth: ' + maxWidth + ', maxHeight: ' + maxHeight + ', original: ' + rect.width + ' x ' + rect.height + ', scaled: ' + rWidth + ' x ' + rHeight);
			
			return ({width: rWidth, height: rHeight});
		}; // scaleRect ()
		
		var showTooltip = function (text, e, side) {
			$($ttContent).html (text);
			moveTooltip (e, null, side);
		};
		
		var moveTooltip = function (e, text, side) {
			if (text) {
				$($ttContent).html (text);
			}
			var top = e.pageY - $($tooltip).outerHeight() - 5,
				left = e.pageX - $($tooltip).outerWidth();
			if (side === 'right') {
				left = e.pageX - 10;
			}
			$($tooltip).css ({top: top, left: left});
			$($tooltip).stop(true).fadeTo (200, 0.8, function () {
				$($tooltip).fadeTo (2000, 0);
			});
		};
		
		var hideTooltip = function (e, side) {
			var top = e.pageY - $($tooltip).outerHeight() - 5,
				left = e.pageX - $($tooltip).outerWidth();
			if (side === 'right') {
				left = e.pageX - 10;
			}
			$($tooltip).css ({top: top, left: left});
			$($tooltip).stop(true).fadeTo (10, 0, function () {
				$($tooltip).css({top: -1000, left: -1000});
			});
		};
		
		var doPlay = function () {
			$this.nextImage ();
		};
		
		var play = function () {
			if ($playMode === 1) {
				return;
			}
			debug ('playing');
			$playMode = 1;
			$($playCtl).addClass ('jqcg-ctl-pause');
			if ($ixCurrentGallery < 0) {
				showGallery (0);
			}
			$playInterval = setInterval (doPlay, $opts.playSpeed);
		};
		
		var stop = function () {
			if ($playMode === 0) {
				return;
			}
			debug ('stopping');
			$playMode = 0;
			$($playCtl).removeClass ('jqcg-ctl-pause');
			clearInterval ($playInterval);
			$playInterval = null;
		};
		
		// PRIVATE: loadImage ()
		var loadImage = function (img, fnSuccess, fnFail) {
			if (img.complete) {
				//debug ('loadImage: [' + $(img).attr ('src') + '], loaded: [true], width: ' + $(img).width() + ' height: ' + $(img).height());
				return (fnSuccess (img));
			}
			$(img).error (function () {
				//debug ('image failed to load: [' + $(this).attr ('src') + ']');
				$(this).unbind ('load'); // prevent the load function from being called again
				return (null);
			});
			$(img).load (function () {
				//debug ('loadImage: [' + $(this).attr ('src') + '], loaded: [true], width: ' +$(this).width() + ' height: ' + $(this).height());
				$(img).unbind ('load');
				return (fnSuccess ($(this)));
			});
			$(img).trigger ('load');
			//return (callback (img));
		};
		
		// PRIVATE: addThumb ()
		var addThumb = function (container, src, thWidth, thHeight, ixThumb) {
			
			var rect = scaleRect ({width:thWidth, height:thHeight}, $opts.thumbWidth, $opts.thumbHeight),
				liWidth = $opts.thumbWidth,
				liHeight = $opts.thumbHeight,
				topPadding = 0,
				marginRight = 0,
				marginBottom = 0,
				helperRect,
				thumb,
				li = $('<li></li>').appendTo (container);
			
			debug ('adding thumb: ' + src + ' [' + thWidth + ',' + thHeight + '], scaled to: [' + rect.width + ',' + rect.height + ']');
			
			topPadding = Math.floor ((liHeight - rect.height) / 2);
			
			helperRect = scaleRect ({width:thWidth, height:thHeight}, $opts.thumbHelperMaxWidth, $opts.thumbHelperMaxHeight);
			
			if ($opts.thumbStyle === 'grid') {
				marginBottom = $opts.thumbMargin;
				if ((ixThumb+1) % $ctThumbsPerRow !== 0) {
					marginRight = $opts.thumbMargin;
				}
			}
			else {
				switch ($opts.thumbLocation) {
					case 'top':
					case 'bottom':
						marginRight = $opts.thumbMargin;
						break;
					case 'left':
					case 'right':
						marginBottom = $opts.thumbMargin;
						break;
				}
			}
			
			$(li).css ({
				width: liWidth + 'px',
				height: liHeight + 'px',
				padding: $opts.thumbPadding + 'px',
				borderWidth: $opts.thumbBorderWidth + 'px',
				marginRight: marginRight + 'px',
				marginBottom: marginBottom + 'px'
			});
			
			$(li).addClass ('jqcg-thumb-loading');
			
			thumb = $('<img src="' + src + '" width="' + rect.width + '" height="' + rect.height + '" alt="" />').appendTo (li);
			
			if (topPadding > 0) {
				$(thumb).css ({paddingTop: topPadding + 'px'});
			}
				
			loadImage (thumb, function (imgThumb) {
				//$(img).parent().css({backgroundImage:''});
				$(li).removeClass ('jqcg-thumb-loading');
			}, function (imgThumb) {
				//$(img).parent().css({backgroundImage:'url(\'' + $opts.imagePath + $opts.urlErrorIcon + '\')'});
				$(li).removeClass ('jqcg-thumb-loading');
				$(li).addClass ('jqcg-thumb-error');
			});
			
			if ($opts.thumbStyle === 'list') {
				if ($opts.thumbLocation === 'top' || $opts.thumbLocation === 'bottom') {
					$(container).css ({width: (parseInt ($(container).css ('width'), 10) + $(li).outerWidth (true)) + 'px'});
				}
				else {
					$(container).css ({height: (parseInt ($(container).css ('height'), 10) + $(li).outerHeight (true)) + 'px'});
				}
			}
			
			$(li).hover (function (e) {
				
				// hover over
				
				$(this).addClass ('jqcg-thumb-hover');
				
				if (!$opts.thumbHelper) {
					return;
				}
					
				var width = $(thumb).outerWidth (),
					height = $(thumb).outerHeight (),
					offset = $(thumb).offset (),
					liOffset = $(this).offset (),
					thumbBarOffset = $($thumbBar).offset (),
					thumbRight = ($(window).scrollLeft() + $(window).innerWidth())- (offset.left + width),
					thumbBottom = ($(window).scrollTop() + $(window).innerHeight()) - (offset.top + height),
					helperTop;
				
				$($thumbHelperImg).attr ('src', src);
				$($thumbHelperImg).css ({width: helperRect.width + 'px', height:  helperRect.height + 'px'});
				
				helperTop = (liOffset.top - $($thumbHelperDiv).outerHeight () - 5);
				if ($opts.thumbStyle === 'list' && $opts.thumbLocation === 'top') {
					helperTop = (liOffset.top + liHeight) + 5;
				}
				$($thumbHelperDiv).css ({
					top: helperTop + 'px',
					left: ((liOffset.left + (liWidth / 2)) - ($($thumbHelperDiv).outerWidth () / 2)) + 'px',
					display:'block'
				});
			
			}, function () {
				// hover out
				$(this).removeClass ('jqcg-thumb-hover');
				
				if (!$opts.thumbHelper) {
					return;
				}
				
				$($thumbHelperImg).attr ('src', '');
				
				$($thumbHelperDiv).css ({
					display:'none'
				});
				
			});
			
			$(li).click (function () {
				var ix = $(this).index ();
				stop ();
				showImage (ix);
			});
		}; // addThumb()
		
		// PRIVATE: forceImageReload ()
		var forceImageReload = function (img) {
			var newSrc = $(img).attr ('src') + '?x=' + Math.floor (Math.random()*100000);
			$(img).attr ('src', newSrc);
		};
		
		// PRIVATE: addGalleryItem ()
		var addGalleryItem = function (li, ix, thumbWidth, thumbHeight, gallery) {
			debug ('adding gallery item: ' + ix);

			// setup the gallery list item css
			$(li).css ({display:'block', textAlign:$opts.imageAreaAlign, top:0, left:0, opacity:0, position:'absolute'});
			// set the width and height for the item, if imageAreaWidth/height is specified in the settings
			if (($opts).imageAreaWidth > 0) {
				$(li).width (($opts).imageAreaWidth);
			}
			if (($opts).imageAreaHeight > 0) {
				$(li).height (($opts).imageAreaHeight);
			}
			
			$(li).addClass ('jqcg-item-loading');
			//$(li).css ({backgroundImage:'url(\'' + $opts.imagePath + $opts.urlLoader + '\')', backgroundRepeat:'no-repeat', backgroundPosition:'center center'});
			
			$(li).find ('.caption').hide ();
			
			// find the thumb list for the gallery
			var galleryThumbs = $(gallery).children ('.jqcg-thumbs-list');
			
			// get the image
			var itemImage = $(li).children('img').first();
			
			loadImage (itemImage, function (img) {
				$(li).removeClass ('jqcg-item-loading');
				var urlImage = $(img).attr ('src');
				var imgWidth = $(img).width ();
				var imgHeight = $(img).height ();
				
				var rect = scaleRect ({width:imgWidth, height:imgHeight}, $opts.imageAreaWidth, $opts.imageAreaHeight);
				$(img).width (rect.width);
				$(img).height (rect.height);
				
				debug ('loading image: ' + urlImage + ', width: ' + imgWidth + ', height: ' + imgHeight);
				
				// add the thumbnail
				var thWidth = thumbWidth;
				var thHeight = thumbHeight;
				var urlThumb = $(img).attr ('data-thumb');
				if (typeof (urlThumb) === 'undefined') {
					urlThumb = urlImage;
					thWidth = imgWidth;
					thHeight = imgHeight;
				}
				else {
					var tmp = $(img).attr ('data-thumb-width');
					if (typeof (tmp) !== 'undefined') {
						thWidth = parseInt (tmp, 10);
					}
					tmp = $(img).attr ('data-thumb-height');
					if (typeof (tmp) !== 'undefined') {
						thHeight = parseInt (tmp, 10);
					}
				}
				
				if (thWidth === -1 || thHeight === -1) {
//					urlThumb = urlImage; 	закомментировано lefro
					urlThumb = urlThumb; // добавлено lefro
					thWidth = imgWidth;
					thHeight = imgHeight;
				}
				
				debug ('using thumb: ' + urlThumb + ', width: ' + thWidth + ', height: ' + thHeight);
				
				addThumb (galleryThumbs, urlThumb, thWidth, thHeight, ix);
			});
		
		}; // addGalleryItem ()
		
		// PRIVATE: addGalleryItems ()
		var addGalleryItems = function (gallery, galleryItems) {
			// set the width/height for the list if it is specified in the settings
			if (($opts).imageAreaWidth > 0) {
				$(galleryItems).width (($opts).imageAreaWidth);
			}
			if (($opts).imageAreaHeight > 0) {
				$(galleryItems).height (($opts).imageAreaHeight);
			}
			
			// hide the list
			//$(galleryItems).hide ();
			// set position to relative to support hover captions
			$(galleryItems).css ('position', 'relative');
			
			var thumbWidth = $opts.thumbImageWidth;
			var thumbHeight = $opts.thumbImageHeight;
			var tmp = $(galleryItems).attr ('data-thumb-width');
			if (typeof (tmp) !== 'undefined') {
				thumbWidth = parseInt (tmp, 10);
			}
			tmp = $(galleryItems).attr ('data-thumb-height');
			if (typeof (tmp) !== 'undefined') {
				thumbHeight = parseInt (tmp, 10);
			}
			
			// visit each item in the gallery itself
			$(galleryItems).children('li').each (function (ix) {
				addGalleryItem (this, ix, thumbWidth, thumbHeight, gallery);
			});
		}; // addGalleryItems ()
		
		// PRIVATE: addGallery ()
		var addGallery = function (thisGallery, ix) {
			debug ('adding gallery: ' + ix);
			$ctGalleries++;
			
			$(thisGallery).width ($opts.galleryTileWidth);
			$(thisGallery).height ($opts.galleryTileHeight);
			
			$(thisGallery).attr ('data-gallery-ix', ix);
			$(thisGallery).addClass ('jqcg-gallery-item');
			$(thisGallery).addClass ('jqcg-preloader');
			
			if ($opts.galleryColumnCount > 0) {
				if ($ctGalleries % $opts.galleryColumnCount === 0) {
					$(thisGallery).addClass ('jqcg-gallery-item-last');
				}
			}
			
			// find the thumbnail for the gallery
			var galleryThumb = $(thisGallery).children ('img');
			if (!galleryThumb.length) {
				galleryThumb = $(thisGallery).find ('a img');	// gallery thumb can be surrounded by a link
			}
			
			debug ('gallery thumb - found: ' + galleryThumb.length);
			
			// setup thumbnail container
			var galleryThumbs = $('<ul class="jqcg-thumbs-list"></ul>').appendTo (thisGallery);
			if ($opts.thumbStyle === 'grid') {
				$(galleryThumbs).css ({height: 'auto', width:$thumbListWidth+'px'});
			}
			else {
				$(galleryThumbs).css ({height: $opts.thumbHeight + ($opts.thumbPadding * 2) + ($opts.thumbBorderWidth * 2) + 'px'});
			}
			
			$(galleryThumbs).hide ();
			
			// Get the gallery caption
			var htmlCaption = '';
			var galleryCaption = $(thisGallery).children ('.caption');
			if (galleryCaption.length) {
				htmlCaption = $(galleryCaption).html ();
				$(galleryCaption).remove ();
			}
			else {
				// a .caption element is not found, create a caption from the title/desc
				var galleryTitle = $(thisGallery).attr ('data-title');
				var galleryDesc = $(thisGallery).attr ('data-desc');
				if (galleryTitle && galleryTitle.length) {
					htmlCaption = '<p class="jqcg-gallery-caption-title">' + galleryTitle + '</p>';
				}
				if (galleryDesc && galleryDesc.length) {
					htmlCaption += '<p class="jqcg-gallery-caption-desc">' + galleryDesc + '</p>';
				}
			}
			
			debug ('gallery caption: ' + htmlCaption);
			
			if (htmlCaption.length && $opts.galleryHoverCaption) {
				galleryCaption = $('<div class="jqcg-gallery-caption caption"><div class="jqcg-gallery-caption-content">' + htmlCaption + '</div></div>').appendTo (thisGallery);
			}
			
			loadImage (galleryThumb, function (img) {
				
				debug ('loading gallery thumb: [' + $(img).attr ('src') + ']');
				
				$(thisGallery).removeClass ('jqcg-preloader');
				
				var captionWidth = $opts.galleryTileWidth;
				
				$(img).attr ('jqcg-width', $opts.galleryTileWidth);
				$(img).attr ('jqcg-height', $opts.galleryTileHeight);
				$(img).css ('position', 'absolute');
				
				var captionBottom = 0;
				var captionLeft = 0;
				if ($opts.galleryHoverExpand) {
					captionWidth += ($opts.galleryHoverExpandPx * 2);
					captionBottom = $opts.galleryHoverExpandPx * -1;
					captionLeft = captionBottom;
				}
				$(galleryCaption).css({opacity:0, width:captionWidth+'px', position:'absolute', bottom:captionBottom, left:captionLeft, display:'block'});  
				
			}, function (img) {
				debug ('FAILED gallery thumb: [' + $(img).attr ('src') + ']');
				$(thisGallery).removeClass ('jqcg-preloader');
				$(thisGallery).addClass ('jqcg-failed');
				$(img).width (0);
				$(img).height (0);
				$(img).attr ('src', '');
				var captionWidth = $opts.galleryTileWidth;
			});
			
			// get the gallery's items
			var galleryItems = $(thisGallery).children ('ul.jqcg-viewer-slides');
			
			addGalleryItems (thisGallery, galleryItems);
			
			// add hover handler for each gallery
			$(thisGallery).hover (function (e) {
				//debug ('over: [' + $(this).attr ('data-gallery-ix') + ']');
				$(this).addClass ('jqcg-gallery-hover');
				if ($galleryTooltip && $galleryTooltip.length) {
					showTooltip ($galleryTooltip, e);
				}
				if ($opts.galleryHoverExpand) {
					//debug ('bigger');
					var img = $(this).children ('img').first();
					if (!img.length) {
						img = $(this).find ('a img');
					}
					//debug ('found image: [' + img.length + ']');
					var w = parseInt ($(img).attr ('jqcg-width'), 10) + ($opts.galleryHoverExpandPx * 2);
					var h = parseInt ($(img).attr ('jqcg-height'), 10) + ($opts.galleryHoverExpandPx * 2);
					$(img).css ({zIndex:1000});
					var animateTo = {
						top:'-' + $opts.galleryHoverExpandPx + 'px',
						left:'-' + $opts.galleryHoverExpandPx + 'px',
						width:w + 'px',
						height:h + 'px'
					};
					$(img).addClass ("jqcg-gallery-hover-expand");
					$(img).stop(true);
					$(galleryCaption).stop();
					$(img).animate(animateTo, $opts.animateInSpeed, function () {
						if ($opts.galleryHoverCaption) {
							$(galleryCaption).fadeTo($opts.animateInSpeed, 0.7);
						}
					});
				}
				else if ($opts.galleryHoverCaption) {
					$(galleryCaption).stop().fadeTo($opts.animateInSpeed, 0.7);
				}
				
			}, function (e) {
				//debug ('out: [' + $(this).attr ('data-gallery-ix') + ']');
				$(this).removeClass ('jqcg-gallery-hover');
				hideTooltip (e);
				if ($opts.galleryHoverExpand) {
					//debug ('smaller');
					var img = $(this).children ('img').first();
					if (!img.length) {
						img = $(this).find ('a img');
					}
					var caption = $(this).children ('.jqcg-gallery-caption');
					var w = $(img).attr ('jqcg-width');
					var h = $(img).attr ('jqcg-height');
					$(img).css ({zIndex:1});
					var animateTo = {
						marginTop: 0,
						marginLeft: 0,
						top:0,
						left:0,
						width: w + 'px',
						height: h + 'px'
					};
					$(img).removeClass ("jqcg-gallery-hover-expand");
					// stop all animations
					$(img).stop(true);
					$(galleryCaption).stop(true);
					if ($opts.galleryHoverCaption) {
						// fade out the caption
						$(galleryCaption).fadeTo($opts.animateOutSpeed, 0, function () {
							// when done, return thumb to original size
							$(img).animate(animateTo, $opts.animateOutSpeed);
						});
					}
					else {
						$(img).animate(animateTo, $opts.animateOutSpeed);
					}
				}
				else if ($opts.galleryHoverCaption) {
					$(galleryCaption).stop().fadeTo($opts.animateOutSpeed, 0);
				}
			});
			
			if ($galleryTooltip && $galleryTooltip.length) {
				$(thisGallery).mousemove (function (e) {
					moveTooltip (e, $galleryTooltip);
				});
			}
			
			// add click handler
			$(thisGallery).click (function () {
				$(this).trigger ('mouseout');
				var ixGallery = parseInt ($(this).attr ('data-gallery-ix'), 10);
				var flShow = true;
				if ($opts.fnGalleryClick !== null && !$opts.fnGalleryClick (ixGallery, this)) {
					flShow = false;
				}
				if (flShow) {
					showGallery (ixGallery);
				}
			});
			
			// hide the gallery
			$(galleryItems).hide ();
		}; // addGallery ()
		
		var showLoader = function () {
			//$($loader).fadeTo (100, 1);
			//$($element).css ({backgroundImage:'url(\'' + $opts.imagePath + $opts.urlLoader + '\')'});
			var o = $($element).offset ();
			var w = $($element).width ();
			var h = $($element).height ();
			var loaderTop = o.top + (h/2) - ($($loader).height()/2);
			var loaderLeft = o.left + (w/2) - ($($loader).width()/2);
			$($loader).css ({top: loaderTop, left: loaderLeft});
			$($loader).show ();
		};
		
		var hideLoader = function () {
			//$($element).css ({backgroundImage:''});
			$($loader).hide ();
		};
		
		// PRIVATE: loadGallery ()
		var loadGallery = function (url, gallery, ix, success) {
			
			if ($opts.forceDemandReload) {
				if (url.indexOf ('?') > 0) {
					url += '&';
				}
				else {
					url += '?';
				}
				url += $opts.forceDemandReloadParam + '=' + $randomNum;
			}
			
			$.ajax ({
				url: url,
				type: 'get',
				dataType: 'html',
				success: function (data) {
					$(gallery).removeAttr ('rel');	// remove the rel attribute so the load will not be attempted again
					var galleryItems;
					if ($(data).is ('ul')) {
						galleryItems = data;
					}
					else {
						galleryItems = $(data).find('ul');
					}
					if (!galleryItems.length) {
						alert ('Failed to load gallery: element UL was not found.');
						return;
					}
					galleryItems = $(galleryItems).appendTo (gallery).addClass ('jqcg-viewer-slides');
						
					addGalleryItems (gallery, galleryItems);
					if (success) {
						success ();
					}
				},
				error: function () {
					hideLoader ();
					$(gallery).removeAttr ('rel');	// remove the rel attribute so the load will not be attempted again
					alert ('An error occurred while loading the specified gallery: [' + url + ']');
				}
			});
		
		}; // loadGallery ()
		
		// PRIVATE: resetViewer ()
		var resetViewer = function (flHide) {
			if ($ixCurrentGallery >= 0) {
				if (flHide) {
					$($viewer).hide ();
				}
				// move the current gallery back to its home (and optionally hide it)
				var liHome = $($galleryList).find ("li[data-gallery-ix='" + $ixCurrentGallery + "']");
				var gallery = $viewer.find ('ul.jqcg-viewer-slides');
				var thumbs = $thumbBar.children ('ul.jqcg-thumbs-list');
				if ($ixCurrentSlide > 0 && $ixCurrentSlide < $ctGalleryItems) {
					// nth-child is base 1!
					var curItem = $(gallery).children ('li:nth-child(' + ($ixCurrentSlide + 1) + ')');
					$(curItem).css ('opacity', 0);
				}
				//$(gallery).removeClass ('jqcg-viewer-slides');
				$(thumbs).appendTo (liHome);
				$(thumbs).hide ();
				$(gallery).appendTo (liHome);
				$(gallery).hide ();
				$($viewer).find ('.jqcg-viewer-pagedesc').html ('');
				$ixCurrentGallery = -1;
				$ctGalleryItems = 0;
				$ixCurrentSlide = -1;
			}
		};
		
		// PRIVATE: showImage ()
		var showImage = function (ix) {
			
			debug ('showing image: [' + ix + ']');
			
			if ($ixCurrentGallery < 0) {
				showGallery (0);
				return;
			}
			
			if (ix === $ixCurrentSlide) {
				return;
			}
			
			if ($ctGalleryItems === 0 || ix > $ctGalleryItems) {
				if (!$opts.loopGalleries) {
					return;
				}
				var nextGallery = $ixCurrentGallery + 1;
				if (nextGallery > $ctGalleries) {
					nextGallery = 0;
				}
				if (nextGallery !== $ixCurrentGallery) {
					showGallery (nextGallery);
				}
				return;
			}
			
			var ixLastSlide = $ixCurrentSlide;
			$ixCurrentSlide = ix;
			
			var items = $($viewerImageWrapper).children ('ul').children ('li');
			
			// if the current slide is shown
			if (ixLastSlide >= 0 && ixLastSlide < $ctGalleryItems) {
				// hide the current slide
				$(items [ixLastSlide]).stop().fadeTo(500, 0, function () {
					$(items [$ixCurrentSlide]).stop().fadeTo (500, 1);
				});
			}
			else { // show the current slide
				$(items [$ixCurrentSlide]).stop().fadeTo (500, 1);
			}
			
			// get the caption
			var caption = $(items [ix]).children ('.caption').html ();
			caption = caption ? caption : '';
			$($viewerCaption).html (caption);
			
			// update the page count
			var pageDesc = $($viewer).find ('.jqcg-viewer-pagedesc');
			if (pageDesc.length) {
				var pageDescText = $opts.pageDescFormat.replace ('%pageNum%', ix+1);
				pageDescText = pageDescText.replace ('%pageCount%', items.length);
				$(pageDesc).html (pageDescText);
			}
			
			showThumb ($ixCurrentSlide, true);
			
		}; // showImage ()
		
		// PRIVATE: resetThumbBar ()
		var resetThumbBar = function (thumbs) {
			if ($opts.thumbStyle === 'list') {
				$(thumbs).css ({margin: '0'});
				if ($opts.thumbLocation === 'top' || $opts.thumbLocation === 'bottom') {
					var totalWidth = $(thumbs).css ('width');
					if (totalWidth < $viewerWidth) {
						$($thumbLeftScroll).hide ();
						$($thumbRightScroll).hide ();
						$($thumbBar).css ({width: ($viewerWidth - ($thumbsPaddingLeft + $thumbsPaddingRight)) + 'px'});
					}
					else {
						$($thumbLeftScroll).show ();
						$($thumbRightScroll).show ();
						$($thumbBar).css ({width: ($viewerWidth - ($thumbsPaddingLeft + $thumbsPaddingRight) - ($opts.thumbScrollButtonWidth * 2)) + 'px'});
					}
				}
				else if ($opts.thumbLocation === 'left' || $opts.thumbLocation === 'right') {
					var totalHeight = $(thumbs).css ('height');
				}
			}

			$($thumbBar).find ('ul li.jqcg-thumb-current').removeClass ('jqcg-thumb-current'); // remove any reminant selected thumbs (when gallery was previously open)
			$thumbBarInfo = getThumbBarInfo (thumbs);
			debug ($thumbBarInfo);
		}; // resetThumbBar ()
		
		// PRIVATE: getThumbBarinfo ()
		var getThumbBarInfo = function (thumbList) {
			
			var info = {
				ixFirst: -1,
				ixLast: -1,
				ctTotal: 0,
				pxListLeft: 0,
				pxListLength: 0,
				pxBarRight: 0,
				thumbBarWidth: 0,
				thumbBarHeight: 0,
				thumbList: thumbList,
				thumbItems: null
			};
			
			if (!thumbList || !thumbList.length) {
				return (info);
			}
			
			info.thumbItems = $(thumbList).children();
			info.ctTotal = info.thumbItems.length;
			if (!info.ctTotal) {
				return;
			}
			
			info.thumbListOffset = $(thumbList).offset ();
			info.thumbListPostion = $(thumbList).position ();
			info.thumbBarOffset = $($thumbBar).offset ();
			info.thumbBarPosition = $($thumbBar).position ();
			info.thumbBarWidth = $($thumbBar).width ();
			info.thumbBarHeight = $($thumbBar).height ();
			
			if ($opts.thumbLocation === 'top' || $opts.thumbLocation === 'bottom') {
				info.pxListLeft = parseInt ($(thumbList).css ('margin-left'), 10);
				info.pxListLength = $(thumbList).width ();
				info.pxBarRight = info.thumbBarOffset.left + info.thumbBarWidth;
			}
			
			$(info.thumbItems).each (function (ix, li) {
				var offset = $(li).offset ();
				if ($opts.thumbLocation === 'top' || $opts.thumbLocation === 'bottom') {
					if (info.ixFirst < 0 && offset.left >= info.thumbBarOffset.left) {
						info.ixFirst = ix;
					}
					if (info.ixLast < 0) {
						if ((offset.left + $(li).outerWidth()) > info.pxBarRight) {
							info.ixLast = ix - 1;
						}
						else if (ix === info.ctTotal - 1) {
							info.ixLast = ix;
						}
					}
				}
			});
			
			return (info);
		}; // getThumbBarInfo ()
		
		// PRIVATE: thumbScroll ()
		var thumbScroll = function (direction, ctItems) {
			if (!$thumbBarInfo || !$thumbBarInfo.ctTotal) {
				return;
			}
			
			var ix = 0;
			var newStyle = {};
			var px = 0;
			
			if (direction === 'left') {
				if ($thumbBarInfo.ixLast === $thumbBarInfo.ctTotal - 1) {
					return;
				}
				if ($thumbBarInfo.ixLast + ctItems >= $thumbBarInfo.ctTotal) {
					ctItems = $thumbBarInfo.ctTotal - $thumbBarInfo.ixLast - 1;
				}
				for (ix = $thumbBarInfo.ixFirst; ix < $thumbBarInfo.ixFirst + ctItems; ix++) {
					if ($opts.thumbLocation === 'top' || $opts.thumbLocation === 'bottom') {
						px += $($thumbBarInfo.thumbItems[ix]).outerWidth (true);
					}
				}
				$thumbBarInfo.pxListLeft -= px;
				$thumbBarInfo.ixFirst += ctItems;
			}
			else {
				if ($thumbBarInfo.ixFirst <= 0) {
					return;
				}
				if ($thumbBarInfo.ixFirst - ctItems < 0) {
					ctItems = $thumbBarInfo.ixFirst + 1;
				}
				for (ix = $thumbBarInfo.ixFirst - ctItems; ix < $thumbBarInfo.ixFirst; ix++) {
					if ($opts.thumbLocation === 'top' || $opts.thumbLocation === 'bottom') {
						px += $($thumbBarInfo.thumbItems[ix]).outerWidth (true);
					}
				}
				$thumbBarInfo.pxListLeft += px;
				$thumbBarInfo.ixFirst -= ctItems;
				//$thumbBarInfo.ixLast -= ctItems;
			}
			
			if ($opts.thumbLocation === 'top' || $opts.thumbLocation === 'bottom') {
				newStyle.marginLeft = $thumbBarInfo.pxListLeft + 'px';
			}
			
			$($thumbBarInfo.thumbList).stop().animate (newStyle, 400, function () {
				// calculate the last item (thumb widths can vary)
				var offset;
				if ($opts.thumbLocation === 'top' || $opts.thumbLocation === 'bottom') {
					//var thumbBarRight = $($thumbBar).offset ().left + $($thumbBar).width ();
					var outerWidth;
					for (ix = $thumbBarInfo.ixFirst + 1; ix < $thumbBarInfo.ctTotal; ix++) {
						offset = $($thumbBarInfo.thumbItems [ix]).offset ();
						outerWidth = $($thumbBarInfo.thumbItems [ix]).outerWidth();
						var liRight = offset.left + outerWidth;
						//debug ('ix: ' + ix + ', offset.left: ' + offset.left + ', outerWidth: ' + outerWidth + ', right: ' + liRight + ', thumbBarRight: ' + thumbBarRight);
						if (liRight > $thumbBarInfo.pxBarRight) {
							$thumbBarInfo.ixLast = ix - 1;
							break;
						}
						else if (ix === $thumbBarInfo.ctTotal - 1) {
							$thumbBarInfo.ixLast = ix;
						}
					}
				}
			});
			
			//debug ($thumbBarInfo);
			
		}; // thumbScroll ()
		
		// PRIVATE: showThumb ()
		var showThumb = function (ix, flPage) {
			$($thumbBar).find ('ul li.jqcg-thumb-current').removeClass ('jqcg-thumb-current');
			var curThumb = $($thumbBar).find ('li:nth-child(' + (ix + 1) + ')');
			$(curThumb).addClass ('jqcg-thumb-current');
			// scroll thumb into view
			if (ix >= $thumbBarInfo.ixFirst && ix <= $thumbBarInfo.ixLast) {
				return;
			}
			var ctScroll = 0;
			var direction;
			if (ix < $thumbBarInfo.ixFirst) {
				direction = 'right';
				if (!flPage) {
					ctScroll = $thumbBarInfo.ixFirst - ix;
				}
				else {
					ctScroll = $thumbBarInfo.ixFirst - ix;
				}
			}
			else if (ix > $thumbBarInfo.ixLast) {
				direction = 'left';
				if (!flPage) {
					ctScroll = ix - $thumbBarInfo.ixLast;
				}
				else {
					ctScroll = ix - $thumbBarInfo.ixFirst;
				}
			}
			if (ctScroll > 0) {
				thumbScroll (direction, ctScroll);
			}
		};
		
		// PRIVATE: showGalleryItems ()
		var showGalleryItems = function (gallery, thumbs, galleryTitle, galleryDesc, ix) {
			debug ('showing gallery items: [' + ix + ']');
			
			// set the globals
			$ctGalleryItems = $(gallery).children ('li').length;
			debug ('item count: [' + $ctGalleryItems + ']');
			
			$ixCurrentGallery = ix;
			
			// move the specified gallery items into the viewer
			
			// title and caption of the gallery
			$($viewer).find ('.jqcg-viewer-gallery-title').html (galleryTitle);
			$($viewer).find ('.jqcg-viewer-gallery-desc').html (galleryDesc);
			
			// set the slideshow class to the gallery
			$(gallery).addClass ('jqcg-viewer-slides');
			
			// move the gallery into the viewer
			$(gallery).prependTo ($viewerImageWrapper);
			$(gallery).show ();
			
			// move the thumbs into the viewer
			$(thumbs).appendTo ($thumbBar);
			$(thumbs).show ();
			
			// show the viewer
			$($viewer).show ();
			resetThumbBar (thumbs);
			
			// fade the gallery back in
			$($viewer).fadeTo (500, 1, function () {
				hideLoader ();
				// show the first image
				showImage (0);
			});
		}; // showGalleryItems ()
		
		// PRIVATE: showGallery ()
		var showGallery = function (ix) {
			
			debug ('showing gallery: [' + ix + '], ixCurrentGallery: [' + $ixCurrentGallery + ']');
			
			if (ix < 0 || ix >= $ctGalleries || ix === $ixCurrentGallery) {
				debug ('invalid gallery or gallery already being shown.');
				return;
			}
			
			var flResume = false;
			if ($playMode === 1) {
				stop ();
				flResume = true;
			}
			
			showLoader ();
			
			// get the required DOM elements
			var galleryItem = $($galleryList).find ("li[data-gallery-ix='" + ix + "']");
			var gallery = $(galleryItem).children ("ul.jqcg-viewer-slides");
			var currentHeight = $($element).height ();
			
			if (!gallery.length) {
				debug ('gallery slides not found');
				// there aren't any items in the gallery... see if we need to demand load
				var urlData = $(galleryItem).attr ('rel');
				if (urlData && urlData.length) {
					debug ('getting gallery from rel attr: [' + urlData + ']');
					if ($ixCurrentGallery < 0) {
						$($galleryList).fadeTo ($opts.animateOutSpeed, 0, function () {
							loadGallery (urlData, galleryItem, ix, function () {
								showGallery (ix);
							});
						});
					}
					else {
						$($element).css ({height: currentHeight + 'px'});
						$($viewer).fadeTo (500, 0, function () {
							resetViewer (true);
							loadGallery (urlData, galleryItem, ix, function () {
								showGallery (ix);
								$($element).css ({height: 'auto'});
							});
						});
					}
				}
				if (flResume) {
					play ();
				}
				return;
			}
			
			var galleryTitle = $(galleryItem).attr ('data-title');
			var galleryDesc = $(galleryItem).attr ('data-desc');
			
			var thumbs = $(galleryItem).children ("ul.jqcg-thumbs-list");
			
			if ($ixCurrentGallery >= 0) {
				// fade out the viewer
				$($element).css ({height: currentHeight + 'px'});
				$($viewer).fadeTo (500, 0, function () {
					// reset the viewer - move current gallery list back to its home
					resetViewer (false);
					showGalleryItems (gallery, thumbs, galleryTitle, galleryDesc, ix);
					$($element).css ({height: 'auto'});
				});
			}
			else {
				if ($($galleryList).is(':visible')) {
					// fade out the gallery list
					$($galleryList).fadeTo (500, 0, function () {
						$($galleryList).hide (); // hide the gallery list
						showGalleryItems (gallery, thumbs, galleryTitle, galleryDesc, ix);
					});
				}
				else {
					showGalleryItems (gallery, thumbs, galleryTitle, galleryDesc, ix);
				}
			}
			
			if (flResume) {
				play ();
			}
			
		}; // showGallery()
		
		var initControl = function (viewer, selector, toolTip, fnClick) {
			$(viewer).find (selector).hover (
				function (e) {
					$(this).addClass ('jqcg-ctl-hover');
					if (toolTip && toolTip.length > 0) {
						showTooltip (toolTip, e);
					}
				}, 
				function (e) {
					$(this).removeClass ('jqcg-ctl-hover');
					if (toolTip && toolTip.length > 0) {
						hideTooltip (e);
					}
				}
			).mousemove (function (e) {
				if (toolTip && toolTip.length > 0) {
					moveTooltip (e);
				}
			}).click (function () {
				if (fnClick) {
					fnClick ();
				}
				return (false);
			});
		};
		
		/*
		Variables
		*/
		
		// settings
		var $opts = $.extend ({}, $.fn.jqCoolGallery.defaults, options);
		
		var $element = element;
		var $this = this;
		
		var $playMode = 0;	// 0 for stopped, 1 for playing
		var $playInterval = null;
		
		var $randomNum = Math.floor (Math.random()*100000);
		
		// Make sure the element has an ID. If not, create one and store it
		var $elementID = $($element).attr ('id');
		if ($elementID === '') {
			// if the element doesn't have an id, give it a random id
			$elementID = 'jqcg-' + $randomNum;
			$($element).attr ('id', $elementID);
		}
		
		var $ixCurrentGallery = -1;
		var $ctGalleryItems = 0;
		var $ixCurrentSlide = -1;
		var $galleryList = $($element).find ('ul.jqcg-gallery');
		var $ctGalleries = 0;
		
		debug ('initializing: [' + $elementID + ']');
		
		/*
		DOM Mods
		*/
		
		$($element).addClass ('jqcoolgallery');	// give it the jqcoolgallery root class
		
		//if ($opts.xmlData != '')
		//	getXMLData ();
			
		if ($opts.forceImageReload) {
			// ensure that all images load
			//$($element).find ('img').removeAttr('src').attr('src', $(this).attr('src') + '?x=' + $randomNum);
			$($element).find ('img').each (function () {
				forceImageReload (this);
			});
		}
		
		// setup the viewer for the image display
		var $viewer = $($element).find ('.jqcg-viewer');
		if (!$viewer.length) {
			// create a generic viewer
			var viewerControls = '<div class="jqcg-viewer-controls">' +
				'<div class="jqcg-viewer-controls jqcg-gallery-controls">' +
					'<ul>' +
						'<li class="jqcg-ctl-arrow jqcg-ctl-prev-gallery"><span>&nbsp;</span></li>' +
						'<li class="jqcg-ctl-home"><span>' + $opts.htmlHome + '</span></li>' +
						'<li class="jqcg-ctl-arrow jqcg-ctl-next-gallery"><span>&nbsp;</span></li>' +
					'</ul>' +
				'</div>' +
				'<div class="jqcg-viewer-controls jqcg-show-controls">' +
					'<ul>' +
						'<li class="jqcg-ctl-button jqcg-ctl-rewind"><span>&nbsp;</span></li>' +
						'<li class="jqcg-ctl-button jqcg-ctl-prev-slide"><span>&nbsp;</span></li>' +
						'<li class="jqcg-ctl-button jqcg-ctl-play"><span>&nbsp;</span></li>' +
						'<li class="jqcg-ctl-button jqcg-ctl-next-slide"><span>&nbsp;</span></li>' +
					'</ul>' +
				'</div>' +
				'<div class="jqcg-viewer-gallery-info">' +
					'<' + $opts.galleryTitleElement + ' class="jqcg-viewer-gallery-title"></' + $opts.galleryTitleElement + '>' +
					'<' + $opts.galleryDescElement + ' class="jqcg-viewer-gallery-desc"></' + $opts.galleryDescElement + '>' +
				'</div>' +
			'</div>';
			$viewer = $('<div class="jqcg-viewer"><div class="jqcg-viewer-image-wrapper"></div><div class="jqcg-panel">' + viewerControls + '<div class="jqcg-viewer-text-panel"><div class="jqcg-viewer-caption"></div></div></div></div>').appendTo ($element);
		}
		
		// Previous Gallery
		initControl ($viewer, '.jqcg-ctl-prev-gallery', $opts.prevGalleryToolTip, function (e) {
			stop ();
			$this.prevGallery ();
		});
		// Next Gallery
		initControl ($viewer, '.jqcg-ctl-next-gallery', $opts.nextGalleryToolTip, function (e) {
			stop ();
			$this.nextGallery ();
		});
		// Gallery Home
		initControl ($viewer, '.jqcg-ctl-home', $opts.homeToolTip, function (e) {
			stop ();
			$this.home ();
		});
		// Rewind
		initControl ($viewer, '.jqcg-ctl-rewind', $opts.rewindToolTip, function (e) {
			stop ();
			$this.firstImage ();
		});
		// Previous Image
		initControl ($viewer, '.jqcg-ctl-prev-slide', $opts.prevSlideToolTip, function (e) {
			stop ();
			$this.prevImage ();
		});
		// Next Image
		initControl ($viewer, '.jqcg-ctl-next-slide', $opts.nextSlideToolTip, function (e) {
			stop ();
			$this.nextImage ();
		});
		// Play Slideshow
		var $playCtl = $($viewer).find ('.jqcg-ctl-play').hover (
			function (e) {
				$(this).addClass ('jqcg-ctl-hover');
				if ($playMode === 0 && $opts.playToolTip && $opts.playToolTip.length > 0) {
					showTooltip ($opts.playToolTip, e);
				}
				else if ($playMode === 1 && $opts.pauseToolTip && $opts.pauseToolTip.length > 0) {
					showTooltip ($opts.pauseToolTip, e);
				}
			}, 
			function (e) {
				$(this).removeClass ('jqcg-ctl-hover');
				hideTooltip (e);
			}
		).mousemove (function (e) {
			moveTooltip (e);
		}).click (function () {
			$this.togglePlay ();
			if ($playMode === 1 && $opts.pauseToolTip && $opts.pauseToolTip.length > 0) {
				$ttContent.html ($opts.pauseToolTip);
			}
			else if ($playMode === 1 && $opts.playToolTip && $opts.playToolTip.length > 0) {
				$ttContent.html ($opts.playToolTip);
			}
			return (false);
		});
		
		var $viewerCaption = $($viewer).find ('.jqcg-viewer-caption');
		
		var $panel = $($viewer).find ('.jqcg-panel');
		
		var $viewerImageWrapper = $($viewer).find ('.jqcg-viewer-image-wrapper');
		if (!$viewerImageWrapper.length) {
			$viewerImageWrapper = $('<div class="jqcg-viewer-image-wrapper"></div>').prependTo ($viewer);
		}
		$($viewerImageWrapper).empty ();
		$($viewerImageWrapper).css ({width:$opts.imageAreaWidth + 'px', height:$opts.imageAreaHeight + 'px'});
		if ($opts.imageAreaPadding.length > 0) {
			$($viewerImageWrapper).css ({padding: $opts.imageAreaPadding});
		}
			
		var $stepLeft = $('<div class="jqcg-step jqcg-step-left"></div>').appendTo ($viewerImageWrapper);
		$($stepLeft).css ({opacity:0});
		$($stepLeft).hover (function () {
			$(this).animate({opacity:1},100);
		}, function () {
			$(this).animate({opacity:0},100);
		}).click (function () {
			$this.prevImage ();
		});
		
		var $stepRight = $('<div class="jqcg-step jqcg-step-right"></div>').appendTo ($viewerImageWrapper);
		$($stepRight).css ({opacity:0});
		$($stepRight).hover (function () {
			$(this).animate({opacity:1},100);
		}, function () {
			$(this).animate({opacity:0},100);
		}).click (function () {
			$this.nextImage ();
		});
		
		var $viewerWidth = $($viewer).width ();
		debug ('viewer width: [' + $viewerWidth + ']');
		
		var $thumbs = $($viewer).find ('.jqcg-thumbs');
		if (!$thumbs.length) {
			if ($opts.thumbStyle === 'grid') {
				$thumbs = $('<div class="jqcg-thumbs"></div>').appendTo ($panel);
			}
			else {
				if ($opts.thumbLocation === 'top' || $opts.thumbLocation === 'left') {
					$thumbs = $('<div class="jqcg-thumbs"></div>').prependTo ($viewer);
				}
				else {
					$thumbs = $('<div class="jqcg-thumbs"></div>').appendTo ($viewer);
				}
			}
		}
		
		if ($opts.panelAreaWidth > 0) {
			$($panel).width ($opts.panelAreaWidth + 'px');
		}

		var $thumbLeftScroll = null;
		var $thumbRightScroll = null;
		
		if ($opts.thumbStyle === 'list') {
			$thumbLeftScroll = $('<div class="jqcg-thumbs-scrollleft"></div>').appendTo ($thumbs);
		}
		var $thumbBar = $('<div class="jqcg-thumbs-bar"></div>').appendTo ($thumbs);
		if ($opts.thumbStyle === 'list') {
			$thumbRightScroll = $('<div class="jqcg-thumbs-scrollright"></div>').appendTo ($thumbs);
		}
		
		var $totalThumbHeight = $opts.thumbHeight + ($opts.thumbPadding * 2) + ($opts.thumbBorderWidth * 2);
		var $thumbsPaddingLeft = parseInt ($($thumbs).css ('padding-left'), 10);
		var $thumbsPaddingRight = parseInt ($($thumbs).css ('padding-right'), 10);
		
		var $thumbItemWidth = $opts.thumbWidth + ($opts.thumbPadding * 2) + ($opts.thumbBorderWidth * 2);
		debug ('$thumbItemWidth: ' + $thumbItemWidth);
		var $thumbListWidth = -1;
		var $ctThumbsPerRow = -1;
		
		var $thumbHelperDiv = null;
		var $thumbHelperImg = null;
		if ($opts.thumbHelper) {
			$thumbHelperDiv = $('<div class="jqcg-thumb-helper"></div>').appendTo ('body');
			$thumbHelperImg = $('<img src="" width="0" height="0" alt="" />').appendTo ($thumbHelperDiv);
		}
		
		if ($opts.thumbStyle === 'grid') {
				$($thumbs).addClass ('jqcg-thumbs-gridstyle');
				if ($opts.thumbAreaWidth === -1) {
					$opts.thumbAreaWidth = $($panel).innerWidth ();
				}
				$($thumbs).css ({width: $opts.thumbAreaWidth + 'px', paddingTop: $opts.thumbMargin + 'px'});
				$($thumbBar).css ({width: $opts.thumbAreaWidth + 'px'});
				$ctThumbsPerRow = Math.floor (($opts.thumbAreaWidth+$opts.thumbMargin) / ($thumbItemWidth+$opts.thumbMargin));
				$thumbListWidth = ($thumbItemWidth * $ctThumbsPerRow) + ($opts.thumbMargin * ($ctThumbsPerRow - 1));
				debug ('$ctThumbsPerRow: ' + $ctThumbsPerRow + ', $opts.thumbMargin: ' + $opts.thumbMargin + ', $thumbListWidth: ' + $thumbListWidth);
				if ($thumbHelperDiv !== null) {
					$('<div class="jqcg-helper-arrow jqcg-helper-arrow-down"></div>').appendTo ($thumbHelperDiv);
				}
				debug ('thumbAreaWidth: ' + $opts.thumbAreaWidth + ', thumbItemWidth: ' + $thumbItemWidth + ', ctThumbsPerRow: ' + $ctThumbsPerRow);
		}
		else {
			$($thumbs).addClass ('jqcg-thumbs-liststyle');
			switch ($opts.thumbLocation) {
				case 'top':
					$($thumbs).addClass ('jqcg-thumbs-top');
					// recalc padding after applying top class
					$thumbsPaddingLeft = parseInt ($($thumbs).css ('padding-left'), 10);
					$thumbsPaddingRight = parseInt ($($thumbs).css ('padding-right'), 10);
					$($thumbs).css ({height:$totalThumbHeight+'px', width:($viewerWidth-$thumbsPaddingLeft-$thumbsPaddingRight)+'px'});
					$($thumbLeftScroll).css ({width: $opts.thumbScrollButtonWidth + 'px', height: $totalThumbHeight+'px'});
					$($thumbRightScroll).css ({width: $opts.thumbScrollButtonWidth + 'px', height: $totalThumbHeight+'px'});
					if ($thumbHelperDiv !== null) {
						$('<div class="jqcg-helper-arrow jqcg-helper-arrow-up"></div>').prependTo ($thumbHelperDiv);
					}
					break;
				case 'bottom':
					$($thumbs).addClass ('jqcg-thumbs-bottom');
					// recalc padding after applying bot class
					$thumbsPaddingLeft = parseInt ($($thumbs).css ('padding-left'), 10);
					$thumbsPaddingRight = parseInt ($($thumbs).css ('padding-right'), 10);
					$($thumbs).css ({height:$totalThumbHeight+'px', width:($viewerWidth-$thumbsPaddingLeft-$thumbsPaddingRight)+'px'});
					$($thumbLeftScroll).css ({width: $opts.thumbScrollButtonWidth + 'px', height: $totalThumbHeight+'px'});
					$($thumbRightScroll).css ({width: $opts.thumbScrollButtonWidth + 'px', height: $totalThumbHeight+'px'});
					if ($thumbHelperDiv !== null) {
						$('<div class="jqcg-helper-arrow jqcg-helper-arrow-down"></div>').appendTo ($thumbHelperDiv);
					}
					break;
				case 'left':
					$($thumbs).addClass ('jqcg-thumbs-left');
					if ($thumbHelperDiv !== null) {
						$('<div class="jqcg-helper-arrow jqcg-helper-arrow-left"></div>').appendTo ($thumbHelperDiv);
					}
					break;
				case 'right':
					$($thumbs).addClass ('jqcg-thumbs-right');
					if ($thumbHelperDiv !== null) {
						$('<div class="jqcg-helper-arrow jqcg-helper-arrow-right"></div>').appendTo ($thumbHelperDiv);
					}
					break;
			}
		}
		
		if ($thumbLeftScroll !== null) {
			$($thumbLeftScroll).hover (
				function () {
					if ($thumbBarInfo.ixFirst > 0) {
						$(this).addClass ('jqcg-thumbs-scrollleft-hover');
					}
				},
				function () {
					$(this).removeClass ('jqcg-thumbs-scrollleft-hover');
				}
			);
			$thumbLeftScroll.click (function () {
				thumbScroll ('right', 1);
			});
		}
		
		if ($thumbRightScroll !== null) {
			$($thumbRightScroll).hover (
				function () {
					if ($thumbBarInfo.ixLast < $thumbBarInfo.ctTotal - 1) {
						$(this).addClass ('jqcg-thumbs-scrollright-hover');
					}
				},
				function () {
					$(this).removeClass ('jqcg-thumbs-scrollright-hover');
				}
			);
			$thumbRightScroll.click (function () {
				thumbScroll ('left', 1);
			});
		}
		
		if ($opts.keyboardInput === true) { // Add keyboard navigation support
			$($element).attr ('tabindex', 100);	// allow focus for keyboard input
			
			$(document).keydown (function (e) {
				if (!$($element).is (':focus')) {
					return (true);
				}
				var code = (e.keyCode ? e.keyCode : e.which);
				var row, col, newRow, ixMoveTo;
				var flHandled = false;
				switch (code) {
					case 32: // space
						$this.togglePlay ();
						flHandled = true;
						break;
					case 35: // end
						$this.stop ();
						$this.lastImage ();
						flHandled = true;
						break;
					case 36: // home
						$this.stop ();
						$this.firstImage ();
						flHandled = true;
						break;
					case 37: // left arrow
						$this.stop ();
						$this.prevImage ();
						flHandled = true;
						break;
					case 38: // up arrow
						if ($opts.thumbStyle === 'grid') {
							$this.stop ();
							if ($ixCurrentSlide > $ctThumbsPerRow - 1) {
								row = Math.floor ($ixCurrentSlide / $ctThumbsPerRow) + 1;
								col = $ixCurrentSlide % $ctThumbsPerRow + 1;
								newRow = Math.max (row - 1, 1);
								ixMoveTo = (($ctThumbsPerRow * newRow) - 1) - ($ctThumbsPerRow - col);
								debug ('MOVE UP - from: [row: ' + row + ', col: ' + col + '], newRow: ' + newRow + ', ixMoveTo: ' + ixMoveTo);
								showImage (ixMoveTo);
							}
							flHandled = true;
						}
						break;
					case 39: // right arrow
						$this.stop ();
						$this.nextImage ();
						flHandled = true;
						break;
					case 40: // down arrow
						if ($opts.thumbStyle === 'grid') {
							$this.stop ();
							row = Math.floor ($ixCurrentSlide / $ctThumbsPerRow) + 1;
							col = $ixCurrentSlide % $ctThumbsPerRow + 1;
							newRow = Math.max (row + 1, 1);
							debug ('MOVE DOWN - from: [row: ' + row + ', col: ' + col + '], newRow: ' + newRow);
							if (newRow > row) {
								ixMoveTo = (($ctThumbsPerRow * newRow) - 1) - ($ctThumbsPerRow - col);
								debug ('ixMoveTo: ' + ixMoveTo + ', $ctGalleryItems: ' + $ctGalleryItems);
								if (ixMoveTo >= $ctGalleryItems) {
									ixMoveTo = $ctGalleryItems - 1;
								}
								showImage (ixMoveTo);
							}
							flHandled = true;
						}
						break;
				}
				return (!flHandled);
			});
		}
		
		var $thumbBarInfo = null;
		
        // tooltip
        var $tooltip = $('<div class="jqcg-tooltip" style="opacity:0;"></div>').appendTo ('body');
		var $ttContent = $('<div class="jqcg-tooltip-content"></div>').appendTo ($tooltip);
		
		var $loader = $('<div id="' + $elementID + '-loader" class="jqcg-loading" style="display:none;"></div>').appendTo ('body'); 
		
		$($viewer).hide ();
		$($viewer).css ('opacity', 0);
		
		// move the galleryList off the screen
		//$($galleryList).css ({position:'absolute', left:-10000});
		
		var $galleryTooltip = $($galleryList).attr ('data-gallery-tooltip');
		if (typeof ($galleryTooltip) === 'undefined' || !$galleryTooltip.length) {
			$galleryTooltip = $opts.galleryTooltip;
		}
		
		// visit each item in the gallery list
		$($galleryList).children ('li').each (function (ixElement) {
			addGallery (this, ixElement);
		});
		
		// move the gallery list back to the screen, but set the opacity to 0
		$($galleryList).css ({left:0, opacity:0});
		
		if ($ctGalleries <= 0) {
			$($viewer).empty ();
			$($viewer).html ('<p>No galleries or images found.</p>');
		}
		else {
			if ($ctGalleries === 1) {
				var galleryCtls = $($viewer).find ('div.jqcg-gallery-controls');
				$(galleryCtls).hide();
			}
			if ($opts.openGallery >= 0) {
				showGallery ($opts.openGallery);
			}
			else {
				$($galleryList).fadeTo ($opts.initialFadeSpeed, 1);
			}
		}
		
		/*
		PUBLIC METHODS
		*/
		
		// PUBLIC: home ()
		this.home = function () {
			debug ('going home');
			stop ();
			if ($ixCurrentGallery >= 0) {
				showLoader ();
				// fade out the viewer
				$viewer.fadeTo(500, 0, function () {
					resetViewer (true);
					hideLoader ();
					// show the gallery list
					$($galleryList).show ();
					// fade the gallery list back in
					$($galleryList).stop().fadeTo(500, 1);
				});
			}
		};
		
		this.play = function () {
			play ();
		};

		this.stop = function () {
			stop ();
		};
		
		this.togglePlay = function () {
			if ($playMode === 0) {
				play ();
			}
			else {
				stop ();
			}
		};
		
		this.firstImage = function () {
			debug ('firstImage');
			if ($ixCurrentGallery < 0) {
				showGallery (0);
				return;
			}
			showImage (0);
		};
		
		this.lastImage = function () {
			debug ('lastImage');
			if ($ixCurrentGallery < 0) {
				showGallery (0);
				return;
			}
			showImage ($ctGalleryItems-1);
		};
		
		this.nextImage = function () {
			debug ('nextImage');
			if ($ixCurrentGallery < 0) {
				showGallery (0);
				return;
			}
			var ixNext = $ixCurrentSlide + 1;
			if (ixNext >= $ctGalleryItems) {
				if ($opts.loopGalleries) {
					this.nextGallery ();
					return;
				}
				ixNext = 0;
			}
			showImage (ixNext);
		};
		
		this.prevImage = function () {
			debug ('prevImage');
			if ($ixCurrentGallery < 0) {
				showGallery (0);
				return;
			}
			var ixPrev = $ixCurrentSlide - 1;
			if (ixPrev < 0) {
				if ($opts.loopGalleries) {
					this.prevGallery ();
					return;
				}
				ixPrev = $ctGalleryItems - 1;
			}
			showImage (ixPrev);
		};
		
		this.nextGallery = function () {
			debug ('nextGallery');
			var ixNext = $ixCurrentGallery + 1;
			if (ixNext > $ctGalleries - 1) {
				if (!$opts.loopGalleries) {
					return;
				}
				ixNext = 0;
			}
			showGallery (ixNext);
		};

		this.prevGallery = function () {
			debug ('prevGallery');
			var ixPrev = $ixCurrentGallery - 1;
			if (ixPrev < 0) {
				if (!$opts.loopGalleries) {
					return;
				}
				ixPrev = $ctGalleries - 1;
			}
			showGallery (ixPrev);
		};

	};
	
	// PUBLIC: jqCoolGallery () - main plugin function
	$.fn.jqCoolGallery = function (options, p) {
		return this.each (function () {
			var element = $(this);
			var jq = element.data ('jqCoolGallery');
			
			// handle when jqRadar exists, and options is a function call
			if (jq && typeof (options) === 'string') {
				var fn = jq [options];
				if (typeof (fn) === 'function') {
					return (fn(p));
				}
				return;
			}
			
			// return early if this element already has a plugin instance
			if (jq) {
				return;
			}
			
			// support the metadata plugin
			if ($.metadata) {
				options = $.extend ({}, options, element.metadata());
			}
			
			// store plugin object in this element's data
			element.data ('jqCoolGallery', new jqCoolGallery (this, options));
		});
	};
	
	$.fn.jqCoolGallery.defaults = {
		galleryTileWidth: 200,			// integer: width of gallery tiles
		galleryTileHeight: 200,			// integer: height of gallery tiles
		galleryColumnCount: -1,			// integer: adds the "jqcg-gallery-item-last" class to the last item in each row - used to eliminate right margin
		galleryHoverCaption: true,		// boolean: whether or not to show/hide a gallery captions when hovering
		galleryHoverExpand: true,		// boolean: whether or not to expand gallery items when hovering
		galleryHoverExpandPx: 5,		// integer: number of pixels to expand gallery items (when galleryHoverExpand is true)
		galleryTitleElement: 'h3',		// string: HTML element used to display gallery title in panel
		galleryDescElement: 'p',		// string: HTML element used to display gallery description in panel
		openGallery: -1,				// integer: 0 based index of gallery to open on initial view
		playSpeed: 3000,				// integer: milliseconds delay between slides when playing
		animateInSpeed:200,				// ingeger: milliseconds to animate gallery tiles IN
		animateOutSpeed:200,			// ingeger: milliseconds to animate gallery tiles OUT
		initialFadeSpeed: 2000,			// integer: milliseconds to fade in gallery after loading
		loopGalleries: false,			// boolean: true to auto-load next gallery after playing last slide in current gallery
		imageAreaWidth:-1,				// integer: width of the image area
		imageAreaHeight:-1,				// integer: height of the image area
		imageAreaAlign: 'center',		// string: CSS alignment of image area - 'center' or 'left' or 'right'
		imageAreaPadding: '0',			// string: CSS padding - '0' or '0 10px 0 0' or any other padding CSS to apply to image area
		panelAreaWidth: -1,				// integer: width of panel area, -1: don't apply width
		thumbLocation: 'bottom',		// string: 'left' or 'right' or 'top' or 'bottom'
		thumbStyle: 'list',				// string: 'list' or 'grid'
		thumbAreaWidth: -1,				// integer: only used for grid style, -1: use panelAreaWidth
		thumbHelper: true,				// boolean: true to show a larger thumbnail (thumbnail helper) next to a thumbnail
		thumbHelperMaxWidth: 100,		// integer: max width of thumbnail helper
		thumbHelperMaxHeight: 100,		// integer: max height of thumbnail helper
		thumbWidth: 100,				// integer: width of a thumbnail container
		thumbHeight: 100,				// integer: height of a thumbnail container
		thumbPadding: 0,				// integer: padding around thumbnail container
		thumbMargin: 4,					// integer: margin next to (and below-for thumbs in a grid) thumbnail
		thumbBorderWidth: 1,			// integer: border width for thumbnail container
		thumbImageWidth: -1,			// integer: used to specify width for all thumbnails
		thumbImageHeight: -1,			// integer: used to specify height for all thumbnails
		thumbScrollButtonWidth: 25,
		forceImageReload: true,			// boolean: true to reload all images
		forceDemandReload: true,			// boolean: force reload of ajax content
		forceDemandReloadParam: '_jqcg_',	// string: param used to force reload of ajax content
		htmlHome: 'All Galleries',		// string: HTML to use to bring the user back home from a slideshow
		homeToolTip: 'return to all galleries',	// string: for gallery home text (see also htmlHome option)		
		galleryTooltip: '',			// string: for gallery items, or use data-gallery-tooltip attribute in gallery item li element
		playToolTip: 'start the slideshow',					// string: for the play button
		pauseToolTip: 'pause the slideshow',				// string: for the pause button
		prevGalleryToolTip: 'open the previous gallery',	// string: for the previous gallery button
		nextGalleryToolTip: 'open the next gallery',		// string: for next gallery button
		nextSlideToolTip: 'next slide',						// string: for next slide button
		prevSlideToolTip: 'previous slide',					// string: for previous slide button
		rewindToolTip: 'first slide',						// string: for first slide button

		fnGalleryClick: null,			// function: do your own thing when a gallery item is clicked. Return false to not open the gallery
		keyboardInput: true,			// boolean: listen for keyboard input
		debug: false					// boolean: true to send debug info to the console
	}; // jqlooper.defaults

})(jQuery);
