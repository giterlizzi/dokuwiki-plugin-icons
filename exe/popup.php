<?php
/**
 * Plugin Icons: Popup helper
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Giuseppe Di Terlizzi <giuseppe.diterlizzi@gmail.com>
 * @copyright  (C) 2015-2019, Giuseppe Di Terlizzi
 */

if (!defined('DOKU_INC')) define('DOKU_INC', dirname(__FILE__).'/../../../../');
define('DOKU_MEDIAMANAGER', 1); // needed to get proper CSS/JS

global $lang;
global $conf;
global $JSINFO;

require_once(DOKU_INC.'inc/init.php');
require_once(DOKU_INC.'inc/template.php');
require_once(DOKU_INC.'inc/lang/en/lang.php');
require_once(DOKU_INC.'inc/lang/'.$conf['lang'].'/lang.php');

$JSINFO['id']        = '';
$JSINFO['namespace'] = '';

$tmp = array();
trigger_event('MEDIAMANAGER_STARTED', $tmp);
session_write_close();  //close session


$icons_plugin = plugin_load('action','icons');

$use_font_awesome          = $icons_plugin->getConf('loadFontAwesome');
$use_material_design_icons = $icons_plugin->getConf('loadMaterialDesignIcons');
$use_typicons              = $icons_plugin->getConf('loadTypicons');
$use_font_linux            = $icons_plugin->getConf('loadFontlinux');
$use_rpg_awesome           = $icons_plugin->getConf('loadRpgAwesome');
$use_glyphicons            = false;

# Load Bootstrap3 Template assets
if ($conf['template'] == 'bootstrap3') {

  include_once(DOKU_INC.'lib/tpl/bootstrap3/tpl_functions.php');
  include_once(DOKU_INC.'lib/tpl/bootstrap3/tpl_global.php');

  # Glyphicons is bundled into Bootstrap 3.x
  $use_glyphicons = true;

}

$list_material_design_icons = include(dirname(__FILE__) . '/list-material-design-icons.php');
$list_font_awesome          = include(dirname(__FILE__) . '/list-font-awesome.php');
$list_glyphicon             = include(dirname(__FILE__) . '/list-glyphicon.php');
$list_typicons              = include(dirname(__FILE__) . '/list-typicons.php');
$list_font_linux            = include(dirname(__FILE__) . '/list-font-linux.php');
$list_rpg_awesome           = include(dirname(__FILE__) . '/list-rpg-awesome.php');

header('Content-Type: text/html; charset=utf-8');
header('X-UA-Compatible: IE=edge,chrome=1');

