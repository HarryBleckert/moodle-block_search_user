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
 * @package    block_search_user
 * @copyright  2022 onwards Harry.Bleckert@ASH-Berlin.eu for ASH Berlin
 * Fork of block_quick_user
 * @copyright  2019 Conn Warwicker <conn@cmrwarwicker.com>..
 * @link       https://github.com/HarryBleckert/moodle-block_search_user
 * @license    http://gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

require_once(__DIR__ . '/../../config.php');

global $DB, $CFG, $PAGE;

//$PAGE->set_context();
$show_user = su_get_string('show_user');
$CFG->debugdeveloper = false;$CFG->debugdisplay = 0;
require_login();
if ( isset($_REQUEST['id']) AND !empty($_REQUEST['id']) )
{	$user = $DB->get_record('user', array('id' => $_REQUEST['id']), '*', MUST_EXIST); }

if ( !isset($user->id) or empty($user->id) ) 
{	print "<br>\n".su_get_string('user_id_missing')."!<br>\n"; exit; }
$userid = $user->id; 
if ( !($user->deleted OR $user->suspended ) )
{	$context = context_user::instance($user->id); }
else
{	$context = context_course::instance(1); }
$PAGE->set_context($context);
//$ereporting = error_reporting();error_reporting(0);
$PAGE->set_url('/block/search_user/show_user.php', array('id'=>$user->id, 'courseid'=>SITEID ));
$PAGE->requires->css('/block/search_user/styles.css');
$PAGE->set_pagelayout('report');
$PAGE->set_title($show_user);
$PAGE->set_heading($show_user);
//$CFG->debugdeveloper = true; $CFG->debugdisplay = 1;
//error_reporting($ereporting);
echo $OUTPUT->header();
//print '<link rel="stylesheet" type="text/css"  href="'.$CFG->wwwroot.'/block/search_user/styles.css">';


print "<style>table, td, th {  border: 1px solid; padding:3px; } table { border-collapse: collapse; } </style>";

//print "<html><head><title>Konto anzeigen</title></head><body>\n";
//print "<h1>$show_user</h1>\n";
$active = su_get_string('active');
$suspended = su_get_string('suspended');
$deleted = su_get_string('deleted');

$timecreated = $user->timecreated ?date( "d.m.Y H:s", $user->timecreated ) :'<span style="color:red">'.su_get_string('unknown').'</span>';
$firstaccess = $user->firstaccess ?date( "d.m.Y", $user->firstaccess ) :'<span style="color:red">'.su_get_string('never').'</span>';
$lastaccess  = $user->lastaccess ?date( "d.m.Y H:s", $user->lastaccess ) :'<span style="color:red">'.su_get_string('never').'</span>';
$fullname = "<b>" .$user->firstname . " " . $user->lastname . "</b>" 
			. ( $user->firstname != $user->alternatename ?" (".su_get_string('alias').": " . $user->alternatename . ")" :"" );
$email 	= strstr($user->email, "@" ) ?$user->email :'<span style="color:red">'.$user->email.'</span>';
$status = !($user->deleted + $user->suspended) ?$active :( $user->deleted ?$deleted : $suspended );
$profile = '<a href="/user/profile.php?id=' . $userid . '" target="profile"><b>'.su_get_string('profile') . '</b></a>';
$description = trim($user->description);
if ( !empty( $description ) )
{	$description = " - <b>" . su_get_string('user_description') . "</b>:<br>\n$description"; }
if ( $status != $active )
{	$status = '<span style="color:red;">' . $status . "</span>"; }

// only use for ASH
if ( isset($CFG->ash) )
{	$expiration_date = "";
	$sql = "SELECT v FROM ash_user_attr WHERE id=$userid AND n='expiration_date' LIMIT 1";
	$auth = $user->auth;
	if ( $auth == "manual" AND !empty($DB->get_record_sql( $sql )->v) )
	{	$auth = su_get_string('external'); $expiration_date = " " .su_get_string('expiration') .": <b>" .$DB->get_record_sql( $sql )->v . "</b> -"; }
}

