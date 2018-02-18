<?php
/**
 * Plugin Icons: Plasma Breeze helper
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Giuseppe Di Terlizzi <giuseppe.diterlizzi@gmail.com>
 * @copyright  (C) 2015-2018, Giuseppe Di Terlizzi
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

require_once(dirname(__FILE__).'/icon.php');

class syntax_plugin_icons_breeze extends syntax_plugin_icons_icon {

  const IS_ICON      = true;
  const IS_FONT_ICON = false;

  protected $pattern = '{{breeze>.+?}}';

  public static function makePath($icon, $size, $base_url) {

    $sizes = array(8, 16, 22, 32, 48, 64, 128, 256, 512);
    $size  = (($size > max($sizes)) ? max($sizes) : $size);

    list($category, $icon) = explode('/', $icon);

    return "$base_url/$category/$size/$icon.svg";

  }

}

