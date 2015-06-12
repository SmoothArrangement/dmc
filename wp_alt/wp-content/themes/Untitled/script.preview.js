function getParameterByName(name, source) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(source);
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

jQuery(function ($) {
    $(".carousel .left-button, .carousel .right-button").attr("href", "#");

    $("a[href*=\'#038;\']").each(function(){
       var href = $(this).attr("href");
       href = href.replace("#038;","&");
       $(this).attr("href", href);
    });

    $('form').each(function() {
        var action = this.action,
            anchor = '',
            anchorPosition = action.indexOf('#');
        if (!action){
            return;
        }
        if (anchorPosition !== -1) {
            anchor = action.substring(anchorPosition);
            action = action.substring(0, anchorPosition);
        }
        $.each(['preview', 'template', 'stylesheet', 'preview_iframe'], function(idx, attr) {
            var value = getParameterByName(attr, location.href);
            if (value && !getParameterByName(attr, action)) {
                action += (action.indexOf('?') === -1 ? '?' : '&') + attr + '=' + value;
            }
        });
        this.action = action + anchor;
    });
});