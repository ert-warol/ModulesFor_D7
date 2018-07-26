(function ($) {
    Drupal.behaviors.select_change = {
        attach: function (context, settings) {
                     $('#page-title').bind('click',{},clickHandler);
                function clickHandler(eventObj)
                {
                        $(eventObj.target).text('Катенька Любимая');
                }
        }
    }
})(jQuery);
