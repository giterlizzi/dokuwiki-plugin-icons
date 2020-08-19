<?php
/**
 * Icons Action Plugin
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Giuseppe Di Terlizzi <giuseppe.diterlizzi@gmail.com>
 * @copyright  (C) 2015-2018, Giuseppe Di Terlizzi
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) {
    die();
}

/**
 * Class Icons Action Plugin
 *
 * Add external CSS file to DokuWiki
 */
class action_plugin_icons extends DokuWiki_Action_Plugin
{

    /**
     * Register events
     *
     * @param  Doku_Event_Handler  $controller
     */
    public function register(Doku_Event_Handler $controller)
    {
        #$controller->register_hook('TPL_METAHEADER_OUTPUT', 'BEFORE', $this, '_loadcss');
        $controller->register_hook('TOOLBAR_DEFINE', 'AFTER', $this, '_toolbarButton', array());
        $controller->register_hook('PLUGIN_POPULARITY_DATA_SETUP', 'AFTER', $this, '_popularity');
    }

    /**
     * Event handler
     *
     * @param  Doku_Event  &$event
     */
    public function _toolbarButton(Doku_Event $event, $param)
    {

        $event->data[] = array(
            'type'    => 'mediapopup',
            'title'   => 'Icons',
            'icon'    => '../../tpl/dokuwiki/images/logo.png',
            'url'     => 'lib/plugins/icons/exe/popup.php?ns=',
            'name'    => 'icons',
            'options' => 'width=800,height=600,left=20,top=20,toolbar=no,menubar=no,scrollbars=yes,resizable=yes',
            'block'   => false,
        );

    }

    /**
     * Event handler
     *
     * @param  Doku_Event  &$event
     */
    public function _popularity(Doku_Event $event, $param)
    {
        $plugin_info                    = $this->getInfo();
        $event->data['icon']['version'] = $plugin_info['date'];
    }

    /**
     * Event handler
     *
     * @param  Doku_Event  &$event
     */
    public function _loadcss(Doku_Event &$event, $param)
    {

        global $conf;
        $plugin_dir = DOKU_BASE . 'lib/plugins/icons';

        $event->data['script'][] = array(
            'type'  => 'text/javascript',
            'defer' => 'defer',
            'src'   => "$plugin_dir/assets/iconify/iconify.min.js",
            '_data' => null,
        );

        $event->data['script'][] = array(
            'type'  => 'text/javascript',
            '_data' => "Iconify.setConfig('defaultAPI', DOKU_BASE + 'lib/plugin/icons/exe/iconify.php?prefix={prefix}&icons={icons}');"
        );

    }

}
