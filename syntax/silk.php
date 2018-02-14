<?php
/**
 * Plugin Icons: Silk helper
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Giuseppe Di Terlizzi <giuseppe.diterlizzi@gmail.com>
 * @copyright  (C) 2015-2018, Giuseppe Di Terlizzi
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

require_once(dirname(__FILE__).'/icon.php');

class syntax_plugin_icons_silk extends syntax_plugin_icons_icon {

    const IS_ICON      = true;
    const IS_FONT_ICON = false;

    protected $pattern = '{{silk>.+?}}';

    public static function makePath($icon, $size, $base_url) {
      return "$base_url/$icon-icon.png";
    }

}
