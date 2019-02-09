/**
 * Created by 23rd and Walnut for Codebasehero.com
 * www.23andwalnut.com
 * www.codebasehero.com
 * User: Saleem El-Amin
 * Date: 6/11/11
 * Time: 6:41 AM
 *
 * License: You are free to use this file in personal and commercial products, however re-distribution 'as-is' without prior consent is prohibited.
 */

(function($){
    $.fn.ttwSimpleNotifications = function(userOptions){

        var $this = this, defaults, markup, options, wrapper, tmp, cssSelector, notifications = {};

        defaults = {
            position:'bottom right',
            autoHide:true,
            autoHideDelay:6000,
            clickCallback:null,
            showCallback:null,
            hideCallback:null
        };

        cssSelector = {
            tmp:'.tmp',
            notification:'.ttw-simple-notification',
            close:'.close'
        };

        markup = {
            wrapper: '<div class="ttw-simple-notification-wrapper"></div>',
            tmp:'<div class="ttw-simple-notification-tmp"></div>',
            notification:'<div class="ttw-simple-notification">' +
                            '<div class="icon"></div>' +
                            '<div class="message"></div>' +
                            '<span class="close"></span>' +
                          '</div>'
        };

        //apply the user defined options
        options = $.extend({}, defaults, userOptions);

        function init(){
            var position, css = {position:'absolute'};

            //set up the object that will be passed to jQuery css to position the notifications wrapper
            position = options.position.split(' ');
            css[position[0]] = 0;
            css[position[1]] = 0;

            //add the wrapper to the document, position it.
            wrapper = $(markup.wrapper).css(css).appendTo($this);

            //add the tmp wrapper to the document. Notifications are added here first to get height in preparation for animations
            tmp = $(markup.tmp).appendTo($this);

            //bind the event handler to the close button
            $(cssSelector.notification + ' ' + cssSelector.close).live('click', function(){
               // console.log($(this).parent(cssSelector.notification).attr('id'));
               hide($(this).parent(cssSelector.notification).attr('id'));
            });

            $(cssSelector.notification).live('click', function(){
                runCallback(options.clickCallback);
            });
        }

        function show(notificationOptions){
            if(typeof notificationOptions != 'undefined'){
                var $notification = $(markup.notification),  id;

                if(typeof notificationOptions == 'string'){
                    setMessage($notification, notificationOptions);
                }
                else{
                    setMessage($notification, notificationOptions.msg);
                    setIcon($notification, notificationOptions.icon);
                }

                //give the notification a unique id
                id = 'ttwNotification' + new Date().getTime();

                //add the notification to the notifications array. This array is used to track to keep track of notifications.
                notifications[id] = {};
                notifications[id].notification = $notification;

                setAutoHide(id, notificationOptions.autoHide);

                //set the notifications id to the id we just created
                $notification.attr('id', id);

                //display the notification
                $notification.appendTo(wrapper).slideDown(300, function(){
                    $notification.animate({'opacity': 1}, function() {
                        runCallback(options.showCallback);
                    });
                });

                return id;
            }
            else return false;
        }

        function setMessage($notification, msg){
                if(typeof msg != 'undefined')
                    $notification.find('.message').html(msg);
        }

        function setIcon($notification, icon){
             if(typeof icon != 'undefined')
                 $notification.addClass('show-icon').find('.icon').css('background','transparent url(' + icon + ') no-repeat center center scroll');
        }

        function setAutoHide(notificationId, autoHide){
            if(autoHide || (autoHide !== false && options.autoHide !== false))
                notifications[notificationId]['timeout'] = setTimeout(function(){
                    hide(notificationId);
                }, options.autoHideDelay);
        }

        function hide(notificationId){
            if(typeof notifications[notificationId] != undefined){

                //remove the autoHide timeout if it exists
                if(typeof notifications[notificationId]['timeout'] != 'undefined')
                    clearTimeout(notifications[notificationId]['timeout']);

                //remove the notification that corresponds to the id
                notifications[notificationId]['notification'].fadeOut();

                //remove the entry for this notification from the active notifications array
                delete notifications[notificationId];

                runCallback(options.hideCallback);
            }
        }

        function runCallback(callback) {
            var functionArgs = Array.prototype.slice.call(arguments, 1);

            if ($.isFunction(callback)) {
                callback.apply(this, functionArgs);
            }
        }

        init();

        return{
            show:show,
            hide:hide,
            notifications:notifications
        }
    }
})(jQuery);