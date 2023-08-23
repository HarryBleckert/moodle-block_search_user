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
 * AJAX search script
 * @package    block_search_user
 * @copyright  2022 onwards Harry@Bleckert.com for ASH Berlin <https://ASH-Berlin.eu>
 * Fork of block_quick_user
 * @copyright  2019 Conn Warwicker <conn@cmrwarwicker.com>
 * @link       https://github.com/HarryBleckert/moodle-block_search_user
 * @license    https://gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_login();

$searchterm = required_param('search', PARAM_TEXT);
$courseid = required_param('course', PARAM_INT);

$context = context_course::instance($courseid);
if (!has_capability('block/search_user:search', $context) &&
    !has_capability('block/search_user:searchall', $context)) {
    exit;
}

// Check that the block is on a valid course.
$course = get_course($courseid);
if (!$course) {
    exit;
}

// Check that something was actually searched for, not just an empty string.
$searchterm = trim($searchterm);
if ($searchterm === '') {
    exit;
}

$PAGE->set_context($context);

$search = new \block_search_user\search();
$search->set_course($course);
$search->set_context($context);
$results = $search->results($searchterm);

$output = "";

// Exact Results.
$output .= html_writer::tag('p', get_string('exactresults', 'block_search_user'),
    array('class' => 'search_user_results_heading'));

// If there are exact results.
if ($results['exact']) {

    foreach ($results['exact'] as $result) {
        $output .= \block_search_user\user::info($result->id);
    }

} else {

    // If not, display no results message.
    $output .= html_writer::tag('em', get_string('noresults', 'block_search_user') . '...');

}

$output .= html_writer::empty_tag('hr', null);

// Similar Results.
$output .= html_writer::tag('p', get_string('similarresults', 'block_search_user'),
    array('class' => 'search_user_results_heading'));

// If there are similar results.
if ($results['similar']) {

    foreach ($results['similar'] as $result) {
        $output .= \block_search_user\user::info($result->id);
    }

} else {

    // If not, display no results message.
    $output .= html_writer::tag('em', get_string('noresults', 'block_search_user') . '...');

}

echo $output;
exit;
