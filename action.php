<?php
/**
 * Icons Action Plugin
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Giuseppe Di Terlizzi <giuseppe.diterlizzi@gmail.com>
 * @copyright  (C) 2015-2016, Giuseppe Di Terlizzi
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
    $controller->register_hook('TPL_METAHEADER_OUTPUT', 'BEFORE', $this, '_hookcss');
    $controller->register_hook('TOOLBAR_DEFINE', 'AFTER', $this, '_popup_button', array ());

  }

  /**
   * Event handler
   *
   * @param  Doku_Event  &$event
   */
  public function _hookcss(Doku_Event &$event, $param) {

    $font_icons = array();

    if ($this->getConf('loadFontAwesome')) {
      $font_icons[] = $this->getConf('fontAwesomeURL');
    }

    if ($this->getConf('loadTypicon')) {
      $font_icons[] = $this->getConf('typiconURL');
    }

    if ($this->getConf('loadFontlinux')) {
      $font_icons[] = $this->getConf('fontlinuxURL');
    }

    foreach ($font_icons as $font_icon) {
      $event->data['link'][] = array(
        'type'    => 'text/css',
        'rel'     => 'stylesheet',
        'href'    => $font_icon);
    }

  }

}
