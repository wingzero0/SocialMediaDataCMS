/**
 * Created by kit on 02/11/15.
 */

jQuery(document).ready(function () {
    jQuery('div.pagination span.current').each(function (index) {
        jQuery(this).replaceWith(jQuery('<li class="active"><a href="#">' + this.innerHTML + '</a></li>'));
    });
    var classArray = ['first', 'previous', 'page', 'next', 'last'];
    jQuery.each(classArray, function (key, className) {
        jQuery('div.pagination span.' + className).each(function (index) {
            jQuery(this).replaceWith(jQuery('<li>' + this.innerHTML + '</li>'));
        });
    })
    jQuery('div.pagination').each(function (index) {
        jQuery(this).replaceWith(jQuery('<ul class="pagination">' + this.innerHTML + '</ul>'));
    });

});