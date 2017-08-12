<?php
/**
 * DokuWiki Plugin datecount (Renderer Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

require_once DOKU_INC.'inc/parser/renderer.php';

class renderer_plugin_datecount extends Doku_Renderer {

    /**
     * The format this renderer produces
     */
    public function getFormat(){
        return 'datecount';
    }

    // FIXME implement all methods of Doku_Renderer here
}

