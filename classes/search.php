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
 * Search class for search_user
 * @package    block_search_user
 * @copyright  2022 onwards Harry@Bleckert.com for ASH Berlin <https://ASH-Berlin.eu>
 * Fork of block_quick_user
 * @copyright  2019 Conn Warwicker <conn@cmrwarwicker.com>
 * @link       https://github.com/HarryBleckert/moodle-block_search_user
 * @license    https://gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


namespace block_search_user;

defined('MOODLE_INTERNAL') || die();


class search {

    /**
     * Course object
     * @var stdClass
     */
    private $course;

    /**
     * Conext object
     * @var context
     */
    private $context;

    /**
     * Set the course we are searching in.
     * @param \stdClass $course
     * @return void
     */
    public function set_course(\stdClass $course) {
        $this->course = $course;
    }

    /**
     * Set the context we are searching in.
     * @param \context $context
     * @return void
     */
    public function set_context(\context $context) {
        $this->context = $context;
    }

    /**
     * Get the results of the search.
     * @param $search Text to search for
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function results($search) {

        global $DB;

        $results = array(
            'exact' => array(),
            'similar' => array()
        );

        // First find out what the search limit should be.
        $limit = get_config('block_search_user', 'limit');
        if (!$limit) {
            $limit = 50;
        }
		
        $concat = $DB->sql_concat('u.firstname', "' '", 'u.lastname');

		// ignore GDPR deleted records
		$gdprDeleted = "AND NOT (u.deleted=1 and (u.username like '%@%' OR u.username like 'unknown.%' 
						OR (u.email NOT like '%@%' AND u.email not ilike 'unknown')))";
		
        // Build up the SQL to search the courses.
        // This searches the user's enrolled courses and any courses in any category they are enrolled onto.
        $sql = array();
        $sql['select'] = "SELECT DISTINCT u.*  ";
        $sql['from'] = "FROM {user} u  ";
        $sql['join'] = "INNER JOIN {role_assignments} r on r.userid = u.id
                        INNER JOIN {context} x on x.id = r.contextid and x.contextlevel = ? and x.instanceid = ?  ";

        // First search for exact matches.
        $sql['where'] = "WHERE (u.username = ? OR u.idnumber = ?  OR email = ? OR {$concat} = ? ) $gdprDeleted";
        $sql['order'] = "ORDER BY u.lastname ASC, u.firstname ASC, u.username ASC, u.idnumber ASC  ";
        $sqlparams = array( CONTEXT_COURSE, $this->course->id, $search, $search, $search, $search );

        // If we have the capability to searchall users, remove the join section and just search in mdl_user.
        // Only if it's on the site home though. If it's on a course, we still want to search those users.
        if (has_capability('block/search_user:searchall', $this->context) && $this->course->id == SITEID) {
            $sql['join'] = '';
            $sqlparams = array( $search, $search, $search, $search );
        }

        $fullsql = implode(" ", $sql);

        $results['exact'] = $DB->get_records_sql($fullsql, $sqlparams, 0, $limit);

        // Now the similar results.
        $sql['where'] = "
        WHERE
        (
            " . $DB->sql_like('u.username', '?', false, false) . "
            OR
            " . $DB->sql_like('u.idnumber', '?', false, false) . "
			 OR
            " . $DB->sql_like('u.email', '?', false, false) . "
            OR
            " . $DB->sql_like($concat, '?', false, false) . "
        )
        AND
        (
            u.username != ?
            AND
            u.idnumber != ?
            AND
            u.email != ?
            AND
            {$concat} != ?
			$gdprDeleted
        )
        ";

        $sqlparams = array( CONTEXT_COURSE, $this->course->id, "%{$search}%", "%{$search}%", "%{$search}%", "%{$search}%",
                            $search, $search, $search, $search );

        // If we have the capability to searchall users, remove the join section and just search in mdl_user.
        // Only if it's on the site home though. If it's on a course, we still want to search those users.
        if (has_capability('block/search_user:searchall', $this->context) && $this->course->id == SITEID) {
            $sql['join'] = '';
            $sqlparams = array("%{$search}%", "%{$search}%", "%{$search}%", "%{$search}%", $search, $search, $search, $search );
        }

        $fullsql = implode(" ", $sql);
        $results['similar'] = $DB->get_records_sql($fullsql, $sqlparams, 0, $limit);

        return $results;

    }

}
