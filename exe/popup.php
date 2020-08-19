<?php
/**
 * Plugin Icons: Popup helper
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Giuseppe Di Terlizzi <giuseppe.diterlizzi@gmail.com>
 * @copyright  (C) 2015-2019, Giuseppe Di Terlizzi
 */

# NOTE Some Linux distributon change the location of DokuWiki core libraries (DOKU_INC)
#
#      Bitnami (Docker)         /opt/bitnami/dokuwiki
#      LinuxServer.io (Docker)  /app/dokuwiki
#      Arch Linux               /usr/share/webapps/dokuwiki
#      Debian/Ubuntu            /usr/share/dokuwiki
#
# NOTE If DokuWiki core libraries (DOKU_INC) is in another location you can
#      create a PHP file in bootstrap3 root directory called "doku_inc.php" with
#      this content:
#
#           <?php define('DOKU_INC', '/path/dokuwiki/');
#
#      (!) This file will be deleted on every upgrade of template

$doku_inc_dirs = array(
  '/opt/bitnami/dokuwiki',                       # Bitnami (Docker)
  '/usr/share/webapps/dokuwiki',                 # Arch Linux
  '/usr/share/dokuwiki',                         # Debian/Ubuntu
  '/app/dokuwiki',                               # LinuxServer.io (Docker),
  realpath(dirname(__FILE__) . '/../../../../'), # Default DokuWiki path
);

# Load doku_inc.php file
#
if (file_exists(dirname(__FILE__) . '/../doku_inc.php')) {
    require_once dirname(__FILE__) . '/../doku_inc.php';
}

if (!defined('DOKU_INC')) {
    foreach ($doku_inc_dirs as $dir) {
        if (!defined('DOKU_INC') && @file_exists("$dir/inc/init.php")) {
            define('DOKU_INC', "$dir/");
        }
    }
}

define('DOKU_MEDIAMANAGER', 1); // needed to get proper CSS/JS

global $lang;
global $conf;
global $JSINFO;
global $INPUT;

require_once DOKU_INC . 'inc/init.php';
require_once DOKU_INC . 'inc/template.php';
require_once DOKU_INC . 'inc/lang/en/lang.php';
require_once DOKU_INC . 'inc/lang/' . $conf['lang'] . '/lang.php';

$JSINFO['id']        = '';
$JSINFO['namespace'] = '';

$tmp = array();
trigger_event('MEDIAMANAGER_STARTED', $tmp);
session_write_close(); //close session

$icons_plugin = plugin_load('action', 'icons');

$popup_url = DOKU_BASE . 'lib/plugins/icons/exe/popup.php';
$pack      = $INPUT->str('pack');

$collections_dir    = dirname(__FILE__) . '/../assets/iconify/json';
$collections        = json_decode(file_get_contents(dirname(__FILE__) . '/../assets/iconify/collections.json'), true);
$custom_collections = json_decode(file_get_contents(dirname(__FILE__) . '/../assets/iconify/custom-collections.json'), true);

$collections = array_merge($collections, $custom_collections);
$categories  = array();

ksort($collections);

foreach ($collections as $collection => $data) {
    $categories[$data['category']][] = $collection;
}

ksort($categories);

header('Content-Type: text/html; charset=utf-8');
header('X-UA-Compatible: IE=edge,chrome=1');

?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $conf['lang'] ?>" lang="<?php echo $conf['lang'] ?>" dir="<?php echo $lang['direction'] ?>" class="no-js">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title>Icons Plugin</title>
  <script>(function(H){H.className=H.className.replace(/\bno-js\b/,'js')})(document.documentElement)</script>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <?php echo tpl_favicon(array('favicon', 'mobile')) ?>
  <?php tpl_metaheaders()?>
  <style type="text/css">
    body { padding: 20px; }
    main {  width: 75%; float:left; }
    aside { width: 25%; float:left; overflow-y: scroll; height: 500px; }
    .btn-icon { margin: 4px; padding: 4px; }
    .collections { padding: 5px; }
    .collection-samples { margin-bottom: 10px; }
    .collection-category { margin-top: 10px; }
    .collection-name { margin-left: 10px; }
    .collection-box, .preview-box { padding: 5px; }
    .collection-icons { overflow-y: auto; height: 250px; padding: 10px 0px; }
    .collection-info ul { margin: 0; padding: 0; }
    .collection-info ul li { display: inline-block; list-style-type: none; padding-right: 5px; }
    <?php if (!$use_glyphicons): ?>
    footer { bottom: 20px; position: fixed; }
    .col-sm-6 { width:50%; float: left; }
    .col-sm-4 { width:33.3%; float: left; }
    /**.tab-pane, .hide { display: none; }**/
    button.active { border-style: inset; }
    <?php endif;?>
  </style>
  <script type="text/javascript" src="popup.js" defer="defer"></script>
