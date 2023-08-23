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
 * @package    block_search_user
 * @copyright  2022 onwards Harry@Bleckert.com for ASH Berlin <https://ASH-Berlin.eu>
 * Fork of block_quick_user
 * @copyright  2019 Conn Warwicker <conn@cmrwarwicker.com>
 * @link       https://github.com/HarryBleckert/moodle-block_search_user
 * @license    https://gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {

    var module = {};

    module.bind = function(courseID){

        // Result links toggle.
        $('.search_user_toggle').each( function(){

            $(this).off('click');
            $(this).on('click', function(){

                var id = $(this).data('userid');
                $('#hidden_user_' + id).toggle();

                var pix = M.cfg.wwwroot + '/blocks/search_user/pix/' +
                    ( ( $(this).attr('src').indexOf('plus.png') > -1 ) ? 'minus.png' : 'plus.png' );
                $(this).attr('src', pix);

            });

        });

        // Submit form.
        $('#search_user_form').off('submit');
        $('#search_user_form').on('submit', function(e){

            var search = $('#search_user_search').val();
            search.trim();

            var results = $('#search_user_results');
            results.html('');

            // If the search term was empty, just stop.
            if (search == ''){
                e.preventDefault();
                e.stopPropagation();
                return false;
            }

            // Display the loading gif while the results are fetched.
            var img = '<img id="search_user_loading" src="' + M.cfg.wwwroot + '/blocks/search_user/pix/load.gif" />';
            results.html('<div class="search_user_loading">' + img + '</div>');

            // Ajax call to search script.
            $.post(M.cfg.wwwroot + '/blocks/search_user/ajax/search.php', {
                course: courseID,
                search: search
            }).done( function(data){
                results.html(data);
                module.bind(courseID);
            }).fail( function(){
                results.html('');
            } );

            e.preventDefault();
            e.stopPropagation();
            return true;

        });

        // Clear results.
        $('#search_user_clear').off('click');
        $('#search_user_clear').on('click', function(e){

            $('#search_user_search').val('');
            $('#search_user_results').html('');

            e.preventDefault();
            e.stopPropagation();
            return true;

        });

    };

    module.init = function(courseID){
        module.bind(courseID);
    };

    return module;

});
