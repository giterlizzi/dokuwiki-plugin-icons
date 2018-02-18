<?php
/**
 * Plugin Icons: Fugue helper
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Giuseppe Di Terlizzi <giuseppe.diterlizzi@gmail.com>
 * @copyright  (C) 2015-2018, Giuseppe Di Terlizzi
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

require_once(dirname(__FILE__).'/icon.php');

class syntax_plugin_icons_fugue extends syntax_plugin_icons_icon {

  const IS_ICON      = true;
  const IS_FONT_ICON = false;

  protected $pattern = '{{fugue>.+?}}';

  public static function makePath($icon, $size, $base_url) {

    $sizes = array(16, 24, 32);
    $size  = (($size > max($sizes)) ? max($sizes) : $size);

    switch ($size) {
      case 0:
      case 16:
         $size = 'icons'; break;
      case 24:
        $size = 'bonus/icons-24'; break;
      case 32:
        $size = 'bonus/icons-32'; break;
      default:
        $size = 'icons';
    }

    return "$base_url/$size/$icon.png";

    }

}
