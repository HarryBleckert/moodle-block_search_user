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
 * Privacy provider class
 *
 * @package    block_search_user\privacy
 * @copyright  2022 onwards Harry@Bleckert.com for ASH Berlin <https://ASH-Berlin.eu>
 * Fork of block_quick_user
 * @copyright  2019 Conn Warwicker <conn@cmrwarwicker.com>
 * @link       https://github.com/HarryBleckert/moodle-block_search_user
 * @license    https://gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_search_user\privacy;

use core_privacy\local\metadata\null_provider;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy provider class
 *
 * @package    block_search_user
 * @copyright  2022 onwards Harry@Bleckert.com for ASH Berlin <https://ASH-Berlin.eu>
 * Fork of block_quick_user
 * @copyright  2019 Conn Warwicker <conn@cmrwarwicker.com>
 * @link       https://github.com/HarryBleckert/moodle-block_search_user
 * @license    https://gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements null_provider {

    /**
     * Get the language string identifier with the component's language
     * file to explain why this plugin stores no data.
     *
     * @return  string
     */
    public static function get_reason() : string {
        return 'privacy:metadata';
    }
}