print "<h2>$fullname</h2>\n";
print 	su_get_string('status') . ": <b>$status</b> - " .su_get_string('username').": <b>$user->username</b> - ".
		su_get_string('userid') . ": <b>$userid</b> - " . su_get_string('auth') . ": <b>$auth</b> -$expiration_date ".
		su_get_string('lang') . ": <b>$user->lang</b><br>\n";

print 	su_get_string('timecreated')  . ": <b>$timecreated</b> - " 
		.su_get_string('firstaccess') . ": <b>$firstaccess</b> - "
		.su_get_string('lastaccess')  . ": <b>$lastaccess</b><br>\n";
print 	$profile . ' - ' . su_get_string('email') .": <b>". $email. "</b>$description";

print '<span id="show"></span>' . "\n";

if ( $courses = courses_of_mid( $userid ) )
{	if ( false AND empty( $_COOKIE['show_user_hint'] ) )
	{	setcookie('show_user_hint', "true", time() + (3600*24*180) );
		print "<br><b". ' style="color:magenta;"'.">" . su_get_string('open_parts_list',su_get_string('course_shortname')) ."</b>\n";
	}

	print "<br><b>".(count($courses)>1 ?count($courses)." " . su_get_string('courses') :su_get_string('course'))."</b>:<table>\n";
	print "<tr " .'style="font-weight:bolder;"'.">\n";
	print "<th>" .su_get_string('courseid') . "</th><th>" .su_get_string('role'). "</th><th>"
				 .su_get_string('course_shortname')."</th><th>".su_get_string('course_fullname')."</th><th>"
                 .su_get_string('category') ."</th>\n";
	print "</tr>\n";
	
	foreach ( $courses as $course )
	{	$roles = su_roles_in_course( $userid, $course->courseid );
		$shortname = $course->shortname;
		$fullname = $course->fullname;
		$show = su_get_enrolled_users( $course );
		$fullname = '<span id="short_'.$course->courseid.'" 
							onclick="toggleviewparts(\''.$course->courseid.'\');">' . $fullname . '</span>'.$show; 	
		$viewCourse = '<a href="/course/view.php?id='.$course->courseid.'" target="viewCourse">'.$course->courseid ."</a>\n";
        $Studiengang = get_course_of_studies($course->courseid);  // get Studiengang with link
        // $semester = su_get_course_of_studies($courseid, true, true);  // get Semester with link
		print '<tr title="' . su_get_string('open_parts_list', su_get_string('course_fullname') ) .'">' ."\n";
		print "<td>$viewCourse</td><td>$roles</td><td>$shortname</td><td>$fullname</td><td>$Studiengang</td>\n";

		print "</tr>\n";
	}	
	print "</table>\n";
}


//print var_export($roles, true );
//print "\n</body></html>\n";
echo $OUTPUT->footer();


// get_string for get_string('show_user', 'block_search_user');
function su_get_string( $string, $param="" )
{	$plugin = 'block_search_user';
	$trstring = get_string($string, $plugin, $param );
	/*if ( !empty($param) )
	{	$trstring = get_string($string, $plugin, $param ); }
	else
	{	$trstring = get_string($string, $plugin ); }
	*/
	if ( stristr( $trstring, ']' ) )
	{	return $string; }
	return $trstring;
}


//return all course ids a given mdl_user id is assossiated with
function courses_of_mid( $userid ) {
    global $DB;
	$sql="SELECT e.id, e.courseid, c.shortname, c.fullname FROM {enrol} e, {course} c WHERE e.courseid=c.id AND e.id IN " .
      "(SELECT enrolid FROM {user_enrolments} ue WHERE ue.userid=$userid) ORDER BY e.courseid DESC";
    return $DB->get_records_sql( $sql );
}


