<?php
/**
 * Options for the icons plugin
 *
 * @author Giuseppe Di Terlizzi <giuseppe.diterlizzi@gmail.com>
 */


$meta['defaultSize']     = array('string');
$meta['defaultPack']     = array('multichoice','_choices' => array('fa', 'glyphicon', 'fugue', 'oxygen', 'fl'));

$meta['loadFontAwesome'] = array('onoff');
$meta['fontAwesomeURL']  = array('string');

$meta['loadTypicons']    = array('onoff');
$meta['typiconsURL']     = array('string');

$meta['loadFontlinux']   = array('onoff');
$meta['fontlinuxURL']    = array('string');

$meta['fugueURL']        = array('string');
$meta['oxygenURL']       = array('string');
$meta['silkURL']         = array('string');
$meta['flagURL']         = array('string');
