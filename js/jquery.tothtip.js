 /**
  * Tothtip 
  * -------
  * Tooltip hover plugin for jQuery
  * Created by:
  * - Toth (http://www.toth.cl)
  * - DigitalDev (http://www.digitaldev.org)	
  * © Copyright 2014
  */
(function ($) {    
    $.fn.tothtip = function (options) {       
        if (typeof options === 'object') { 
            var tothtipDiv = $('<div id="tt'+ this.get(0).id +'"></div>');
            tothtipDiv.html(options);
            tothtipDiv.css({
                'min-width': '200px',
                'display': 'block',
                'position': 'absolute',
                'top': '0px',
                'left': '0px',
                'background': '#FFF',
                'color': '#000',
                'border': '1px solid #CCC',
                'border-radius': '15px',
                'padding': '14px',
                '-webkit-box-shadow': '0 5px 10px rgba(0, 0, 0, .2)',
                'box-shadow': '0 5px 10px rgba(0, 0, 0, .2)'
            });   
            return this.on('mouseover', function () {
                $('body').append(tothtipDiv);
                $(this).on('mousemove', function (event) {
                    if (((event.clientX + tothtipDiv.width() + (parseInt(tothtipDiv.css('padding')) * 2)) > $(window).width()) && ((event.clientY + tothtipDiv.height() + (parseInt(tothtipDiv.css('padding')) * 2)) < $(window).height())) {
                        tothtipDiv.css({
                            'left': event.clientX - 20 - tothtipDiv.width() - (parseInt(tothtipDiv.css('padding')) * 2),
                            'top': event.clientY + 20
                        });
                    } else if (((event.clientX + tothtipDiv.width() + (parseInt(tothtipDiv.css('padding')) * 2)) < $(window).width()) && ((event.clientY + tothtipDiv.height() + (parseInt(tothtipDiv.css('padding')) * 2)) > $(window).height())) {
                        tothtipDiv.css({
                            'left': event.clientX + 20,
                            'top': event.clientY - 20 - tothtipDiv.height() - (parseInt(tothtipDiv.css('padding')) * 2)
                        });
                    } else if (((event.clientX + tothtipDiv.width() + (parseInt(tothtipDiv.css('padding')) * 2)) > $(window).width()) && ((event.clientY + tothtipDiv.height() + (parseInt(tothtipDiv.css('padding')) * 2)) > $(window).height())) {
                        tothtipDiv.css({
                            'left': event.clientX - 20 - tothtipDiv.width() - (parseInt(tothtipDiv.css('padding')) * 2),
                            'top': event.clientY - 20 - tothtipDiv.height() - (parseInt(tothtipDiv.css('padding')) * 2)
                        });
                    } else {
                        tothtipDiv.css({
                            'left': event.clientX + 20,
                            'top': event.clientY + 20
                        });
                    }                    
                });                
            }).on('mouseout', function () {
                tothtipDiv.remove();
            });
        } else if (typeof options === 'string') {
            if (options === 'hide') {
                $('#tt'+ this.get(0).id).remove();
                this.unbind();
            }            
        }          
    };
})(jQuery);