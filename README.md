Quick User Search
==========
[![Build Status](https://travis-ci.org/cwarwicker/moodle-block_search_user.svg?branch=master)](https://travis-ci.org/cwarwicker/moodle-block_search_user)
[![Open Issues](https://img.shields.io/github/issues/cwarwicker/moodle-block_search_user)](https://github.com/cwarwicker/moodle-block_search_user/issues)
[![License](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)

![Moodle 3.4 supported](https://img.shields.io/badge/Moodle-3.4-brightgreen)
![Moodle 3.5 supported](https://img.shields.io/badge/Moodle-3.5-brightgreen)
![Moodle 3.6 supported](https://img.shields.io/badge/Moodle-3.6-brightgreen)
![Moodle 3.9 supported](https://img.shields.io/badge/Moodle-3.9-brightgreen)
![Moodle 4.1 supported](https://img.shields.io/badge/Moodle-4.1-brightgreen)
![Moodle 4.2 supported](https://img.shields.io/badge/Moodle-4.2-brightgreen)


The search_user block allows you to search quickly for users, without having to go through the Moodle user interface.

* As site admin or user with block/block_search_user:searchall capability - Search all users in the site (if the block
    is on the site home or "my dashboard")
* As a user with block/block_search_user:search capability - Search all users on the course


Requirements
------------
Moodle 3.9+

Screenshots
-----------
These screenshots were taken on a plain Moodle installation with no fancy theme installed. Appearances may vary slightly depending on your theme.

The Block:

![block](pix/screenshots/block.png)

The block with some search results:

![block-with-results](pix/screenshots/block-with-results.png)

Search results expanded to see extra links:

![block-with-expanded-results](pix/screenshots/block-with-results-expanded.png)


Installation
------------
**From github:**
1. Download the latest version of the plugin from the [Releases](https://github.com/cwarwicker/moodle-block_search_user/releases) page.
2. Extract the directory from the zip file and rename it to 'search_user' if it is not already named as such.
3. Place the 'search_user' folder into your Moodle site's */blocks/* directory.
4. Run the Moodle upgrade process either through the web interface or command line.
5. Add the block to a page and start using it

License
-------
https://www.gnu.org/licenses/gpl-3.0

Support
-------
If you need any help using the block, or wish to report a bug or feature request, please use the issue tracking system: https://github.com/cwarwicker/moodle-block_search_user/issues
