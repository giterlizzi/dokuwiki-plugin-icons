<?php
/**
 * Plugin Icons for DokuWiki
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Giuseppe Di Terlizzi <giuseppe.diterlizzi@gmail.com>
 * @copyright  (C) 2015, Giuseppe Di Terlizzi
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class syntax_plugin_icons_icon extends DokuWiki_Syntax_Plugin {

    protected $pattern = '{{icon>.+?}}';
    protected $flags   = array();
    protected $classes = array();
    protected $styles  = array();

    /**
     * Syntax Type
     *
     * Needs to return one of the mode types defined in $PARSER_MODES in parser.php
     *
     * @return string
     */
    public function getType() { return 'substition'; }

    /**
     * Sort for applying this mode
     *
     * @return int
     */
    public function getSort() { return 299; }

    /**
     * @param  string  $mode
     */
    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern($this->pattern, $mode, 'plugin_icons_'.$this->getPluginComponent());
        $this->Lexer->addSpecialPattern('\[\[[^\}]+\|'.$this->pattern.'\]\]', $mode, 'plugin_icons_'.$this->getPluginComponent());
    }

    /**
     * Handler to prepare matched data for the rendering process
     *
     * @param   string        $match    The text matched by the patterns
     * @param   int           $state    The lexer state for the match
     * @param   int           $pos      The character position of the matched text
     * @param   Doku_Handler  $handler  The Doku_Handler object
     * @return  bool|array              Return an array with all data you want to use in render, false don't add an instruction
     */ 
    public function handle($match, $state, $pos, Doku_Handler $handler) {

        $match = substr($match, 2, -2); // strip markup
        list($match, $title, $title2) = explode('|', $match);

        if (isset($title2)) $title .= '}}';

        if (isset($title) && preg_match('/'.$this->pattern.'/', $title)) {

          $url   = $match;
          $match = $title;

          $match = substr($match, 2, -2); // strip markup
          list($match, $title) = explode('|', $match);

          if (isset($title2)) $title = rtrim($title2, '}');

        }

        list($match, $flags)  = explode('?', $match, 2);
        list($pack, $icon)    = explode('>', $match, 2);

        return array($pack, $icon, explode('&', $flags), $title, $url, $match, $state, $pos);

    }

    /**
     * Handles the actual output creation.
     *
     * @param   string         $mode      output format being rendered
     * @param   Doku_Renderer  $renderer  the current renderer object
     * @param   array          $data      data created by handler()
     * @return  boolean                   rendered correctly? (however, returned value is not used at the moment)
     */
    public function render($mode, Doku_Renderer $renderer, $data) {

        if ($mode == 'xhtml') {

            /** @var Doku_Renderer_xhtml $renderer */

            list($pack, $icon, $flags, $title, $url) = $data;
            $this->parseFlags($pack, $icon, $flags);

            $class_icon = 'syntax_plugin_icons_'.$this->getFlag('pack');

            if (constant("$class_icon::IS_ICON")) {

              unset($this->styles['font-size']);
              $size      = $this->getFlag('size');
              $base_path = rtrim($this->getConf("{$pack}URL"), '/');
              $path      = call_user_func_array(array($class_icon, 'makePath'), array($icon, $size, $base_path));

              $icon_markup = sprintf('<img src="%s" title="%s" class="%s" style="%s" />',
                                     $path, $title,
                                     $this->toClassString($this->getClasses()),
                                     $this->toInlineStyle($this->getStyles()));

            } else {

              $this->classes[] = $this->getFlag('pack');
              $this->classes[] = $this->getFlag('pack') . '-' . $icon;
  
              $icon_markup = sprintf('<i class="%s" style="%s" title="%s"></i>',
                                     $this->toClassString($this->getClasses()),
                                     $this->toInlineStyle($this->getStyles()),
                                     $title);

            }

            if (isset($url)) {

              global $conf;
              global $ID;

              resolve_pageid(getNS($ID), $url, $exists, $this->date_at, true);

              $link['target'] = $conf['target']['wiki'];
              $link['style']  = '';
              $link['pre']    = '';
              $link['suf']    = '';
              $link['more']   = '';
              $link['class']  = '';
              $link['url']    = wl($url);
              $link['name']   = $icon_markup;

              if ($exists) {
                $link['class'] = 'wikilink1';
              } else {
                $link['class'] = 'wikilink2';
                $link['rel']   = 'nofollow';
              }

              $renderer->doc .= $renderer->_formatLink($link);

            } else {
              $renderer->doc .= $icon_markup;
            }

            return true;

        }

        return false;

    }

    protected function toClassString($things) {
      return trim(implode(' ', $things), ' ');
    }

    protected function toInlineStyle($things) {

      $result = '';

      foreach ($things as $property => $value) {
        $result .= "$property:$value;";
      }

      $result = trim($result, ';');

      return $result;

    }

    protected function getFlag($name) {
      return (isset($this->flags[$name]) ? $this->flags[$name] : null);
    }

    protected function getFlags() {
      return $this->flags;
    }

    protected function parseFlags($pack, $icon, $flags) {

      $this->flags   = array();
      $this->classes = array();
      $this->styles  = array();

      $this->flags['pack'] = $pack;
      $this->flags['icon'] = $icon;

      if ((int) $flags[0] > 0) {
        $flags[] = "size=" . $flags[0];
        unset($flags[0]);
      }

      if ($left = array_search('left', $flags)) {
        $flags[] = 'align=left';
        unset($flags[$left]);
      }

      if ($right = array_search('right', $flags)) {
        $flags[] = 'align=right';
        unset($flags[$right]);
      }

      if ($center = array_search('center', $flags)) {
        $flags[] = 'align=center';
        unset($flags[$center]);
      }

      foreach ($flags as $flag) {

        list($flag, $value) = explode('=', $flag);

        switch ($flag) {

          case 'pack':
            $this->flags[$flag] = $value;
            break;

          case 'size':
            $this->flags[$flag]       = (int) $value;
            $this->styles['font-size'] = "{$value}px";
            break;

          case 'circle':
            $this->flags[$flag]                 = true;
            $this->styles['border-radius']         = '50%';
            $this->styles['-moz-border-radius']    = '50%';
            $this->styles['-webkit-border-radius'] = '50%';
            break;

          case 'border':
            $this->flags[$flag]     = true;
            $this->styles['border'] = '0.08em solid #EEE';
            break;

          case 'borderColor':
            $this->flags[$flag] = $value;
            $this->styles['border-color'] = $value;
            break;

          case 'padding':
            $this->flags[$flag]  = $value;
            $this->styles['padding'] = $value;
            break;

          case 'background':
            $this->flags[$flag]        = $value;
            $this->styles['background-color'] = $value;
            break;

          case 'color':
            $this->flags[$flag]  = $value;
            $this->styles['color'] = $value;
            break;

          case 'class':
            $this->flags[$flag] = $value;
            $this->classes[] = $value;
            break;

          case 'align':

            $this->flags[$flag] = $value;
            $class_icon = 'syntax_plugin_icons_'.$this->getFlag('pack');

            if (constant("$class_icon::IS_ICON")) {
              $this->classes[] = "media$value";
            } else {

              if ($value == 'center') {
                $this->styles['text-align'] = 'center';
              } else {
                $this->styles['padding-'.(($value == 'left') ? 'right' : 'left')] = '.2em';
                $this->styles['float'] = $value;
              }

            }

            break;

          default:
            $this->classes[] = $flag;
        }
      }

      if (! isset($this->flags['size'])) {
        $this->flags['size'] = $this->getConf('defaultSize');
        $this->styles['font-size'] = $this->getConf('defaultSize') . "px";
      }

      if ($this->flags['pack'] == 'icon') {
        $this->flags['pack'] = $this->getConf('defaultPack');
      }

    }

    protected function getStyles() {
      return $this->styles;
    }

    protected function getClasses() {
      return $this->classes;
    }

}
