<?php
/**
 * Options for Icons Plugin
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Giuseppe Di Terlizzi <giuseppe.diterlizzi@gmail.com>
 * @copyright  (C) 2015-2018, Giuseppe Di Terlizzi
 */

$meta['defaultSize']       = array('string');
$meta['defaultPack']       = array('multichoice','_choices' => array('fa', 'ra', 'typcn', 'glyphicon', 'fl', 'mdi', 'fugue', 'oxygen', 'breeze'));

$meta['loadFontAwesome']         = array('onoff');
$meta['loadTypicons']            = array('onoff');
$meta['loadFontlinux']           = array('onoff');
$meta['loadMaterialDesignIcons'] = array('onoff');
$meta['loadRpgAwesome']          = array('onoff');

$meta['fugueURL']          = array('string');
$meta['oxygenURL']         = array('string');
$meta['breezeURL']         = array('string');
$meta['silkURL']           = array('string');
$meta['flagURL']           = array('string');
