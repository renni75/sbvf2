<?php
/**
 * Helper class for displaying search-related HTML chunks.
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2011.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category VuFind2
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
namespace VuFind\View\Helper\Blueprint;
use Zend\View\Helper\AbstractHelper;

/**
 * Helper class for displaying search-related HTML chunks.
 *
 * @category VuFind2
 * @package  View_Helpers
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Search extends AbstractHelper
{
    /**
     * Support function to display spelling suggestions.
     *
     * @param string                          $msg     HTML to display at the top of
     * the spelling section.
     * @param \VuFind\Search\Base\Results     $results Results object
     * @param \Zend\View\Renderer\PhpRenderer $view    View renderer object
     *
     * @return string
     */
    public function renderSpellingSuggestions($msg, $results, $view)
    {
        $spellingSuggestions = $results->getSpellingSuggestions();
        if (empty($spellingSuggestions)) {
            return '';
        }

        $html = '<div class="corrections">';
        $html .= $msg;
        foreach ($spellingSuggestions as $term => $details) {
            $html .= '<br/>' . $view->escapeHtml($term) . ' &raquo; ';
            $i = 0;
            foreach ($details['suggestions'] as $word => $data) {
                if ($i++ > 0) {
                    $html .= ', ';
                }
                $html .= '<a href="'
                    . $results->getUrlQuery()
                        ->replaceTerm($term, $data['new_term'])
                    . '">' . $view->escapeHtml($word) . '</a>';
                if (isset($data['expand_term']) && !empty($data['expand_term'])) {
                    $html .= '<a href="'
                        . $results->getUrlQuery()
                            ->replaceTerm($term, $data['expand_term'])
                        . '"><img src="' . $view->imageLink('silk/expand.png')
                        . '" alt="' . $view->transEsc('spell_expand_alt')
                        . '"/></a>';
                }
            }
        }
        $html .= '</div>';
        return $html;
    }
}