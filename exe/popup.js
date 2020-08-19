/*!
 * DokuWiki Icons Plugins
 *
 * Home     https://dokuwiki.org/plugin:icons
 * Author   Giuseppe Di Terlizzi <giuseppe.diterlizzi@gmail.com>
 * License  GPL 2 (http://www.gnu.org/licenses/gpl.html)
 */

jQuery(document).ready(function () {

    var is_bootstrap = (typeof jQuery.fn.modal !== "undefined");

    var $icon_pack = jQuery('#icon_pack'),
        $icon_name = jQuery('#icon_name'),
        $icon_size = jQuery('#icon_size'),
        $icon_align = jQuery('#icon_align'),
        $output = jQuery('#output'),
        $preview = jQuery('#preview');

    if (!is_bootstrap) {
        //jQuery('.tab-pane').hide();
    }

    jQuery('button[data-icon-size]').on('click', function () {

        jQuery('button[data-icon-size]').removeClass('active');
        jQuery(this).addClass('active');

        $icon_size.val(jQuery(this).data('icon-size'));
        jQuery(document).trigger('popup:build');

    });

    jQuery('button[data-icon-align]').on('click', function () {

        jQuery('button[data-icon-align]').removeClass('active');
        jQuery(this).addClass('active');

        $icon_align.val(jQuery(this).data('icon-align'));
        jQuery(document).trigger('popup:build');

    });

    jQuery('ul.nav a').on('click', function () {

        if (!is_bootstrap) {
            jQuery('.tab-pane').hide();
            jQuery('ul.nav li.active').removeClass('active');
            jQuery(jQuery(this).attr('href')).show();
            jQuery(this).parent().addClass('active');
        }

        $icon_pack.val(jQuery(this).data('pack'));
        jQuery('.preview-box').removeClass('hide');

        jQuery(document).trigger('popup:reset');

    });

    jQuery('.btn-icon').on('click', function () {
        $icon_name.val(jQuery(this).data('icon-name'));
        jQuery(document).trigger('popup:build');
    });

    jQuery(document).on('popup:build', function () {

        var icon_pack = $icon_pack.val(),
            icon_size = $icon_size.val(),
            icon_align = $icon_align.val(),
            icon_name = $icon_name.val();

        if (!icon_name) {
            return false;
        }

        var syntax = ['{{icon'];

        syntax.push('>' + icon_pack + ':' + icon_name);

        var icon_size_pixel = 0;

        switch (icon_size) {
            case 'small':
                icon_size_pixel = 24;
                break;
            case 'medium':
                icon_size_pixel = 32;
                break;
            case 'large':
                icon_size_pixel = 48;
                break;
        }

        if (icon_size_pixel) {
            syntax.push('?' + icon_size_pixel);
        }

        if (icon_align) {
            syntax.push('&align=' + icon_align);
        }

        syntax.push('}}');

        $output.val(syntax.join(''));
        $preview.text(syntax.join(''));

    });

    jQuery('#btn-reset').on('click', function () {
        jQuery(document).trigger('popup:reset');
    });

    jQuery(document).on('popup:reset', function () {
        jQuery('form').each(function () {
            jQuery(this)[0].reset();
        });
        $output.val('');
        $preview.text('');
    });

    jQuery('#btn-preview, #btn-insert').on('click', function () {

        if (jQuery(this).attr('id') === 'btn-insert') {
            opener.insertAtCarret('wiki__text', $output.val());
            opener.focus();
        }

    });

});
