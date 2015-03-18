<?php
/**
 * Options for the icons plugin
 *
 * @author Giuseppe Di Terlizzi <giuseppe.diterlizzi@gmail.com>
 */


$meta['defaultSize']     = array('string');
$meta['defaultPack']     = array('multichoice','_choices' => array('fa', 'glyphicon', 'fugue', 'oxygen'));
$meta['loadFontAwesome'] = array('onoff');
$meta['fontAwesomeURL']  = array('string');
$meta['fugueURL']        = array('string');
$meta['oxygenURL']       = array('string');
