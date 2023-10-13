<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Quick User block
 * @package    block_search_user
 * @copyright  2022 onwards Harry@Bleckert.com for ASH Berlin <https://ASH-Berlin.eu>
 * Fork of block_quick_user
 * @copyright  2019 Conn Warwicker <conn@cmrwarwicker.com>
 * @link       https://github.com/HarryBleckert/moodle-block_search_user
 * @license    https://gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Quick User block
 * @package    block_search_user
 * @copyright  2022 onwards Harry@Bleckert.com for ASH Berlin {link:https://ASH-Berlin.eu}
 * Fork of block_quick_user
 * @copyright  2019 Conn Warwicker <conn@cmrwarwicker.com>
 * @link       https://github.com/HarryBleckert/moodle-block_search_user
 * @license    https://gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_search_user extends block_base
{

    /**
     * Initialise block and set title
     * @throws coding_exception
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_search_user');
    }

    /**
     * Get the content to display in the block
     * @return stdClass|stdObject
     * @throws coding_exception
     */
    public function get_content() {

        global $COURSE;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';

        $context = context_course::instance($COURSE->id);

        if (!has_capability('block/search_user:search', $context)) {
            return $this->content;
        }

        // If it's not on a course, then they need searchall, as by default it searches course users.
        if ($COURSE->id === SITEID && !has_capability('block/search_user:searchall', $context)) {
            return $this->content;
        }

        // Search bar.

        // Form input.
        $this->content->text .= html_writer::start_tag('div', array('id' => 'search_user'));
            $this->content->text .= html_writer::tag(
                'form',
                html_writer::tag('input', null, array('id' => 'search_user_search', 'type' => 'text', 'placeholder' => get_string('placeholder', 'block_search_user') ) ),
                array('id' => 'search_user_form', 'style' => 'display:inline;', 'method' => 'post', 'action' => '')
            );
        $this->content->text .= html_writer::end_tag('div');

        // Clear results link.
        $this->content->text .= html_writer::start_tag('span', array('class' => 'search_user_clear','style' => 'text-align:center;'));
            $this->content->text .= html_writer::tag(
                'small',
                html_writer::link('#', get_string('clear', 'block_search_user'),
                array('id' => 'search_user_clear'))
            );
        $this->content->text .= html_writer::end_tag('span');

        // Results.
        $this->content->text .= html_writer::tag('div', null, array('id' => 'search_user_results'));


        $this->page->requires->js_call_amd('block_search_user/module', 'init', array($COURSE->id));

        return $this->content;

    }

}