</head>
<body class="container-fluid dokuwiki">

  <div>

    <aside>
      <div class="collections">
        <?php foreach ($categories as $category => $collects): ?>
        <h4 class="collection-category"><?php echo $category; ?></h4>
        <?php foreach ($collects as $collection): $data = $collections[$collection];?>
        <div class="collection-name">
          <a href="<?php echo $popup_url; ?>?pack=<?php echo $collection; ?>" data-pack="<?php echo $collection; ?>"><?php echo $data['name']; ?></a>
          <div class="collection-samples">
            <?php foreach ($data['samples'] as $sample) {echo "&nbsp;<span class='iconify' data-icon='$collection:$sample' data-height='16'></span>&nbsp;";}?>
          </div>
        </div>
        <?php
          endforeach;
          endforeach;
        ?>
      </div>
    </aside>

    <main>

      <?php
        if ($pack):
          $collection_name = $pack;
          $collection_data = json_decode(io_readFile("$collections_dir/$collection_name.json.gz"), true);
      ?>

      <div class="collection-box">

        <div class="collection-info">
          <h3>
            <?php echo $collection_data['info']['name']; ?> <?php echo (isset($collection_data['info']['version']) ? '<small>v' . $collection_data['info']['version'] . '</small>' : ''); ?>
          </h3>
          <ul>
            <li>
              <strong>Icon prefix</strong> <code><?php echo $collection_data['prefix']; ?></code>
            </li>
            <li>
              <strong>License</strong> <a href="<?php echo $collection_data['info']['license']['url']; ?>" target="_blank"><?php echo $collection_data['info']['license']['title']; ?></a>
            </li>
            <li>
              <strong>Author</strong> <a href="<?php echo $collection_data['info']['author']['url']; ?>" target="_blank"><?php echo $collection_data['info']['author']['name']; ?></a>
            </li>
            <li>
              <strong>Total icons</strong> <?php echo $collection_data['info']['total']; ?>
            </li>
          </ul>
        </div>
        <div class="collection-icons">
          <?php foreach (array_keys($collection_data['icons']) as $icon): ?>
            <div class="col-sm-4">
              <button class="btn btn-default btn-xs btn-icon" title="<?php echo $icon ?>" data-icon-name="<?php echo $icon ?>">
                <span class="iconify" data-icon="<?php echo $collection_name; ?>:<?php echo $icon ?>" data-height="32"></span>
              </button>
              <small><?php echo $icon ?></small>
            </div>
          <?php endforeach?>
        </div>
      </div>
      <?php endif;?>

      <div class="preview-box">

        <hr/>

        <div class="row">

          <div class="box-alignment col-sm-6">
            <label>Alignment</label>
            <div class="btn-group btn-group-xs">
              <button class="button btn btn-default active" data-icon-align="" title="Use no align">
                <img src="../../../images/media_align_noalign.png" />
              </button>
              <button class="button btn btn-default" data-icon-align="left" title="Align the icon on the left">
                <img src="../../../images/media_align_left.png" />
              </button>
              <button class="button btn btn-default" data-icon-align="center" title="Align the icon in the center">
                <img src="../../../images/media_align_center.png" />
              </button>
              <button class="button btn btn-default" data-icon-align="right" title="Align the icon on the right">
                <img src="../../../images/media_align_right.png" />
              </button>
            </div>
          </div>

          <div class="box-size col-sm-6">
            <label>Image size</label>
            <div class="btn-group btn-group-xs">
              <button class="button btn btn-default" data-icon-size="small" title="Small size">
                <img src="../../../images/media_size_small.png" />
              </button><button class="button btn btn-default" data-icon-size="medium" title="Medium size">
                <img src="../../../images/media_size_medium.png" />
              </button><button class="button btn btn-default" data-icon-size="large" title="Large size">
                <img src="../../../images/media_size_large.png" />
              </button><button class="button btn btn-default active" data-icon-size="original" title="Original size">
                <img src="../../../images/media_size_original.png" />
              </button>
            </div>
          </div>

        </div>

        <p>&nbsp;</p>

        <label>Preview</label>
        <pre id="preview"></pre>

      </div>

    </main>
  </div>

  <footer>
    <nav class="navbar navbar-default navbar-fixed-bottom">
      <div class="container-fluid">
        <div class="navbar-text">
          <button type="button" id="btn-preview" class="hidden btn btn-default">Preview code</button>
          <button type="button" id="btn-insert" class="btn btn-success">Insert</button>
          <button type="button" id="btn-reset" class="btn btn-default">Reset</button>
        </div>
      </div>
    </nav>
  </footer>

  <input type="hidden" id="output" />
  <input type="hidden" id="icon_pack" value="<?php echo $INPUT->str('pack', ''); ?>" />
  <input type="hidden" id="icon_name" />
  <input type="hidden" id="icon_size" />
  <input type="hidden" id="icon_align" />

</body>
</html>