// get role name of given mdl_user id in course courseid 
function su_roles_in_course( $userid, $courseid ) 
{   $context = context_course::instance($courseid);
	$roles = get_user_roles($context, $userid, true);
	$rolenames = array();
	foreach ( $roles as $role )
	{	$rolenames[$role->name] = $role->name; }
	return implode(", ",$rolenames);
}


function su_get_enrolled_users( $course )
{	global $DB;
	$participants = $inactive_participants = $studies = array();
	$context = context_course::instance($course->courseid);
	$users_ids = $inactive_users_ids = array();	
	$users = get_enrolled_users( $context, $withcapability = '', 0, 'u.*');
	foreach ($users as $id => $user) 
	{	//print var_export($head,true);	exit;
		if ( ($user->deleted+$user->suspended) == 0 )
		{	$roles = su_roles_in_course( $user->id, $course->courseid );
			$lastcourseaccess = $DB->get_field( "user_lastaccess", "timeaccess", array( 'userid' => $user->id, 'courseid' => $course->courseid ) );
			/*$DB->get_record_sql("SELECT timeaccess FROM {user_lastaccess} 
													 WHERE userid=$user->id AND courseid = $course->courseid LIMIT1 1")->timeaccess;*/
			$users_ids[] = array( 'id' => $user->id, 'fullname' => $user->firstname . " " . $user->lastname,
							  'email' => $user->email, 'lastaccess' => $user->lastaccess, 
							  'lastcourseaccess' => $lastcourseaccess ?date("d.m.Y", $lastcourseaccess) :su_get_string('never'),
							  "roles" => $roles );
			//print "</td></tr></table>".var_export( $user,true )."<hr>ids:<br>".var_export( $users_ids,true );exit;
		}
	}
	$show = "\n" . '<div id="view_'.$course->courseid.'" onclick="toggleviewparts(\''.$course->courseid.'\');"
			title="'. su_get_string("hide_parts_list").'" 
			style="display:none;right:0px;opacity:1;background-color:navy;color:yellow;position:relative;top:0px;border:3px solid blue;">';
	$show .= '<span style="float:left;font-size:150%;font-weight:bolder;">' . count($users_ids) ." ". su_get_string("participants") . '</span>
				<span style="float:left;">' ."&nbsp;&nbsp;(" .$course->shortname.')</span><span style="float:right;font-size:150%;font-weight:bolder;">&times;</span><br style="clear:both;">';
	$show .= "<table>\n";
	$show .= '<tr style="font-weight:bold;"><td>ID</td><td>'.su_get_string('fullname').'</td><td>'.su_get_string('email').'</td><td>'
				.su_get_string('last_course_access').'</td><td>'.su_get_string('roles').'</td></tr>'."\n";
	foreach ( $users_ids AS $user )
	{	$show .=  "<tr><td>".$user['id']."</td><td>".$user['fullname']."</td><td>".$user['email']."</td><td>".$user['lastcourseaccess']
			  ."</td><td>".$user['roles']."</td></tr>\n"; }
	$show .=  "</table></div>\n\n";
	
	return $show;
}

// get Studiengang name of course from course_categories path
function get_course_of_studies( $courseid )
{	global $DB;
	// ASH specific organisation of course of studies (Studiengänge) = Level 2
	// Semester is level 3
	$COURSE_OF_STUDIES_PATH = 2;

	$course = $DB->get_record('course', array('id' => $courseid), '*'); //get_course($courseid);
	if ( !isset($course->category) )
	{	return ""; }
	$cat = $DB->get_record_sql( "select id,path from {course_categories} where id=".$course->category);
	//print_r("Course category path: " .$cat->path . ""<br>\n");
	$path = explode("/",$cat->path);
	$semesterCat = $path[1];
	if ( empty($semesterCat) OR !isset($path[$COURSE_OF_STUDIES_PATH]) )
	{	return ""; }
	$studiengangCat = $path[$COURSE_OF_STUDIES_PATH];
	//echo ""Course of Studies path: $studiengangCat<br>\n";	
	if ( empty($studiengangCat) )
	{	return ""; }

	$studiengang = $DB->get_record_sql( "select id,name from {course_categories} where id=$studiengangCat");
	if ( isset($studiengang->name) AND !empty($studiengang->name) )
	{	return $studiengang->name; }
	return "";
}

