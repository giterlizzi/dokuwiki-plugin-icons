<?php
/**
 * Plugin Icons: Flag helper
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Giuseppe Di Terlizzi <giuseppe.diterlizzi>
 * @copyright  (C) 2015, Giuseppe Di Terlizzi
 */
 
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

require_once(dirname(__FILE__).'/icon.php');

class syntax_plugin_icons_flag extends syntax_plugin_icons_icon {

    protected $pattern = '{{flag>.+?}}';

    const IS_ICON = true;

    public static function makePath($icon, $size, $base_url) {
      return "$base_url/$icon-icon.png";
    }

}
