<?php
/**
 * DokuWiki Plugin datecount (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

class syntax_plugin_datecount extends DokuWiki_Syntax_Plugin {
    /**
     * @return string Syntax mode type
     */
    public function getType() {
        return 'substition';
    }
    /**
     * @return string Paragraph type
     */
    public function getPType() {
        return 'normal';
    }
    /**
     * @return int Sort order - Low numbers go before high numbers
     */
    public function getSort() {
        return 100;
    }

    /**
     * Connect lookup pattern to lexer.
     *
     * @param string $mode Parser mode
     */
    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('{{datecount\-.+?}}', $mode, 'plugin_datecount');
    }

    /**
     * Handle matches of the datecount syntax
     *
     * @param string          $match   The match of the syntax
     * @param int             $state   The state of the handler
     * @param int             $pos     The position in the document
     * @param Doku_Handler    $handler The handler
     * @return array Data for the renderer
     */
    public function handle($match, $state, $pos, Doku_Handler $handler) {
        if (DOKU_LEXER_SPECIAL == $state) {
            try {
                $date = new DateTime(substr($match, 12, -2));
            } catch (Exception $e) {
                return [];
            }
            return ['date' => $date];
        }
        return [];
    }

    /**
     * Render xhtml output or metadata
     *
     * @param string         $mode      Renderer mode (supported modes: xhtml)
     * @param Doku_Renderer  $renderer  The renderer
     * @param array          $data      The data from the handler() function
     * @return bool If rendering was successful.
     */
    public function render($mode, Doku_Renderer $renderer, $data) {
        if($mode == 'xhtml' && $this->_isValidDateTime($data)) {
            $renderer->doc.= $this->_parseDateCount($data['date']);
            return true;
        }
        return false;
    }

    private function _isValidDateTime($data) {
        return isset($data['date']) && $data['date'] instanceof DateTime && 0 < $data['date']->format('U');
    }

    private function _parseDateCount(DateTime $date) {
        $diff = $date->diff(new DateTime());
        list($diffInMonths, $diffDaysLeft, $diffDaysTotal) = explode(';', $diff->format('%m;%d;%a'));
        $diffInWeeks = floor($diffDaysTotal / 7);
        $diffDaysLeftFromWeeks = $diffDaysTotal % 7;
        $diffInYears = floor($diffInMonths / 12);
        $diffMonthLeft = $diffInMonths % 12;
        return 	'<ul>' .
            '<li>' . $diffInYears . ' Jahre, ' . $diffMonthLeft . ' Monate und ' . $diffDaysLeft . ' Tage</li>' .
            '<li>' . $diffInMonths . ' Monate und ' . $diffDaysLeft . ' Tage</li>' .
            '<li>' . $diffInWeeks . ' Wochen und ' . $diffDaysLeftFromWeeks . ' Tage</li>' .
            '<li>' . round($diffDaysTotal / 7, 2) . ' Wochen</li>' .
            '<li>' . $diffDaysTotal . ' Tage</li>' . 
        '</ul>';
    }
}