?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $conf['lang'] ?>" lang="<?php echo $conf['lang'] ?>" dir="<?php echo $lang['direction'] ?>" class="no-js">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title>Icons Plugin</title>
  <script>(function(H){H.className=H.className.replace(/\bno-js\b/,'js')})(document.documentElement)</script>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <?php echo tpl_favicon(array('favicon', 'mobile')) ?>
  <?php tpl_metaheaders() ?>
  <style type="text/css">
    body { padding: 20px; }
    .btn-icon { margin: 4px; padding: 4px; }
    .tab-icons { overflow-y: auto; height: 300px; }
    .icon { font-size: 2em; width: 1.28571429em; text-align: center; }
    <?php if (! $use_glyphicons): ?>
    footer { bottom: 20px; position: fixed; }
    .col-sm-6 { width:50%; float: left; }
    .col-sm-4 { width:33.3%; float: left; }
    .tab-pane, .hide { display: none; }
    button.active { border-style: inset; }
    <?php endif; ?>
  </style>
  <script type="text/javascript">

    jQuery(document).ready(function() {

      var is_bootstrap = (typeof jQuery.fn.modal !== "undefined");

      var $icon_pack    = jQuery('#icon_pack'),
          $icon_name    = jQuery('#icon_name'),
          $icon_size    = jQuery('#icon_size'),
          $icon_align   = jQuery('#icon_align'),
          $output       = jQuery('#output'),
          $preview      = jQuery('#preview');

      if (! is_bootstrap) {
        jQuery('.tab-pane').hide();
      }

      jQuery('button[data-icon-size]').on('click', function() {

        jQuery('button[data-icon-size]').removeClass('active');
        jQuery(this).addClass('active');

        $icon_size.val(jQuery(this).data('icon-size'));
        jQuery(document).trigger('popup:build');

      });

      jQuery('button[data-icon-align]').on('click', function() {

        jQuery('button[data-icon-align]').removeClass('active');
        jQuery(this).addClass('active');

        $icon_align.val(jQuery(this).data('icon-align'));
        jQuery(document).trigger('popup:build');

      });

      jQuery('ul.nav a').on('click', function() {

        if (! is_bootstrap) {
          jQuery('.tab-pane').hide();
          jQuery('ul.nav li.active').removeClass('active');
          jQuery(jQuery(this).attr('href')).show();
          jQuery(this).parent().addClass('active');
        }

        $icon_pack.val(jQuery(this).data('pack'));
        jQuery('.preview-box').removeClass('hide');

        jQuery(document).trigger('popup:reset');

      });

      jQuery('.btn-icon').on('click', function() {
        $icon_name.val(jQuery(this).data('icon-name'));
        jQuery(document).trigger('popup:build');
      });

      jQuery(document).on('popup:build', function() {

        var icon_pack  = $icon_pack.val(),
            icon_size  = $icon_size.val(),
            icon_align = $icon_align.val(),
            icon_name  = $icon_name.val();

        if (! icon_name) {
          return false;
        }

        var syntax = [ '{{' ];

        syntax.push(icon_pack);
        syntax.push('>' + icon_name);

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

      jQuery('#btn-reset').on('click', function() {
        jQuery(document).trigger('popup:reset');
      });

      jQuery(document).on('popup:reset', function() {
        jQuery('form').each(function(){
          jQuery(this)[0].reset();
        });
        $output.val('');
        $preview.text('');
      });

      jQuery('#btn-preview, #btn-insert').on('click', function() {

        if (jQuery(this).attr('id') === 'btn-insert') {
          opener.insertAtCarret('wiki__text', $output.val());
          opener.focus();
        }

      });

    });

  </script>
</head>
<body class="container-fluid dokuwiki">

  <ul class="tabs nav nav-tabs" role="tablist">

    <?php if ($use_font_awesome): ?>
    <li>
      <a data-toggle="tab" href="#tab-font-awesome" data-pack="fa">Font-Awesome</a>
    </li>
    <?php endif; ?>
    <?php if ($use_glyphicons): ?>
    <li>
      <a data-toggle="tab" href="#tab-glyphicon" data-pack="glyphicon">Glyphicons</a>
    </li>
    <?php endif; ?>
    <?php if ($use_material_design_icons): ?>
    <li>
      <a data-toggle="tab" href="#tab-mdi" data-pack="mdi">Material Design Icons</a>
    </li>
    <?php endif; ?>
    <?php if ($use_typicons): ?>
    <li>
      <a data-toggle="tab" href="#tab-typicons" data-pack="typcn">Typicons</a>
    </li>
    <?php endif; ?>
    <?php if ($use_font_linux): ?>
    <li>
      <a data-toggle="tab" href="#tab-font-linux" data-pack="fl">Font-Linux</a>
    </li>
    <?php endif; ?>
    <?php if ($use_rpg_awesome): ?>
    <li>
      <a data-toggle="tab" href="#tab-rpg-awesome" data-pack="fa">RPG-Awesome</a>
    </li>
    <?php endif; ?>

  </ul>

  <main class="tab-content">

    <div id="tab-font-awesome" class="tab-pane fade">

      <div class="row tab-icons">
        <?php foreach($list_font_awesome as $icon): ?>
          <div class="col-sm-4">
            <button class="btn btn-default btn-xs btn-icon" title="<?php echo $icon ?>" data-icon-name="<?php echo $icon ?>">
              <i class="fa fa-fw fa-2x fa-<?php echo $icon ?>"></i>
            </button>
            <small><?php echo $icon ?></small>
          </div>
        <?php endforeach ?>
      </div>

    </div>

    <div id="tab-rpg-awesome" class="tab-pane fade">

      <div class="row tab-icons">
        <?php foreach($list_rpg_awesome as $icon): ?>
          <div class="col-sm-4">
            <button class="btn btn-default btn-xs btn-icon" title="<?php echo $icon ?>" data-icon-name="<?php echo $icon ?>">
              <i class="ra ra-fw ra-2x ra-<?php echo $icon ?>"></i>
            </button>
            <small><?php echo $icon ?></small>
          </div>
        <?php endforeach ?>
      </div>

    </div>

    <div id="tab-mdi" class="tab-pane fade">

      <div class="row tab-icons">
        <?php foreach($list_material_design_icons as $icon): ?>
          <div class="col-sm-4">
            <button class="btn btn-default btn-xs btn-icon" title="<?php echo $icon ?>" data-icon-name="<?php echo $icon ?>">
              <i class="icon mdi mdi-<?php echo $icon ?>"></i>
            </button>
            <small><?php echo $icon ?></small>
          </div>
        <?php endforeach ?>
      </div>

    </div>

    <div id="tab-glyphicon" class="tab-pane fade">

      <div class="row tab-icons">
        <?php foreach($list_glyphicon as $icon): ?>
          <div class="col-sm-4">
            <button class="btn btn-default btn-xs btn-icon" title="<?php echo $icon ?>" data-icon-name="<?php echo $icon ?>">
              <i class="icon glyphicon glyphicon-<?php echo $icon ?>"></i>
            </button>
            <small><?php echo $icon ?></small>
          </div>
        <?php endforeach ?>
      </div>

    </div>

    <div id="tab-typicons" class="tab-pane fade">

      <div class="row tab-icons">
        <?php foreach($list_typicons as $icon): ?>
          <div class="col-sm-4">
            <button class="btn btn-default btn-xs btn-icon" title="<?php echo $icon ?>" data-icon-name="<?php echo $icon ?>">
              <i class="fa-fw fa-2x typcn typcn-<?php echo $icon ?>"></i>
            </button>
            <small><?php echo $icon ?></small>
          </div>
        <?php endforeach ?>
      </div>

    </div>

    <div id="tab-font-linux" class="tab-pane fade">

      <div class="row tab-icons">
        <?php foreach($list_font_linux as $icon): ?>
          <div class="col-sm-4">
            <button class="btn btn-default btn-xs btn-icon" title="<?php echo $icon ?>" data-icon-name="<?php echo $icon ?>">
              <i class="icon fl fl-<?php echo $icon ?>"></i>
            </button>
            <small><?php echo $icon ?></small>
          </div>
        <?php endforeach ?>
      </div>

    </div>

    <div class="preview-box hide">

      <hr/>

      <div class="row">

        <div class="box-alignment col-sm-6">
          <label>Alignment</label>
          <div class="btn-group btn-group-xs">
            <button class="button btn btn-default active" data-icon-align="" title="Use no align">
              <img src="../../../images/media_align_noalign.png" />
            </button><button class="button btn btn-default" data-icon-align="left" title="Align the icon on the left">
              <img src="../../../images/media_align_left.png" />
            </button><button class="button btn btn-default" data-icon-align="center" title="Align the icon in the center">
              <img src="../../../images/media_align_center.png" />
            </button><button class="button btn btn-default" data-icon-align="right" title="Align the icon on the right">
              <img src="../../../images/media_align_right.png" />
            </button>
          </div>
        </div>

        <div class="box-size col-sm-6">
          <label>Image size</label>
          <div class="btn-group btn-group-xs">
            <button class="button btn btn-default" data-icon-size="small" title="Small size">
              <img src="../../../images/media_size_small.png" />
            </button><button class="button btn btn-default" data-icon-size="medium" title="Medium size">
              <img src="../../../images/media_size_medium.png" />
            </button><button class="button btn btn-default" data-icon-size="large" title="Large size">
              <img src="../../../images/media_size_large.png" />
            </button><button class="button btn btn-default active" data-icon-size="original" title="Original size">
              <img src="../../../images/media_size_original.png" />
            </button>
          </div>
        </div>

      </div>

      <p>&nbsp;</p>

      <label>Preview</label>
      <pre id="preview"></pre>

      <input type="hidden" id="output" />
      <input type="hidden" id="icon_pack" />
      <input type="hidden" id="icon_name" />
      <input type="hidden" id="icon_size" />
      <input type="hidden" id="icon_align" />

    </div>

  </main>

  <footer>
    <nav class="navbar navbar-default navbar-fixed-bottom">
      <div class="container-fluid">
        <div class="navbar-text">
          <button type="button" id="btn-preview" class="hidden btn btn-default">Preview code</button>
          <button type="button" id="btn-insert" class="btn btn-success">Insert</button>
          <button type="button" id="btn-reset" class="btn btn-default">Reset</button>
        </div>
      </div>
    </nav>
  </footer>

</body>
</html>
