<?php
/**
 * Plugin Icons for DokuWiki
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Giuseppe Di Terlizzi <giuseppe.diterlizzi>
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
    public function getSort() { return 32; }
 
    /**
     * @param  string  $mode
     */
    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern($this->pattern, $mode, 'plugin_icons_'.$this->getPluginComponent());
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

        $match                = substr($match, 2, -2); // strip markup
        list($match, $flags)  = explode('?', $match, 2);
        list($pack, $icon)    = preg_split('/>/u', $match, 2);
        list($flags, $title)  = explode('|', $flags);

        return array($pack, $icon, explode('&', $flags), $title, $align, $match, $state, $pos);

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

            list($pack, $icon, $flags, $title) = $data;
            $this->parseFlags($pack, $icon, $flags);

            switch ($this->getFlag('pack')) {
                case 'fugue':
                case 'oxygen':
                    $path = $this->makeIconPath($icon);    
                    $renderer->doc .= sprintf('<img src="%s" title="%s" class="%s" style="%s" />',
                                              $path, $title,
                                              trim(implode(' ', $this->getClasses()), ' '),
                                              trim(implode(';', $this->getStyles()), ';'));
                    return true;
            }

            $this->classes[] = $this->getFlag('pack');
            $this->classes[] = $this->getFlag('pack') . '-' . $icon;

            $renderer->doc .= sprintf('<i class="%s" style="%s" title="%s"></i>',
                                      trim(implode(' ', $this->getClasses()), ' '),
                                      trim(implode(';', $this->getStyles()), ';'),
                                      $title);
            return true;

        }

        return false;

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
            $this->flags['pack'] = $value;
            break;

          case 'size':
            $this->flags['size'] = (int) $value;
            $this->styles[] = "font-size:{$value}px";
            break;

          case 'circle':
            $this->flags['circle'] = true;
            $this->styles[] = 'border-radius:50%; -moz-border-radius:50%; -webkit-border-radius:50%';
            break;

          case 'padding':
            $this->flags['padding'] = $value;
            $this->styles[] = "padding:$value";
            break;

          case 'background':
            $this->flags['background'] = $value;
            $this->styles[] = "background-color:$value";
            break;

          case 'color':
            $this->flags['color'] = $value;
            $this->styles[] = "color: $value";
            break;

          case 'class':
            $this->flags['class'] = $value;
            $this->classes[] = $value;
            break;

          case 'align':

            $this->flags['align'] = $value;

            if ($value !== 'center') {
              $margin = ($value == 'left') ? 'right' : 'left';
              $this->styles[] = "float:$value; margin-$margin: .2em";
            } else {
              $this->styles[] = "display:block; text-align:center; margin:0 auto";
            }

            break;

          default:
            $this->classes[] = $flag;
        }
      }

      if (! isset($this->flags['size'])) {
        $this->styles[] = "font-size:" . $this->getConf('defaultSize') . "px";
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

    protected function makeIconPath($icon) {

      switch ($this->getFlag('pack')) {
          case 'fugue'  : return $this->makeFuguePath($icon);
          case 'oxygen' : return $this->makeOxygenPath($icon);
      }

    }

    protected function makeFuguePath($icon) {

        $repo  = rtrim($this->getConf('fugueURL'), '/');
        $sizes = array(16, 24, 32);
        $size  = (($this->getFlag('size') > max($sizes)) ? max($sizes) : $this->getFlag('size'));

        switch ($size) {
            case 0:
            case 16:
               $size = 'icons'; break;
            case 24:
              $size = 'bonus/icons-24'; break;
            case 32:
              $size = 'bonus/icons-32'; break;
            default:
              $size = 'icons';
        }

        return "$repo/$size/$icon.png";

    }

    protected function makeOxygenPath($icon) {

      $repo = rtrim($this->getConf('oxygenURL'), '/');
      $size = $this->getFlag('size').'x'.$this->getFlag('size');
      return "$repo/$size/$icon.png";

    }

}
