<?php
/**
 * Icons Action Plugin
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Giuseppe Di Terlizzi <giuseppe.diterlizzi@gmail.com>
 * @copyright  (C) 2015-2018, Giuseppe Di Terlizzi
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

/**
 * Class Icons Action Plugin
 *
 * Add external CSS file to DokuWiki
 */
class action_plugin_icons extends DokuWiki_Action_Plugin {

  /**
   * Register events
   *
   * @param  Doku_Event_Handler  $controller
   */
  public function register(Doku_Event_Handler $controller) {
    $controller->register_hook('TPL_METAHEADER_OUTPUT', 'BEFORE', $this, '_loadcss');
    $controller->register_hook('TOOLBAR_DEFINE', 'AFTER', $this, '_toolbarButton', array ());
    $controller->register_hook('PLUGIN_POPULARITY_DATA_SETUP', 'AFTER', $this, '_popularity');
  }


  /**
   * Event handler
   *
   * @param  Doku_Event  &$event
   */
  public function _toolbarButton(Doku_Event $event, $param) {

    $event->data[] = array(
      'type'   => 'mediapopup',
      'title'  => 'Icons',
      'icon'   => '../../tpl/dokuwiki/images/logo.png',
      'url'    => 'lib/plugins/icons/exe/popup.php?ns=',
      'name'   => 'icons',
      'options'=> 'width=800,height=600,left=20,top=20,toolbar=no,menubar=no,scrollbars=yes,resizable=yes',
      'block'  => false
    );

  }


  /**
   * Event handler
   *
   * @param  Doku_Event  &$event
   */
  public function _popularity(Doku_Event $event, $param) {
    $plugin_info = $this->getInfo();
    $event->data['icon']['version'] = $plugin_info['date'];
  }


  /**
   * Event handler
   *
   * @param  Doku_Event  &$event
   */
  public function _loadcss(Doku_Event &$event, $param) {

    global $conf;

    $base_url   = DOKU_BASE . 'lib/plugins/icons/assets';
    $font_icons = array();

    # Load Font-Awesome (skipped for Bootstrap3 template)
    if ($this->getConf('loadFontAwesome')) {
      $font_icons[] = "$base_url/font-awesome/css/font-awesome.min.css";
    }

    # Load Typicons
    if ($this->getConf('loadTypicons')) {
      $font_icons[] = "$base_url/typicons/typicons.min.css";
    }

    # Load RPG-Awesome
    if ($this->getConf('loadRpgAwesome')) {
      $font_icons[] = "$base_url/rpg-awesome/css/rpg-awesome.min.css";
    }

    # Load Font Linux
    if ($this->getConf('loadFontlinux')) {
      $font_icons[] = "$base_url/font-linux/font-linux.css";
    }

    # Load Material Icons
    if ($this->getConf('loadMaterialDesignIcons')) {
      $font_icons[] = "$base_url/material-design-icons/css/materialdesignicons.min.css";
    }


    foreach ($font_icons as $font_icon) {
      $event->data['link'][] = array(
        'type'    => 'text/css',
        'rel'     => 'stylesheet',
        'href'    => $font_icon);
    }

  }

}