?>
<script>
var showing = false;
function toggleviewparts( courseid )
{	var course = 'view_'+courseid;
	var role   = 'short_'+courseid;
	var span   = document.getElementById( role );
	var ele = document.getElementById( course );
	if ( ele.style.display === 'block' )
	{	ele.style.display="none"; showing = false; span.style.color = "black"; span.style.backgroundColor = "white"; span.scrollIntoView(); }
	else if ( showing === false )
	{	ele.style.display="block"; showing = true; span.style.color = "yellow"; span.style.backgroundColor = "darkblue"; span.scrollIntoView(); }
	return true;
}
</script>
<?php

// get Studiengang and optionally Semester name of course from course_categories path 2 (ASH)
function su_get_course_of_studies($courseid, $link = false, $showsemester = false) {
global $DB;
$COURSE_OF_STUDIES_PATH = 2;
if (empty($courseid) or $courseid == SITEID) {
return "";
}
if (!$showsemester and !$link) {
    $studiengang = $DB->get_record_sql("SELECT id, name AS name, description FROM {course} 
                                         WHERE id = $courseid LIMIT 1");
    if (isset($studiengang->name) and !empty($studiengang->name)) {
        return $studiengang->name;
    }
}
if ($showsemester or $link or !isset($studiengang->name) or empty($studiengang->name)) {
    $course = $DB->get_record('course', array('id' => $courseid), '*'); //get_course($courseid);
    if (!isset($course->category) and !$showsemester) {
    return "";
    }
$cat = $DB->get_record_sql("select id,path from {course_categories} where id=" . $course->category);
//print_r("Course category path: " .$cat->path . ""<br>\n");
$path = explode("/", $cat->path);
$semesterCat = (safeCount($path) >= $COURSE_OF_STUDIES_PATH ? $path[1] : 0);
if ($showsemester and (empty($semesterCat) or !isset($path[$COURSE_OF_STUDIES_PATH]))) {
    return "";
}
$studiengangCat = $path[min(safeCount($path) - 1, $COURSE_OF_STUDIES_PATH)];
//echo ""Course of Studies path: $studiengangCat<br>\n";
if (empty($studiengangCat) and !$showsemester) {
    return "";
}

if ($showsemester) {
if (!isset($path[$COURSE_OF_STUDIES_PATH + 1])) {
    return "./.";
}
$SsemesterCat = $path[$COURSE_OF_STUDIES_PATH + 1];
$semester = $DB->get_record_sql("select id,name from {course_categories} where id=" . $SsemesterCat);
//echo ""Semester category: $SemesterCat - Semester: $semester->name<br>\n";
if (!$semester or !isset($semester->name) or empty($semester->name) or !stristr($semester->name, 'semester')) {
    return "./.";
}
if ($link and empty($_SESSION["LoggedInAs"])) {
    return '<a href="/course/index.php?categoryid=' . $semester->id . '" target="semester">' . $semester->name .
    "</a>\n";
}
return $semester->name;
}
}
if (isset($studiengangCat) and empty ($studiengang->name)) {
$studiengang = $DB->get_record_sql("select id,name from {course_categories} where id=$studiengangCat");
}
//$GLOBALS["studiengang"] = $studiengang;
// return name of studiengang
//echo ""Course of Studies: $studiengang->name<br>\n";
if (isset($studiengang->name) and
!empty($studiengang->name)) {    //set new value to course->customfield_studiengang, disabled because customfield was removed
    if ($link and !empty($studiengang->id) and empty($_SESSION["LoggedInAs"])) {
        return '<a href="/course/index.php?categoryid=' . $studiengang->id . '" target="studiengang">' . $studiengang->name .
        "</a>\n";
    }
    $_SESSION['course_of_studies'] = $studiengang->name;
    return $studiengang->name;
    }
    return "";
}

