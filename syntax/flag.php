<?php
/**
 * Plugin Icons: Flag helper
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Giuseppe Di Terlizzi <giuseppe.diterlizzi@gmail.com>
 * @copyright  (C) 2015-2018, Giuseppe Di Terlizzi
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

require_once(dirname(__FILE__).'/icon.php');

class syntax_plugin_icons_flag extends syntax_plugin_icons_icon {

  const IS_ICON      = true;
  const IS_FONT_ICON = false;

  protected $pattern = '{{flag>.+?}}';

  public static function makePath($icon, $size, $base_url) {

    if ($translation = plugin_load('helper', 'translation')) {

      $translation_url  = rtrim(DOKU_BASE, '/')   . '/lib/plugins/translation/flags';
      $translation_path = rtrim(DOKU_PLUGIN, '/') . '/translation/flags';

      if (file_exists("$translation_path/$icon.gif")) {
        return "$translation_url/$icon.gif";
      }

      if (file_exists("$translation_path/more/$icon.gif")) {
        return "$translation_url/more/$icon.gif";
      }

    }

    return "$base_url/$icon-icon.png";

  }

}
