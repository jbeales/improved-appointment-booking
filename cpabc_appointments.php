<?php
/*
Plugin Name: Improved Appointment Booking Calendar
Plugin URI: 
Description: Improved Appointment Booking Calendar improves on Appointment Booking Calendar by CodePeople.net, ( http://wordpress.dwbooster.com/calendars/appointment-booking-calendar ).
This plugin allows you to easily insert appointments forms into your WP website. Note: Cannot be installed as a must-use plugin, (if you don't know what this is you probably don't have to worry about it).
Requires PHP 5.2, (DateTime)
Version: 1.1
Author: John Beales
Author URI: http://johnbeales.com
License: GPL




Filters: 
cpabc_appointment_buffered_data :   Filters the "buffered data" that's saved 
                                    when an appointment is first created, but 
                                    before it's confirmed, (paid).
cpabc_appointment_description :     Filters the appointment description that's 
                                    saved after an appointment is confirmed, 
                                    (paid). For free appointments, this happens
                                    in the same pageload as 
                                    cpabc_appointment_buffered_data.

                                    One argument is passed to this function, a
                                    database row of appointment data, (including
                                    the buffered data as $arg->buffered_date, 
                                    (yes that's a spelling error).

Actions:

cpabc_end_of_form :                 Runs at the end of the main part of the book
                                    appointment form, but before the CAPTCHA.


*/

// using this instead of __FILE__ with plugins_url lets us symlink the plugin's directory without any trouble
define('CPABC_PLUGIN_FILE', WP_PLUGIN_DIR . '/' . basename(dirname(__FILE__)) . '/' . basename(__FILE__));



/* initialization / install / uninstall functions */

define('CPABC_APPOINTMENTS_DEFAULT_CALENDAR_LANGUAGE', 'EN');
define('CPABC_APPOINTMENTS_DEFAULT_CALENDAR_DATEFORMAT', '0');
define('CPABC_APPOINTMENTS_DEFAULT_CALENDAR_MILITARYTIME', '1');
define('CPABC_APPOINTMENTS_DEFAULT_CALENDAR_WEEKDAY', '0');
define('CPABC_APPOINTMENTS_DEFAULT_CALENDAR_MINDATE', 'today');
define('CPABC_APPOINTMENTS_DEFAULT_CALENDAR_MAXDATE', '');
define('CPABC_APPOINTMENTS_DEFAULT_CALENDAR_PAGES', 3);

define('CPABC_APPOINTMENTS_DEFAULT_ENABLE_PAYPAL', 1);
define('CPABC_APPOINTMENTS_DEFAULT_PAYPAL_EMAIL','put_your@email_here.com');
define('CPABC_APPOINTMENTS_DEFAULT_PRODUCT_NAME','Consultation');
define('CPABC_APPOINTMENTS_DEFAULT_COST','25');
define('CPABC_APPOINTMENTS_DEFAULT_OK_URL',get_site_url());
define('CPABC_APPOINTMENTS_DEFAULT_CANCEL_URL',get_site_url());
define('CPABC_APPOINTMENTS_DEFAULT_CURRENCY','USD');
define('CPABC_APPOINTMENTS_DEFAULT_PAYPAL_LANGUAGE','EN');

define('CPABC_APPOINTMENTS_DEFAULT_ENABLE_REMINDER', 0);
define('CPABC_APPOINTMENTS_DEFAULT_REMINDER_HOURS', 24);
define('CPABC_APPOINTMENTS_DEFAULT_REMINDER_SUBJECT', 'Appointment reminder...');
define('CPABC_APPOINTMENTS_DEFAULT_REMINDER_CONTENT', "This is a reminder for your appointment with the following information:\n\n%INFORMATION%\n\nThank you.\n\nBest regards.");

define('CPABC_APPOINTMENTS_DEFAULT_SUBJECT_CONFIRMATION_EMAIL', 'Thank you for your request...');
define('CPABC_APPOINTMENTS_DEFAULT_CONFIRMATION_EMAIL', "We have received your request with the following information:\n\n%INFORMATION%\n\nThank you.\n\nBest regards.");
define('CPABC_APPOINTMENTS_DEFAULT_SUBJECT_NOTIFICATION_EMAIL','New appointment requested...');
define('CPABC_APPOINTMENTS_DEFAULT_NOTIFICATION_EMAIL', "New appointment made with the following information:\n\n%INFORMATION%\n\nBest regards.");

define('CPABC_APPOINTMENTS_DEFAULT_CP_CAL_CHECKBOXES',"");
define('CPABC_APPOINTMENTS_DEFAULT_EXPLAIN_CP_CAL_CHECKBOXES',"1.00 | Service 1 for us$1.00\n5.00 | Service 2 for us$5.00\n10.00 | Service 3 for us$10.00");


// tables

define('CPABC_APPOINTMENTS_TABLE_NAME_NO_PREFIX', "cpabc_appointments");
define('CPABC_APPOINTMENTS_TABLE_NAME', $wpdb->prefix . CPABC_APPOINTMENTS_TABLE_NAME_NO_PREFIX);

define('CPABC_APPOINTMENTS_CALENDARS_TABLE_NAME_NO_PREFIX', "cpabc_appointment_calendars_data");
define('CPABC_APPOINTMENTS_CALENDARS_TABLE_NAME', $wpdb->prefix ."cpabc_appointment_calendars_data");

define('CPABC_APPOINTMENTS_CONFIG_TABLE_NAME_NO_PREFIX', "cpabc_appointment_calendars");
define('CPABC_APPOINTMENTS_CONFIG_TABLE_NAME', $wpdb->prefix ."cpabc_appointment_calendars");

define('CPABC_APPOINTMENTS_DISCOUNT_CODES_TABLE_NAME_NO_PREFIX', "cpabc_appointments_discount_codes");
define('CPABC_APPOINTMENTS_DISCOUNT_CODES_TABLE_NAME', $wpdb->prefix ."cpabc_appointments_discount_codes");

// calendar constants

define("CPABC_TDEAPP_DEFAULT_CALENDAR_ID","1");
define("CPABC_TDEAPP_DEFAULT_CALENDAR_LANGUAGE","EN");

define("CPABC_TDEAPP_CAL_PREFIX", "cal");
define("CPABC_TDEAPP_CONFIG",CPABC_APPOINTMENTS_CONFIG_TABLE_NAME);
define("CPABC_TDEAPP_CONFIG_ID","id");
define("CPABC_TDEAPP_CONFIG_TITLE","title");
define("CPABC_TDEAPP_CONFIG_USER","uname");
define("CPABC_TDEAPP_CONFIG_PASS","passwd");
define("CPABC_TDEAPP_CONFIG_LANG","lang");
define("CPABC_TDEAPP_CONFIG_CPAGES","cpages");
define("CPABC_TDEAPP_CONFIG_TYPE","ctype");
define("CPABC_TDEAPP_CONFIG_MSG","msg");
define("CPABC_TDEAPP_CONFIG_WORKINGDATES","workingDates");
define("CPABC_TDEAPP_CONFIG_RESTRICTEDDATES","restrictedDates");
define("CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES0","timeWorkingDates0");
define("CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES1","timeWorkingDates1");
define("CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES2","timeWorkingDates2");
define("CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES3","timeWorkingDates3");
define("CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES4","timeWorkingDates4");
define("CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES5","timeWorkingDates5");
define("CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES6","timeWorkingDates6");
define("CPABC_TDEAPP_CALDELETED_FIELD","caldeleted");

define("CPABC_TDEAPP_CALENDAR_DATA_TABLE",CPABC_APPOINTMENTS_CALENDARS_TABLE_NAME);
define("CPABC_TDEAPP_DATA_ID","id");
define("CPABC_TDEAPP_DATA_IDCALENDAR","appointment_calendar_id");
define("CPABC_TDEAPP_DATA_DATETIME","datatime");
define("CPABC_TDEAPP_DATA_TITLE","title");
define("CPABC_TDEAPP_DATA_DESCRIPTION","description");
// end calendar constants

define('CPABC_TDEAPP_DEFAULT_dexcv_enable_captcha', 'true');
define('CPABC_TDEAPP_DEFAULT_dexcv_width', '180');
define('CPABC_TDEAPP_DEFAULT_dexcv_height', '60');
define('CPABC_TDEAPP_DEFAULT_dexcv_chars', '5');
define('CPABC_TDEAPP_DEFAULT_dexcv_font', 'font-1.ttf');
define('CPABC_TDEAPP_DEFAULT_dexcv_min_font_size', '25');
define('CPABC_TDEAPP_DEFAULT_dexcv_max_font_size', '35');
define('CPABC_TDEAPP_DEFAULT_dexcv_noise', '200');
define('CPABC_TDEAPP_DEFAULT_dexcv_noise_length', '4');
define('CPABC_TDEAPP_DEFAULT_dexcv_background', 'ffffff');
define('CPABC_TDEAPP_DEFAULT_dexcv_border', '000000');
define('CPABC_TDEAPP_DEFAULT_dexcv_text_enter_valid_captcha', 'Please enter a valid captcha code.');




register_activation_hook(CPABC_PLUGIN_FILE,'cpabc_appointments_install');
register_deactivation_hook( CPABC_PLUGIN_FILE, 'cpabc_appointments_remove' );

function cpabc_appointments_install($networkwide)  {
	global $wpdb;

	if (function_exists('is_multisite') && is_multisite()) {
		// check if it is a network activation - if so, run the activation function for each blog id
		if ($networkwide) {
	                $old_blog = $wpdb->blogid;
			// Get all blog ids
			$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
			foreach ($blogids as $blog_id) {
				switch_to_blog($blog_id);
				_cpabc_appointments_install();
			}
			switch_to_blog($old_blog);
			return;
		}
	}
	_cpabc_appointments_install();
}

function _cpabc_appointments_install() {
    global $wpdb;


    $table_name = $wpdb->prefix . CPABC_APPOINTMENTS_TABLE_NAME_NO_PREFIX;

    $sql = "DROP TABLE IF EXISTS".$table_name.";";
    $wpdb->query($sql);
    $sql = "DROP TABLE IF EXISTS".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME.";";
    $wpdb->query($sql);
    $sql = "DROP TABLE IF EXISTS".$wpdb->prefix.CPABC_APPOINTMENTS_CALENDARS_TABLE_NAME.";";
    $wpdb->query($sql);
    $sql = "DROP TABLE IF EXISTS".$wpdb->prefix.CPABC_APPOINTMENTS_CALENDARS_TABLE_NAME.";";
    $wpdb->query($sql);

    $sql = "CREATE TABLE ".$wpdb->prefix.CPABC_APPOINTMENTS_DISCOUNT_CODES_TABLE_NAME_NO_PREFIX." (
         id mediumint(9) NOT NULL AUTO_INCREMENT,
         cal_id mediumint(9) NOT NULL DEFAULT 1,
         code VARCHAR(250) DEFAULT '' NOT NULL,
         discount VARCHAR(250) DEFAULT '' NOT NULL,
         expires datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
         availability int(10) unsigned NOT NULL DEFAULT 0,
         used int(10) unsigned NOT NULL DEFAULT 0,
         UNIQUE KEY id (id)
         );";
    $wpdb->query($sql);

    $sql = "CREATE TABLE $table_name (
         id mediumint(9) NOT NULL AUTO_INCREMENT,
         time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
         booked_time VARCHAR(250) DEFAULT '' NOT NULL,
         booked_time_unformatted VARCHAR(250) DEFAULT '' NOT NULL,
         name VARCHAR(250) DEFAULT '' NOT NULL,
         email VARCHAR(250) DEFAULT '' NOT NULL,
         phone VARCHAR(250) DEFAULT '' NOT NULL,
         question text DEFAULT '' NOT NULL,
         buffered_date text DEFAULT '' NOT NULL,
         UNIQUE KEY id (id)
         );";
    $wpdb->query($sql);
    $sql = "ALTER TABLE  $table_name ADD `calendar` INT NOT NULL AFTER  `id`;";
    $wpdb->query($sql);

    $sql = "CREATE TABLE `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` (`".CPABC_TDEAPP_CONFIG_ID."` int(10) unsigned NOT NULL auto_increment, `".CPABC_TDEAPP_CONFIG_TITLE."` varchar(255) NOT NULL default '',`".CPABC_TDEAPP_CONFIG_USER."` varchar(100) default NULL,`".CPABC_TDEAPP_CONFIG_PASS."` varchar(100) default NULL,`".CPABC_TDEAPP_CONFIG_LANG."` varchar(5) default NULL,`".CPABC_TDEAPP_CONFIG_CPAGES."` tinyint(3) unsigned default NULL,`".CPABC_TDEAPP_CONFIG_TYPE."` tinyint(3) unsigned default NULL,`".CPABC_TDEAPP_CONFIG_MSG."` varchar(255) NOT NULL default '',`".CPABC_TDEAPP_CONFIG_WORKINGDATES."` varchar(255) NOT NULL default '',`".CPABC_TDEAPP_CONFIG_RESTRICTEDDATES."` text default '' NOT NULL,`".CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES0."` text NOT NULL default '',`".CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES1."` text NOT NULL default '',`".CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES2."` text NOT NULL default '',`".CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES3."` text NOT NULL default '',`".CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES4."` text NOT NULL default '',`".CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES5."` text NOT NULL default '',`".CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES6."` text NOT NULL default '',`".CPABC_TDEAPP_CALDELETED_FIELD."` tinyint(3) unsigned default NULL,PRIMARY KEY (`".CPABC_TDEAPP_CONFIG_ID."`)); ";
    $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `conwer` INT NOT NULL AFTER  `id`;";


    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `conwer` INT NOT NULL AFTER  `id`;";     $wpdb->query($sql);

    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `calendar_language` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `calendar_dateformat` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `calendar_pages` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `calendar_militarytime` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `calendar_weekday` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `calendar_mindate` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `calendar_maxdate` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);

    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `enable_paypal` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `paypal_email` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `request_cost` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `paypal_product_name` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `currency` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `url_ok` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `url_cancel` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `paypal_language` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);

    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `notification_from_email` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `notification_destination_email` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `email_subject_confirmation_to_user` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `email_confirmation_to_user` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `email_subject_notification_to_admin` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `email_notification_to_admin` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);

    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `enable_reminder` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `reminder_hours` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `reminder_subject` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `reminder_content` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);


    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `dexcv_enable_captcha` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `dexcv_width` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `dexcv_height` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `dexcv_chars` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `dexcv_min_font_size` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `dexcv_max_font_size` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `dexcv_noise` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `dexcv_noise_length` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `dexcv_background` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `dexcv_border` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `dexcv_font` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);

    $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `cp_cal_checkboxes` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);


    $sql = "CREATE TABLE `".$wpdb->prefix.CPABC_APPOINTMENTS_CALENDARS_TABLE_NAME."` (`".CPABC_TDEAPP_DATA_ID."` int(10) unsigned NOT NULL auto_increment,`".CPABC_TDEAPP_DATA_IDCALENDAR."` int(10) unsigned default NULL,`".CPABC_TDEAPP_DATA_DATETIME."`datetime NOT NULL default '0000-00-00 00:00:00',`".CPABC_TDEAPP_DATA_TITLE."` varchar(250) default NULL,`".CPABC_TDEAPP_DATA_DESCRIPTION."` text,PRIMARY KEY (`".CPABC_TDEAPP_DATA_ID."`)) ;";
    $wpdb->query($sql);
    
    $sql = "ALTER TABLE  `".CPABC_APPOINTMENTS_CALENDARS_TABLE_NAME."` ADD `reminder` VARCHAR(1) DEFAULT '' NOT NULL;"; 
    $wpdb->query($sql);

    $sql = 'INSERT INTO `'.$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME.'` (`'.CPABC_TDEAPP_CONFIG_ID.'`,`'.CPABC_TDEAPP_CONFIG_TITLE.'`,`'.CPABC_TDEAPP_CONFIG_USER.'`,`'.CPABC_TDEAPP_CONFIG_PASS.'`,`'.CPABC_TDEAPP_CONFIG_LANG.'`,`'.CPABC_TDEAPP_CONFIG_CPAGES.'`,`'.CPABC_TDEAPP_CONFIG_TYPE.'`,`'.CPABC_TDEAPP_CONFIG_MSG.'`,`'.CPABC_TDEAPP_CONFIG_WORKINGDATES.'`,`'.CPABC_TDEAPP_CONFIG_RESTRICTEDDATES.'`,`'.CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES0.'`,`'.CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES1.'`,`'.CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES2.'`,`'.CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES3.'`,`'.CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES4.'`,`'.CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES5.'`,`'.CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES6.'`,`'.CPABC_TDEAPP_CALDELETED_FIELD.'`) VALUES("1","cal1","Calendar Item 1","","ENG","1","3","Please, select your appointment.","1,2,3,4,5","","","9:0,10:0,11:0,12:0,13:0,14:0,15:0,16:0","9:0,10:0,11:0,12:0,13:0,14:0,15:0,16:0","9:0,10:0,11:0,12:0,13:0,14:0,15:0,16:0","9:0,10:0,11:0,12:0,13:0,14:0,15:0,16:0","9:0,10:0,11:0,12:0,13:0,14:0,15:0,16:0","","0");';
    $wpdb->query($sql);

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    //dbDelta($sql);

    add_option("cpabc_appointments_data", 'Default', '', 'yes'); // Creates new database field
}

function cpabc_appointments_remove() {
    delete_option('cpabc_appointments_data'); // Deletes the database field
}


/* Filter for placing the maps into the contents */



function cpabc_appointments_filter_content($atts) {
    global $wpdb;
    extract( shortcode_atts( array(
		'calendar' => '',
		'user' => '',
	), $atts ) );
    if ($calendar != '')
        define ('CPABC_CALENDAR_FIXED_ID',$calendar);
    else if ($user != '') 
    {
        $users = $wpdb->get_results( "SELECT user_login,ID FROM ".$wpdb->users." WHERE user_login='".$wpdb->escape($user)."'" );
        if (isset($users[0]))
            define ('CPABC_CALENDAR_USER',$users[0]->ID);
        else
            define ('CPABC_CALENDAR_USER',0);    
    }  
    else
        define ('CPABC_CALENDAR_USER',0);  
    ob_start();
    cpabc_appointments_get_public_form();
    $buffered_contents = ob_get_contents();
    ob_end_clean();
    return $buffered_contents;
}



function cpabc_appointments_get_public_form() {

    global $wpdb;
    
    wp_enqueue_script( 'jquery' );
    
    if (defined('CPABC_CALENDAR_USER') && CPABC_CALENDAR_USER != 0)
        $myrows = $wpdb->get_results( "SELECT * FROM ".CPABC_APPOINTMENTS_CONFIG_TABLE_NAME." WHERE conwer=".CPABC_CALENDAR_USER." AND caldeleted=0" );
    else if (defined('CPABC_CALENDAR_FIXED_ID'))   
        $myrows = $wpdb->get_results( "SELECT * FROM ".CPABC_APPOINTMENTS_CONFIG_TABLE_NAME." WHERE id=".CPABC_CALENDAR_FIXED_ID." AND caldeleted=0" );
    else
        $myrows = $wpdb->get_results( "SELECT * FROM ".CPABC_APPOINTMENTS_CONFIG_TABLE_NAME." WHERE caldeleted=0" );
    
    define ('CP_CALENDAR_ID',$myrows[0]->id);
    
    $calendar_items = '';
    foreach ($myrows as $item)
      $calendar_items .=  '<option value='.$item->id.'>'.$item->uname.'</option>';
      
    $cpabc_buffer = "";
    $services = explode("\n",cpabc_get_option('cp_cal_checkboxes', CPABC_APPOINTMENTS_DEFAULT_CP_CAL_CHECKBOXES));
    foreach ($services as $item)
        if (trim($item) != '')        
            $cpabc_buffer .= '<option value="'.esc_attr(trim($item)).'">'.trim(substr($item,strpos($item,"|")+1)).'</option>';  

    $codes = $wpdb->get_results( 'SELECT * FROM '.CPABC_APPOINTMENTS_DISCOUNT_CODES_TABLE_NAME.' WHERE `cal_id`='.CP_CALENDAR_ID);         
    
    ?>
</p> <!-- this p tag fixes a IE bug -->
<link rel="stylesheet" type="text/css" href="<?php echo plugins_url('TDE_AppCalendar/all-css.css', CPABC_PLUGIN_FILE); ?>" />
<script>
var pathCalendar = "<?php echo cpabc_appointment_get_site_url(); ?>";
var cpabc_global_date_format = '<?php echo cpabc_get_option('calendar_dateformat', CPABC_APPOINTMENTS_DEFAULT_CALENDAR_DATEFORMAT); ?>';
var cpabc_global_military_time = '<?php echo cpabc_get_option('calendar_militarytime', CPABC_APPOINTMENTS_DEFAULT_CALENDAR_MILITARYTIME); ?>';
var cpabc_global_start_weekday = '<?php echo cpabc_get_option('calendar_weekday', CPABC_APPOINTMENTS_DEFAULT_CALENDAR_WEEKDAY); ?>';
var cpabc_global_mindate = '<?php $value = cpabc_get_option('calendar_mindate', CPABC_APPOINTMENTS_DEFAULT_CALENDAR_MINDATE); if ($value != '') echo date("n/j/Y", strtotime($value)); ?>';
var cpabc_global_maxdate = '<?php $value = cpabc_get_option('calendar_maxdate', CPABC_APPOINTMENTS_DEFAULT_CALENDAR_MAXDATE); if ($value != '') echo date("n/j/Y",strtotime($value)); ?>';
</script>
<script type="text/javascript" src="<?php echo plugins_url('TDE_AppCalendar/all-scripts.js', CPABC_PLUGIN_FILE); ?>"></script>
<script type="text/javascript">
 var cpabc_current_calendar_item;
 function cpabc_updateItem()
 {
    document.getElementById("calarea_"+cpabc_current_calendar_item).style.display = "none";
    var i = document.FormEdit.cpabc_item.options.selectedIndex;
    var selecteditem = document.FormEdit.cpabc_item.options[i].value;
    cpabc_do_init(selecteditem);
 }
 function cpabc_do_init(id)
 {
    cpabc_current_calendar_item = id;
    document.getElementById("calarea_"+cpabc_current_calendar_item).style.display = "";
    initAppCalendar("cal"+cpabc_current_calendar_item,<?php echo cpabc_get_option('calendar_pages', CPABC_APPOINTMENTS_DEFAULT_CALENDAR_PAGES); ?>,2,"<?php echo cpabc_get_option('calendar_language', CPABC_APPOINTMENTS_DEFAULT_CALENDAR_LANGUAGE); ?>",{m1:"<?php _e('Please select the appointment time.'); ?>"});
    document.getElementById("selddiv").innerHTML = "";
 } 
 function updatedate()
 {
    if (document.getElementById("selDaycal"+cpabc_current_calendar_item ).value != '')
    {
        var timead = "";
        var hour = document.getElementById("selHourcal"+cpabc_current_calendar_item ).value;
        if (cpabc_global_military_time == '0')
        {
            if (parseInt(hour) > 12)
            {
                timead = " pm";
                hour = parseInt(hour)-12;
            }
            else
                timead = " am";
        }
        var minute = document.getElementById("selMinutecal"+cpabc_current_calendar_item ).value;
        if (minute.length == 1)
            minute = "0"+minute;
        minute = hour + ":" + minute + timead;
        if (cpabc_global_date_format == '1')
            selected_date = document.getElementById("selDaycal"+cpabc_current_calendar_item ).value+"/"
                                                      +document.getElementById("selMonthcal"+cpabc_current_calendar_item ).value+"/"
                                                      +document.getElementById("selYearcal"+cpabc_current_calendar_item ).value+", "                                                      
                                                      +minute;
        else
            selected_date = document.getElementById("selMonthcal"+cpabc_current_calendar_item ).value+"/"
                                                      +document.getElementById("selDaycal"+cpabc_current_calendar_item ).value+"/"
                                                      +document.getElementById("selYearcal"+cpabc_current_calendar_item ).value+", "                                                      
                                                      +minute;
        document.getElementById("selddiv").innerHTML = "<?php echo _e("Selected date"); ?>: "+selected_date;
    }
 }
 </script>
    <?php
    define('CPABC_AUTH_INCLUDE', true);

    $template_file = locate_template('cpabc_scheduler.inc.php', false, false);
    if( '' == $template_file ) {
        include dirname( CPABC_PLUGIN_FILE ) . '/cpabc_scheduler.inc.php';    
    } else {
        include $template_file;
    }
    
}


function cpabc_appointments_show_booking_form($id = "")
{
    if ($id != '')
        define ('CPABC_CALENDAR_FIXED_ID',$id);
    define('CPABC_AUTH_INCLUDE', true);
    cpabc_appointments_get_public_form();
}

/* Code for the admin area */

if ( is_admin() ) {
    add_action('media_buttons', 'set_cpabc_apps_insert_button', 100);
    add_action('admin_enqueue_scripts', 'set_cpabc_apps_insert_adminScripts', 1);
    add_action('admin_menu', 'cpabc_appointments_admin_menu');

    $plugin = plugin_basename(CPABC_PLUGIN_FILE);
    add_filter("plugin_action_links_".$plugin, 'cpabc_settingsLink');
    add_filter("plugin_action_links_".$plugin, 'cpabc_helpLink');

    function cpabc_appointments_admin_menu() {
        add_options_page('Appointment Booking Calendar Options', 'Appointment Booking Calendar', 'manage_options', 'cpabc_appointments', 'cpabc_appointments_html_post_page' );
        add_menu_page( 'Appointment Booking Calendar Options', 'Appointment Booking Calendar', 'edit_pages', 'cpabc_appointments', 'cpabc_appointments_html_post_page' );
    }
}
else
{
    add_shortcode( 'CPABC_APPOINTMENT_CALENDAR', 'cpabc_appointments_filter_content' );    
}

function cpabc_settingsLink($links) {
    $settings_link = '<a href="options-general.php?page=cpabc_appointments">'.__('Settings').'</a>';
	array_unshift($links, $settings_link);
	return $links;
}


function cpabc_helpLink($links) {
    $help_link = '<a href="http://wordpress.dwbooster.com/calendars/appointment-booking-calendar">'.__('Help').'</a>';
	array_unshift($links, $help_link);
	return $links;
}

function cpabc_appointments_html_post_page() {
    if ($_GET["cal"] != '')
    {
        if ($_GET["list"] == '1')
            include_once dirname( CPABC_PLUGIN_FILE ) . '/cpabc_appointments_admin_int_bookings_list.inc.php';
        else
            include_once dirname( CPABC_PLUGIN_FILE ) . '/cpabc_appointments_admin_int.inc.php';
    }
    else
        include_once dirname( CPABC_PLUGIN_FILE ) . '/cpabc_appointments_admin_int_calendar_list.inc.php';
}

function set_cpabc_apps_insert_button() {
    global $wpdb;
    $options = '';
    $calendars = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME_NO_PREFIX);
    foreach($calendars as $item)
        $options .= '<option value="'.$item->id.'">'.$item->uname.'</option>';

    wp_enqueue_style('wp-jquery-ui-dialog');
    wp_enqueue_script('jquery-ui-dialog');
    ?>
    <script type="text/javascript">
      var cpabc_appointments_fpanel = function($){
        var cpabc_counter = 0;
      	function loadWindow(){
      	    cpabc_counter++;
      		$(' <div title="Appointment Booking Calendar"><div style="padding:20px;">'+
      		   'Select Calendar:<br /><select id="cpabc_calendar_sel'+cpabc_counter+'" name="cpabc_calendar_sel'+cpabc_counter+'"><?php echo $options; ?></select>'+
      		   '</div></div>'
      		  ).dialog({
      			dialogClass: 'wp-dialog',
                  modal: true,
                  closeOnEscape: true,
                  buttons: [
                      {text: "Insert", click: function() {
      						if(send_to_editor){
      							var id = $('#cpabc_calendar_sel'+cpabc_counter)[0].options[$('#cpabc_calendar_sel'+cpabc_counter)[0].options.selectedIndex].value;
                                send_to_editor('[CPABC_APPOINTMENT_CALENDAR calendar="'+id+'"]');
      						}
      						$(this).dialog("close");
      				}}
                  ]
              });
      	}
      	var obj = {};
      	obj.open = loadWindow;
      	return obj;
      }(jQuery);
     </script>
    <?php

    print '<a href="javascript:cpabc_appointments_fpanel.open()" title="'.__('Insert Appointment Booking Calendar').'"><img hspace="5" src="'.plugins_url('/images/cpabc_apps.gif', CPABC_PLUGIN_FILE).'" alt="'.__('Insert  Appointment Booking Calendar').'" /></a>';
}

function set_cpabc_apps_insert_adminScripts($hook) {
    if ($_GET["cal"] != '')
    {
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
    }
    if( 'post.php' != $hook  && 'post-new.php' != $hook )
        return;
}

function cpabc_export_iCal() {
    global $wpdb;
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=events".date("Y-M-D_H.i.s").".ics");

    define('CPABC_CAL_TIME_ZONE_MODIFY'," -2 hours");
    define('CPABC_CAL_TIME_SLOT_SIZE'," +15 minutes");

    echo "BEGIN:VCALENDAR\n";
    echo "PRODID:-//CodePeople//Appointment Booking Calendar for WordPress//EN\n";
    echo "VERSION:2.0\n";
    echo "CALSCALE:GREGORIAN\n";
    echo "METHOD:PUBLISH\n";
    echo "X-WR-CALNAME:Bookings\n";
    echo "X-WR-TIMEZONE:Europe/London\n";
    echo "BEGIN:VTIMEZONE\n";
    echo "TZID:Europe/Stockholm\n";
    echo "X-LIC-LOCATION:Europe/London\n";
    echo "BEGIN:DAYLIGHT\n";
    echo "TZOFFSETFROM:+0000\n";
    echo "TZOFFSETTO:+0100\n";
    echo "TZNAME:CEST\n";
    echo "DTSTART:19700329T020000\n";
    echo "RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU\n";
    echo "END:DAYLIGHT\n";
    echo "BEGIN:STANDARD\n";
    echo "TZOFFSETFROM:+0100\n";
    echo "TZOFFSETTO:+0000\n";
    echo "TZNAME:CET\n";
    echo "DTSTART:19701025T030000\n";
    echo "RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU\n";
    echo "END:STANDARD\n";
    echo "END:VTIMEZONE\n";

    $events = $wpdb->get_results( "SELECT * FROM ".CPABC_APPOINTMENTS_CALENDARS_TABLE_NAME." WHERE appointment_calendar_id=".$_GET["id"]." ORDER BY datatime ASC" );
    foreach ($events as $event)
    {
        echo "BEGIN:VEVENT\n";
        echo "DTSTART:".date("Ymd",strtotime($event->datatime.CPABC_CAL_TIME_ZONE_MODIFY))."T".date("His",strtotime($event->datatime.CPABC_CAL_TIME_ZONE_MODIFY))."Z\n";
        echo "DTEND:".date("Ymd",strtotime($event->datatime.CPABC_CAL_TIME_ZONE_MODIFY))."T".date("His",strtotime($event->datatime.CPABC_CAL_TIME_ZONE_MODIFY.CPABC_CAL_TIME_SLOT_SIZE))."Z\n";
        echo "DTSTAMP:".date("Ymd",strtotime($event->datatime.CPABC_CAL_TIME_ZONE_MODIFY))."T".date("His",strtotime($event->datatime.CPABC_CAL_TIME_ZONE_MODIFY))."Z\n";
        echo "UID:uid".$event->id."@".$_SERVER["SERVER_NAME"]."\n";
        echo "CREATED:".date("Ymd",strtotime($event->datatime.CPABC_CAL_TIME_ZONE_MODIFY))."T".date("His",strtotime($event->datatime.CPABC_CAL_TIME_ZONE_MODIFY))."Z\n";
        echo "DESCRIPTION:".str_replace("<br>",'\n',str_replace("<br />",'\n',str_replace("\n",'\n',$event->description)))."\n";
        echo "LAST-MODIFIED:".date("Ymd",strtotime($event->datatime.CPABC_CAL_TIME_ZONE_MODIFY))."T".date("His",strtotime($event->datatime.CPABC_CAL_TIME_ZONE_MODIFY))."Z\n";
        echo "LOCATION:\n";
        echo "SEQUENCE:0\n";
        echo "STATUS:CONFIRMED\n";
        echo "SUMMARY:Booking from ".str_replace("\n",'\n',$event->title)."\n";
        echo "TRANSP:OPAQUE\n";
        echo "END:VEVENT\n";


    }
    echo 'END:VCALENDAR';
    exit;
}


/**
 * Saves appointment booking info into the initial calendars_data table. Once 
 * saved here a user can go on to pay for their appointment if required.
 * 
 * @param  array  $apt_data An associative array of appointment data. This would
 *                          be the $_POST data passed by the booking form. 
 * @return int              The auto_increment ID of this item in the 
 *                          calendars_data table.
 */
function cpabc_tentatively_book_appointment( $apt_data ) {

    // @TODO: Check to see if there's actually an appointment available at this time.


    global $wpdb;

    $selectedCalendar = $apt_data["cpabc_item"];

    $apt_data["dateAndTime"] =  $apt_data["selYearcal" . $selectedCalendar ] . "-" . $apt_data["selMonthcal" . $selectedCalendar ] . "-" . $apt_data["selDaycal" . $selectedCalendar ] . " " . $apt_data["selHourcal" . $selectedCalendar ] . ":" . $apt_data["selMinutecal" . $selectedCalendar ];

    $military_time = cpabc_get_option( 'calendar_militarytime', CPABC_APPOINTMENTS_DEFAULT_CALENDAR_MILITARYTIME );
    if ( cpabc_get_option( 'calendar_militarytime', CPABC_APPOINTMENTS_DEFAULT_CALENDAR_MILITARYTIME ) == '0' ) {
        $format = "g:i A"; 
    } else {
        $format = "H:i";
    }
    if ( cpabc_get_option( 'calendar_dateformat', CPABC_APPOINTMENTS_DEFAULT_CALENDAR_DATEFORMAT ) == '0' ) {
        $format = "m/d/Y ".$format;
    } else {
        $format = "d/m/Y ".$format;  
    } 

    $apt_data["Date"] = date( $format, strtotime( $apt_data["dateAndTime"] ) );

    $services_formatted = explode( "|", $apt_data["cpabc_services"] );

    $price = ( $apt_data["cpabc_services"] ? trim( $services_formatted[0] ): cpabc_get_option( 'request_cost', CPABC_APPOINTMENTS_DEFAULT_COST ) );

    $discount_note = "";
    $coupon = false;
    $codes = $wpdb->get_results( 
        $wpdb->prepare(
            'SELECT * FROM  ' . CPABC_APPOINTMENTS_DISCOUNT_CODES_TABLE_NAME . ' WHERE code = %s AND expires >= %s AND cal_id = %d',
            $apt_data['cpabc_couponcode'],
            date('Y-m-d') . ' 00:00:00',
            CP_CALENDAR_ID
        )
    );

    if ( count( $codes ) ) {
        $coupon = $codes[0];
        $price = number_format( floatval( $price ) - $price * $coupon->discount / 100, 2 );
        $discount_note = " (" . $coupon->discount . "% discount applied)";
    }


    $buffer = $apt_data["selYearcal".$selectedCalendar].",".$apt_data["selMonthcal".$selectedCalendar].",".$apt_data["selDaycal".$selectedCalendar]."\n".
    $apt_data["selHourcal".$selectedCalendar].":".( $apt_data["selMinutecal".$selectedCalendar] <10 ? "0" : "").$apt_data["selMinutecal".$selectedCalendar]."\n".
    "Name: ".$apt_data["cpabc_name"]."\n".
    "Email: ".$apt_data["cpabc_email"]."\n".
    "Phone: ".$apt_data["cpabc_phone"]."\n".
    "Question: ".$apt_data["cpabc_question"]."\n".
            ($apt_data["cpabc_services"]?"\nService:".trim($services_formatted[1])."\n":"").
            ($coupon?"\nCoupon code:".$coupon->code.$discount_note."\n":"").
    "*-*\n";

    $buffer = apply_filters('cpabc_appointment_buffered_data', $buffer);


    if( isset( $apt_data['id'] ) && ! empty( $apt_data['id'] ) ) {
        $rows_affected = $wpdb->update(
            CPABC_APPOINTMENTS_TABLE_NAME,
            array( 
                'time' => current_time('mysql'),
                'booked_time' => $apt_data["Date"],
                'booked_time_unformatted' => $apt_data["dateAndTime"],
                'name' => $apt_data["cpabc_name"],
                'email' => $apt_data["cpabc_email"],
                'phone' => $apt_data["cpabc_phone"],
                'question' => $apt_data["cpabc_question"]
                   .($apt_data["cpabc_services"]?"\nService:".trim($services_formatted[1]):"")
                   .($coupon?"\nCoupon code:".$coupon->code.$discount_note:"")
                   ,
                'buffered_date' => $buffer
             ),

            // where
            array(
                'id' => $apt_data['id']
            ),
            // formats
            array(
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s'
            ),

            // where format
            array(
                '%d'
            )

        );

        $item_number = $apt_data['id'];

    } else {
        $rows_affected = $wpdb->insert( CPABC_APPOINTMENTS_TABLE_NAME, array( 'calendar' => $selectedCalendar,
            'time' => current_time('mysql'),
            'booked_time' => $apt_data["Date"],
            'booked_time_unformatted' => $apt_data["dateAndTime"],
            'name' => $apt_data["cpabc_name"],
            'email' => $apt_data["cpabc_email"],
            'phone' => $apt_data["cpabc_phone"],
            'question' => $apt_data["cpabc_question"]
               .($apt_data["cpabc_services"]?"\nService:".trim($services_formatted[1]):"")
               .($coupon?"\nCoupon code:".$coupon->code.$discount_note:"")
               ,
            'buffered_date' => $buffer
             )
        );

        if( $rows_affected ) {
             $item_number = $wpdb->insert_id;
        }
    }

    

    if ( ! $rows_affected ) {
        // @TODO: There's got to be a better way of handling an error
        echo 'Error saving data! Please try again.';
        echo '<br /><br />Error debug information: '.mysql_error();

        // @TODO: WTF Is this query for?
        $sql = "ALTER TABLE  `".$wpdb->prefix.CPABC_APPOINTMENTS_TABLE_NAME_NO_PREFIX."` ADD `booked_time_unformatted` text DEFAULT '' NOT NULL;";
        $wpdb->query($sql);
        exit;
    }

    return $item_number;
}


function cpabc_create_appointment( $apt_data ) {

    $calendar_id = $apt_data['calendar'];

    $raw_appointment = array(
        'cpabc_item'                  => $calendar_id,
        'selYearcal' . $calendar_id   => $apt_data['datetimeobj']->format( 'Y' ),
        'selMonthcal' . $calendar_id  => $apt_data['datetimeobj']->format( 'n' ),
        'selDaycal' . $calendar_id    => $apt_data['datetimeobj']->format( 'j' ),
        'selHourcal' . $calendar_id   => $apt_data['datetimeobj']->format( 'G' ),
        'selMinutecal' . $calendar_id => intval($apt_data['datetimeobj']->format( 'i' )),
        'cpabc_couponcode'            => '',
        'cpabc_services'              => '',
        'cpabc_name'                  => '',
        'cpabc_email'                 => $apt_data['title'],    // yeah, bizarre, but it ends up as the appointment "title"
        'cpabc_phone'                 => '',
        'cpabc_question'              => $apt_data['description'],
    );

    $appointment_id = cpabc_tentatively_book_appointment( $raw_appointment );
    cpabc_process_ready_to_go_appointment( $appointment_id );

    return $appointment_id;
}


function cpabc_edit_appointment( $apt_data ) {

    /// date, time, subject/title, description are the only things provided, so I should be able to edit those.

    // I have to figure out how to format the buffered data...?
    
    

    // run everything through the appointment table.
    $raw_appointment = array(
        'selYearcal' . $calendar_id   => $apt_data['datetimeobj']->format( 'Y' ),
        'selMonthcal' . $calendar_id  => $apt_data['datetimeobj']->format( 'n' ),
        'selDaycal' . $calendar_id    => $apt_data['datetimeobj']->format( 'j' ),
        'selHourcal' . $calendar_id   => $apt_data['datetimeobj']->format( 'G' ),
        'selMinutecal' . $calendar_id => intval( $apt_data['datetimeobj']->format( 'i' ) ),
        'cpabc_couponcode'            => '',
        'cpabc_services'              => '',
        'cpabc_name'                  => '',
        'cpabc_email'                 => $apt_data['title'],    // yeah, bizarre, but it ends up as the appointment "title"
        'cpabc_phone'                 => '',
        'cpabc_question'              => $apt_data['description'],
        'id'                          => $apt_data['id'],

    );

    $appointment_id = cpabc_tentatively_book_appointment( $raw_appointment );




    // update data table
    $wpdb->update(
        CPABC_TDEAPP_CALENDAR_DATA_TABLE,
        array(
            CPABC_TDEAPP_DATA_DATETIME => $apt_data['datetime'], 
            CPABC_TDEAPP_DATA_TITLE => $apt_data['title'], 
            CPABC_TDEAPP_DATA_DESCRIPTION => $apt_data['description']
        ),

        array(
            CPABC_TDEAPP_DATA_ID => $apt_data['id']
        ),

        array(
            '%s',
            '%s',
            '%s'
        ),

        array(
            '%d',
        )
    );

}

function cpabc_delete_appointment($appointment_id) {

    global $wpdb;

    $wpdb->query( $wpdb->prepare( "DELETE FROM ".CPABC_TDEAPP_CALENDAR_DATA_TABLE." WHERE ".CPABC_TDEAPP_DATA_ID."=%d", $appointment_id ) );
    $wpdb->query( $wpdb->prepare( "DELETE FROM ".CPABC_APPOINTMENTS_TABLE_NAME." WHERE id=%d", $appointment_id ) );

}


/* hook for checking posted data for the admin area */
add_action( 'init', 'cpabc_appointments_check_posted_data', 11 );

function cpabc_appointments_check_posted_data()
{
    global $wpdb;

    cpabc_appointments_check_reminders();

    if(isset($_GET) && array_key_exists('cpabc_app',$_GET)) {
        if ( $_GET["cpabc_app"] == 'calfeed')
            cpabc_export_iCal();
            
        if ($_GET["cpabc_app"] == 'captcha')
        {
            @include_once dirname( CPABC_PLUGIN_FILE ) . '/captcha/captcha.php';            
            exit;
        }

        if ( $_GET["cpabc_app"] == 'cpabc_loadcoupons')
            cpabc_appointments_load_discount_codes();
    }

    if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset( $_POST['cpabc_appointments_post_options'] ) && is_admin() )
    {
        cpabc_appointments_save_options();
        return;
    }

    // if this isn't the expected post and isn't the captcha verification then nothing to do
	if ( 'POST' != $_SERVER['REQUEST_METHOD'] || ! isset( $_POST['cpabc_appointments_post'] ) )
		if ( 'GET' != $_SERVER['REQUEST_METHOD'] || !isset( $_GET['cpabc_hdcaptcha'] ) )
		    return;

    if (!defined('CP_CALENDAR_ID'))
        define ('CP_CALENDAR_ID',$_POST["cpabc_item"]);

    session_start();
    if ($_GET['cpabc_hdcaptcha'] == '') $_GET['cpabc_hdcaptcha'] = $_POST['cpabc_hdcaptcha'];
    if (
           (cpabc_get_option('dexcv_enable_captcha', CPABC_TDEAPP_DEFAULT_dexcv_enable_captcha) != 'false') &&
           ( ($_GET['cpabc_hdcaptcha'] != $_SESSION['rand_code']) ||
             ($_SESSION['rand_code'] == '')
           )
       )
    {
        $_SESSION['rand_code'] = '';
        echo 'captchafailed';
        exit;
    }

	// if this isn't the real post (it was the captcha verification) then echo ok and exit
    if ( 'POST' != $_SERVER['REQUEST_METHOD'] || ! isset( $_POST['cpabc_appointments_post'] ) )
	{
	    echo 'ok';
        exit;
	}

    $_SESSION['rand_code'] = '';

    $item_number = cpabc_tentatively_book_appointment( $_POST );

    if ( floatval( $price ) > 0 && cpabc_get_option( 'enable_paypal', CPABC_APPOINTMENTS_DEFAULT_ENABLE_PAYPAL ) )
    {
?>
<html>
<head><title>Redirecting to Paypal...</title></head>
<body>
<form action="https://www.paypal.com/cgi-bin/webscr" name="ppform3" method="post">
<input type="hidden" name="cmd" value="_xclick" />
<input type="hidden" name="business" value="<?php echo cpabc_get_option('paypal_email', CPABC_APPOINTMENTS_DEFAULT_PAYPAL_EMAIL); ?>" />
<input type="hidden" name="item_name" value="<?php echo cpabc_get_option('paypal_product_name', CPABC_APPOINTMENTS_DEFAULT_PRODUCT_NAME).($_POST["cpabc_services"]?": ".trim($services_formatted[1]):"").$discount_note; ?>" />
<input type="hidden" name="item_number" value="<?php echo $item_number; ?>" />
<input type="hidden" name="amount" value="<?php echo $price; ?>" />
<input type="hidden" name="page_style" value="Primary" />
<input type="hidden" name="no_shipping" value="1" />
<input type="hidden" name="return" value="<?php echo cpabc_get_option('url_ok', CPABC_APPOINTMENTS_DEFAULT_OK_URL); ?>">
<input type="hidden" name="cancel_return" value="<?php echo cpabc_get_option('url_cancel', CPABC_APPOINTMENTS_DEFAULT_CANCEL_URL); ?>" />
<input type="hidden" name="no_note" value="1" />
<input type="hidden" name="currency_code" value="<?php echo strtoupper(cpabc_get_option('currency', CPABC_APPOINTMENTS_DEFAULT_CURRENCY)); ?>" />
<input type="hidden" name="lc" value="<?php echo cpabc_get_option('paypal_language', CPABC_APPOINTMENTS_DEFAULT_PAYPAL_LANGUAGE); ?>" />
<input type="hidden" name="bn" value="PP-BuyNowBF" />
<input type="hidden" name="notify_url" value="<?php echo cpabc_appointment_get_FULL_site_url(); ?>/?cpabc_ipncheck=1&itemnumber=<?php echo $item_number; ?>" />
<input type="hidden" name="ipn_test" value="1" />
<input class="pbutton" type="hidden" value="Buy Now" /></div>
</form>
<script type="text/javascript">
document.ppform3.submit();
</script>
</body>
</html>
<?php
        exit();
    }
    else
    {
        cpabc_process_ready_to_go_appointment( $item_number );

        header( "Location: ".cpabc_get_option( 'url_ok', CPABC_APPOINTMENTS_DEFAULT_OK_URL ) );
        exit;
    }
}


function cpabc_appointments_load_discount_codes() {
    global $wpdb;

    if ( ! current_user_can('edit_pages') ) // prevent loading coupons from outside admin area
    {
        echo 'No enough privilegies to load this content.';
        exit;
    }

    if (!defined('CP_CALENDAR_ID'))
        define ('CP_CALENDAR_ID',$_GET["cpabc_item"]);

    if ($_GET["add"] == "1")
        $wpdb->insert( CPABC_APPOINTMENTS_DISCOUNT_CODES_TABLE_NAME, array('cal_id' => CP_CALENDAR_ID,
                                                                         'code' => $_GET["code"],
                                                                         'discount' => $_GET["discount"],
                                                                         'expires' => $_GET["expires"],
                                                                         ));
    if ($_GET["delete"] == "1")
        $wpdb->query( $wpdb->prepare( "DELETE FROM ".CPABC_APPOINTMENTS_DISCOUNT_CODES_TABLE_NAME." WHERE id = %d", $_GET["code"] ));

    $codes = $wpdb->get_results( 'SELECT * FROM '.CPABC_APPOINTMENTS_DISCOUNT_CODES_TABLE_NAME.' WHERE `cal_id`='.CP_CALENDAR_ID);
    if (count ($codes))
    {
        echo '<table>';
        echo '<tr>';
        echo '  <th style="padding:2px;background-color: #cccccc;font-weight:bold;">Cupon Code</th>';
        echo '  <th style="padding:2px;background-color: #cccccc;font-weight:bold;">Discount %</th>';
        echo '  <th style="padding:2px;background-color: #cccccc;font-weight:bold;">Valid until</th>';
        echo '  <th style="padding:2px;background-color: #cccccc;font-weight:bold;">Options</th>';
        echo '</tr>';
        foreach ($codes as $value)
        {
           echo '<tr>';
           echo '<td>'.$value->code.'</td>';
           echo '<td>'.$value->discount.'</td>';
           echo '<td>'.substr($value->expires,0,10).'</td>';
           echo '<td>[<a href="javascript:cpabc_delete_coupon('.$value->id.')">Delete</a>]</td>';
           echo '</tr>';
        }
        echo '</table>';
    }
    else
        echo 'No discount codes listed for this calendar yet.';
    exit;
}

add_action( 'init', 'cpabc_appointments_check_IPN_verification', 11 );

function cpabc_appointments_check_IPN_verification() {

    global $wpdb;

	if ( ! isset( $_GET['cpabc_ipncheck'] ) || $_GET['cpabc_ipncheck'] != '1' ||  ! isset( $_GET["itemnumber"] ) )
		return;

    $item_name = $_POST['item_name'];
    $item_number = $_POST['item_number'];
    $payment_status = $_POST['payment_status'];
    $payment_amount = $_POST['mc_gross'];
    $payment_currency = $_POST['mc_currency'];
    $txn_id = $_POST['txn_id'];
    $receiver_email = $_POST['receiver_email'];
    $payer_email = $_POST['payer_email'];
    $payment_type = $_POST['payment_type'];


	if ($payment_status != 'Completed' && $payment_type != 'echeck')
	    return;

	if ($payment_type == 'echeck' && $payment_status == 'Completed')
	    return;

    cpabc_process_ready_to_go_appointment($_GET["itemnumber"], $payer_email);

    echo 'OK';

    exit();

}

function cpabc_process_ready_to_go_appointment($itemnumber, $payer_email = "")
{
   global $wpdb;

   $myrows = $wpdb->get_results( "SELECT * FROM ".CPABC_APPOINTMENTS_TABLE_NAME." WHERE id=".$itemnumber );

   $mycalendarrows = $wpdb->get_results( 'SELECT * FROM '.CPABC_APPOINTMENTS_CONFIG_TABLE_NAME .' WHERE `'.CPABC_TDEAPP_CONFIG_ID.'`='.$myrows[0]->calendar);

   $reminder_timeline = date( "Y-m-d H:i:s", strtotime (date("Y-m-d H:i:s")." +".$mycalendarrows[0]->reminder_hours." hours") );

   if (!defined('CP_CALENDAR_ID'))
        define ('CP_CALENDAR_ID',$myrows[0]->calendar);

   $SYSTEM_EMAIL = cpabc_get_option('notification_from_email', CPABC_APPOINTMENTS_DEFAULT_PAYPAL_EMAIL);
   $SYSTEM_RCPT_EMAIL = cpabc_get_option('notification_destination_email', CPABC_APPOINTMENTS_DEFAULT_PAYPAL_EMAIL);


   $email_subject1 = cpabc_get_option('email_subject_confirmation_to_user', CPABC_APPOINTMENTS_DEFAULT_SUBJECT_CONFIRMATION_EMAIL);
   $email_content1 = cpabc_get_option('email_confirmation_to_user', CPABC_APPOINTMENTS_DEFAULT_CONFIRMATION_EMAIL);
   $email_subject2 = cpabc_get_option('email_subject_notification_to_admin', CPABC_APPOINTMENTS_DEFAULT_SUBJECT_NOTIFICATION_EMAIL);
   $email_content2 = cpabc_get_option('email_notification_to_admin', CPABC_APPOINTMENTS_DEFAULT_NOTIFICATION_EMAIL);

   $information = $mycalendarrows[0]->uname."\n".
                  $myrows[0]->booked_time."\n".
                  $myrows[0]->name."\n".
                  $myrows[0]->email."\n".
                  $myrows[0]->phone."\n".
                  $myrows[0]->question."\n";

   $information = apply_filters('cpabc_appointment_description', $information, $myrows[0]);

   $email_content1 = str_replace("%INFORMATION%", $information, $email_content1);
   $email_content2 = str_replace("%INFORMATION%", $information, $email_content2);

   // SEND EMAIL TO USER
   wp_mail($myrows[0]->email, $email_subject1, $email_content1,
            "From: \"$SYSTEM_EMAIL\" <".$SYSTEM_EMAIL.">\r\n".
            "Content-Type: text/plain; charset=utf-8\n".
            "X-Mailer: PHP/" . phpversion());

   if ($payer_email && $payer_email != $myrows[0]->email)
       wp_mail($payer_email , $email_subject1, $email_content1,
                "From: \"$SYSTEM_EMAIL\" <".$SYSTEM_EMAIL.">\r\n".
                "Content-Type: text/plain; charset=utf-8\n".
                "X-Mailer: PHP/" . phpversion());

   // SEND EMAIL TO ADMIN
   $to = explode(",",$SYSTEM_RCPT_EMAIL);
   foreach ($to as $item)
        if (trim($item) != '')
        {
            wp_mail(trim($item), $email_subject2, $email_content2,
                "From: \"$SYSTEM_EMAIL\" <".$SYSTEM_EMAIL.">\r\n".
                "Content-Type: text/plain; charset=utf-8\n".
                "X-Mailer: PHP/" . phpversion());
        }
        
    if ($reminder_timeline > date("Y-m-d H:i:s", strtotime($myrows[0]->booked_time_unformatted))) {
       $reminder = '1';
    } else {
       $reminder = '';
    }
    
    $rows_affected = $wpdb->insert( CPABC_TDEAPP_CALENDAR_DATA_TABLE, array(
            'id' => $myrows[0]->id,
            'appointment_calendar_id' => $myrows[0]->calendar,
            'datatime' => date("Y-m-d H:i:s", strtotime($myrows[0]->booked_time_unformatted)),
            'title' => $myrows[0]->email,
            'reminder' => $reminder,
            'description' => str_replace("\n","<br />", $information)
        )
    );

}

function cpabc_appointments_save_options()
{
    global $wpdb;
    if (!defined('CP_CALENDAR_ID'))
        define ('CP_CALENDAR_ID',$_POST["cpabc_item"]);

    // temporal solution to guarantee migration:
    $sql = "ALTER TABLE  `".CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `enable_reminder` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `reminder_hours` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `reminder_subject` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);
    $sql = "ALTER TABLE  `".CPABC_APPOINTMENTS_CONFIG_TABLE_NAME."` ADD `reminder_content` text DEFAULT '' NOT NULL AFTER  `timeWorkingDates6`;"; $wpdb->query($sql);

    $sql = "ALTER TABLE  `".CPABC_TDEAPP_CALENDAR_DATA_TABLE."` ADD `reminder` VARCHAR(1) DEFAULT '' NOT NULL;"; $wpdb->query($sql);

    $data = array(
         'calendar_language' => $_POST["calendar_language"],
         'calendar_dateformat' => $_POST["calendar_dateformat"],
         'calendar_pages' => $_POST["calendar_pages"],
         'calendar_militarytime' => $_POST["calendar_militarytime"],
         'calendar_weekday' => $_POST["calendar_weekday"],
         'calendar_mindate' => $_POST["calendar_mindate"],
         'calendar_maxdate' => $_POST["calendar_maxdate"],

         'enable_paypal' => $_POST["enable_paypal"],
         'paypal_email' => $_POST["paypal_email"],
         'request_cost' => $_POST["request_cost"],
         'paypal_product_name' => $_POST["paypal_product_name"],
         'currency' => $_POST["currency"],
         'url_ok' => $_POST["url_ok"],
         'url_cancel' => $_POST["url_cancel"],
         'paypal_language' => $_POST["paypal_language"],

         'notification_from_email' => $_POST["notification_from_email"],
         'notification_destination_email' => $_POST["notification_destination_email"],
         'email_subject_confirmation_to_user' => $_POST["email_subject_confirmation_to_user"],
         'email_confirmation_to_user' => $_POST["email_confirmation_to_user"],
         'email_subject_notification_to_admin' => $_POST["email_subject_notification_to_admin"],
         'email_notification_to_admin' => $_POST["email_notification_to_admin"],

         'enable_reminder' => $_POST["enable_reminder"],
         'reminder_hours' => $_POST["reminder_hours"],
         'reminder_subject' => $_POST["reminder_subject"],
         'reminder_content' => $_POST["reminder_content"],

         'dexcv_enable_captcha' => $_POST["dexcv_enable_captcha"],
         'dexcv_width' => $_POST["dexcv_width"],
         'dexcv_height' => $_POST["dexcv_height"],
         'dexcv_chars' => $_POST["dexcv_chars"],
         'dexcv_min_font_size' => $_POST["dexcv_min_font_size"],
         'dexcv_max_font_size' => $_POST["dexcv_max_font_size"],
         'dexcv_noise' => $_POST["dexcv_noise"],
         'dexcv_noise_length' => $_POST["dexcv_noise_length"],
         'dexcv_background' => $_POST["dexcv_background"],
         'dexcv_border' => $_POST["dexcv_border"],
         'dexcv_font' => $_POST["dexcv_font"],
         'cp_cal_checkboxes' => $_POST["cp_cal_checkboxes"]
	);
    $wpdb->update ( CPABC_APPOINTMENTS_CONFIG_TABLE_NAME, $data, array( 'id' => CP_CALENDAR_ID ));
}

function cpabc_appointments_check_reminders() {
    global $wpdb;
    $query = "SELECT notification_from_email,reminder_subject,reminder_content,".CPABC_TDEAPP_CALENDAR_DATA_TABLE.".* FROM ".
              CPABC_TDEAPP_CALENDAR_DATA_TABLE." INNER JOIN ".CPABC_APPOINTMENTS_CONFIG_TABLE_NAME." ON ".CPABC_TDEAPP_CALENDAR_DATA_TABLE.".appointment_calendar_id=".CPABC_APPOINTMENTS_CONFIG_TABLE_NAME.".id ".
              " WHERE enable_reminder=1 AND reminder<>'1' AND datatime<DATE_ADD(now(),INTERVAL reminder_hours HOUR) AND datatime>'".date("Y-m-d H:i:s")."'";
    $apps = $wpdb->get_results( $query);
    foreach ($apps as $app) {
        // send email
        $email_content = str_replace('%INFORMATION%',str_replace('<br />',"\n",$app->description),$app->reminder_content);
        wp_mail($app->title, $app->reminder_subject, $email_content,
                "From: \"".$app->notification_from_email."\" <".$app->notification_from_email.">\r\n".
                "Content-Type: text/plain; charset=utf-8\n".
                "X-Mailer: PHP/" . phpversion());
        // mark as sent
        $wpdb->query("UPDATE ".CPABC_TDEAPP_CALENDAR_DATA_TABLE." SET reminder='1' WHERE id=".$app->id);
    }
}


add_action( 'init', 'cpabc_appointments_calendar_load', 11 );
add_action( 'init', 'cpabc_appointments_calendar_load2', 11 );
add_action( 'init', 'cpabc_appointments_calendar_update', 11 );
add_action( 'init', 'cpabc_appointments_calendar_update2', 11 );

function cpabc_appointments_calendar_load() {
    global $wpdb;
	if ( ! isset( $_GET['cpabc_calendar_load'] ) || $_GET['cpabc_calendar_load'] != '1' )
		return;
    ob_end_clean();
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Pragma: no-cache");
    $calid = str_replace  (CPABC_TDEAPP_CAL_PREFIX, "",$_GET["id"]);
    $query = "SELECT * FROM ".CPABC_TDEAPP_CONFIG." where ".CPABC_TDEAPP_CONFIG_ID."='".$calid."'";
    $row = $wpdb->get_results($query,ARRAY_A);
    if ($row[0])
    {
        echo $row[0][CPABC_TDEAPP_CONFIG_WORKINGDATES].";";
        echo $row[0][CPABC_TDEAPP_CONFIG_RESTRICTEDDATES].";";
        echo $row[0][CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES0].";";
        echo $row[0][CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES1].";";
        echo $row[0][CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES2].";";
        echo $row[0][CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES3].";";
        echo $row[0][CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES4].";";
        echo $row[0][CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES5].";";
        echo $row[0][CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES6].";";
    }

    exit();
}

function cpabc_appointments_calendar_load2() {
    global $wpdb;
	if ( ! isset( $_GET['cpabc_calendar_load2'] ) || $_GET['cpabc_calendar_load2'] != '1' )
		return;
    ob_end_clean();
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Pragma: no-cache");

    // @TODO: Only send future events, otherwise this XHR will get huge eventually.
    // @TODO: (Long-Term): Don't send a list of events, send a list of when things are available or not.
    // @TODO: (Long-Term): Can the HTML5 date/time picker be used? Is it possible to block off dates/times in it?

    $calid = str_replace  (CPABC_TDEAPP_CAL_PREFIX, "",$_GET["id"]);
    $query = "SELECT * FROM ".CPABC_TDEAPP_CALENDAR_DATA_TABLE." where ".CPABC_TDEAPP_DATA_IDCALENDAR."='".$calid."'";
    $row_array = $wpdb->get_results($query,ARRAY_A);
    foreach ($row_array as $row)
    {
        echo $row[CPABC_TDEAPP_DATA_ID]."\n";
        $dn =  explode(" ", $row[CPABC_TDEAPP_DATA_DATETIME]);
        $d1 =  explode("-", $dn[0]);
        $d2 =  explode(":", $dn[1]);

        echo intval($d1[0]).",".intval($d1[1]).",".intval($d1[2])."\n";
        echo intval($d2[0]).":".($d2[1])."\n";

        // DO NOT send private user data publicly, even if this IS an XHR.
        if( current_user_can( 'manage_options' ) ) {
            echo $row[CPABC_TDEAPP_DATA_TITLE]."\n";
            echo $row[CPABC_TDEAPP_DATA_DESCRIPTION];
        }

        echo "\n*-*\n";
    }

    exit();
}

function cpabc_appointments_calendar_update() {
    global $wpdb, $user_ID;

    if ( ! current_user_can('edit_pages') )
        return;

	if ( ! isset( $_GET['cpabc_calendar_update'] ) || $_GET['cpabc_calendar_update'] != '1' )
		return;
    ob_end_clean();
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Pragma: no-cache");
    if ( $user_ID )
    {
        $calid = str_replace  (CPABC_TDEAPP_CAL_PREFIX, "",$_GET["id"]);
        $wpdb->query("update  ".CPABC_TDEAPP_CONFIG." set ".CPABC_TDEAPP_CONFIG_WORKINGDATES."='".$_POST["workingDates"]."',".CPABC_TDEAPP_CONFIG_RESTRICTEDDATES."='".$_POST["restrictedDates"]."',".CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES0."='".$_POST["timeWorkingDates0"]."',".CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES1."='".$_POST["timeWorkingDates1"]."',".CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES2."='".$_POST["timeWorkingDates2"]."',".CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES3."='".$_POST["timeWorkingDates3"]."',".CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES4."='".$_POST["timeWorkingDates4"]."',".CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES5."='".$_POST["timeWorkingDates5"]."',".CPABC_TDEAPP_CONFIG_TIMEWORKINGDATES6."='".$_POST["timeWorkingDates6"]."'  where ".CPABC_TDEAPP_CONFIG_ID."=".$calid);
    }

    exit();
}

function cpabc_appointments_calendar_update2() {
    global $wpdb, $user_ID;

    if ( ! current_user_can('edit_pages') )
        return;

	if ( ! isset( $_GET['cpabc_calendar_update2'] ) || $_GET['cpabc_calendar_update2'] != '1' )
		return;
    ob_end_clean();
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Pragma: no-cache");
    if ( $user_ID )
    {
        if ( 'del' == $_GET["act"] )
        {
            $calid = str_replace  ( CPABC_TDEAPP_CAL_PREFIX, "", $_GET["id"] );

            cpabc_delete_appointment( $_POST["sqlId"] );

        } else if ($_GET["act"]=='edit') {
            $calid = str_replace  (CPABC_TDEAPP_CAL_PREFIX, "",$_GET["id"]);
            $data = explode("\n", $_POST["appoiments"]);
            $d1 =  explode(",", $data[0]);
            $d2 =  explode(":", $data[1]);
	        $datetime = $d1[0]."-".$d1[1]."-".$d1[2]." ".$d2[0].":".$d2[1];
	        $title = $data[2];
            $description = "";
            for ($j=3;$j<count($data);$j++)
            {
                $description .= $data[$j];
                if ($j!=count($data)-1)
                    $description .= "\n";
            }

            $start = new DateTime();
            $start->setTimezone( new DateTimeZone( get_option( 'timezone_string' ) ) );
            $start->setDate( $d1[0], $d1[1], $d1[2] );
            $start->setTime( $d2[0], $d2[1] );

            $apt_data = array(
                'id' => $_POST["sqlId"],
                'datetime' => $datetime,
                'datetimeobj' => $start,
                'title' => $title,
                'description' => $description
            );

            cpabc_edit_appointment( $apt_data );

            //$wpdb->query("update  ".CPABC_TDEAPP_CALENDAR_DATA_TABLE." set ".CPABC_TDEAPP_DATA_DATETIME."='".$datetime."',".CPABC_TDEAPP_DATA_TITLE."='".$wpdb->escape($title)."',".CPABC_TDEAPP_DATA_DESCRIPTION."='".$wpdb->escape($description)."'  where ".CPABC_TDEAPP_DATA_IDCALENDAR."=".$calid." and ".CPABC_TDEAPP_DATA_ID."=".$_POST["sqlId"]);
        } else if ($_GET["act"]=='add') {
            $calid = str_replace  (CPABC_TDEAPP_CAL_PREFIX, "",$_GET["id"]);
            $data = explode("\n", $_POST["appoiments"]);
            $d1 =  explode(",", $data[0]);
            $d2 =  explode(":", $data[1]);
	        $datetime = $d1[0]."-".$d1[1]."-".$d1[2]." ".$d2[0].":".$d2[1];

            $start = new DateTime();
            $start->setTimezone( new DateTimeZone( get_option( 'timezone_string' ) ) );
            $start->setDate( $d1[0], $d1[1], $d1[2] );
            $start->setTime( $d2[0], $d2[1] );

	        $title = $data[2];
            $description = "";
            for ($j=3;$j<count($data);$j++)
            {
                $description .= $data[$j];
                if ($j!=count($data)-1)
                    $description .= "\n";
            }


             $apt_data = array(
                'calendar' => $calid,
                'datetime' => $datetime,
                'datetimeobj' => $start,
                'title' => $title,
                'description' => $description
            );

            $appointment_id = cpabc_create_appointment( $apt_data );

            echo  $appointment_id;

        }
    }

    exit();
}


function cpabc_appointment_get_site_url()
{
    $url = rtrim( parse_url( get_site_url(), PHP_URL_PATH ), '/' );
    return $url;
}


function cpabc_appointment_get_FULL_site_url()
{
    $url = cpabc_appointment_get_site_url();
    $pos = strpos( $url, '://' );
    if ( false === $pos )
        $url = 'http://' . $_SERVER["HTTP_HOST"] . $url;
    return $url;
}


// cpabc_cpabc_get_option:
$cpabc_option_buffered_item = false;
$cpabc_option_buffered_id = -1;

function cpabc_get_option ($field, $default_value = NULL)
{
    global $wpdb, $cpabc_option_buffered_item, $cpabc_option_buffered_id;
    if ( CP_CALENDAR_ID == $cpabc_option_buffered_id ) {
        $value = $cpabc_option_buffered_item->$field;
    } else {
       $myrows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".CPABC_APPOINTMENTS_CONFIG_TABLE_NAME." WHERE id=%d", CP_CALENDAR_ID ) );
       $value = $myrows[0]->$field;
       $cpabc_option_buffered_item = $myrows[0];
       $cpabc_option_buffered_id  = CP_CALENDAR_ID;
    }
    if ($value == '' && $cpabc_option_buffered_item->calendar_language == '')
        $value = $default_value;
    return $value;
}

function cpabc_appointment_is_administrator()
{
    return current_user_can('manage_options');
}



/**
 * Gets the availability on the date specified by $date
 * @param  int $timestamp    A unix timestamp on the date you're interested in.
 * @return array        An associative array of appointment times and how many 
 *                      slots are available at that time.
 *
 *                      eg. array ( 8:10 => 2, 11:30 => 1, 14:5 => 3 )
 *                      Note: Neither hours nor minutes have leading zeros.
 */
function cpabc_get_availability_on($timestamp) {

    // @TODO: Make this per-calendar? Right now this is handled by the calendar
    // ID set in cpabc_get_option();
    // @TODO: Cache this?
    // @TODO: Refactor this into several smaller functions


    // date should be a unix timestamp?
    $tz = new DateTimeZone( get_option( 'timezone_string' ) );
    $date = new DateTime();
    _cpabc_set_timestamp_on_datetime( $date, $timestamp );
    $date->setTimezone( $tz );

    $date_str = $date->format( 'n/j/Y' );
    $now = new DateTime();
    _cpabc_set_timestamp_on_datetime( $now, time() );
    $now->setTimezone( $tz );

    // is this date in the past?
    if($date < $now) {
        return false;
    }
    
    // is this date a restricted date?
    $restricted_dates = cpabc_get_option( 'restrictedDates' );
    $restricted_dates = explode( ',', $restricted_dates );
    foreach( $restricted_dates as $rdate ) {
        if( $rdate == $date_str ) {
            return false;
        }
    }

     
    // is this date on a day of the week we accept appointments?
    $weekday = $date->format( 'w' ); // weekday, 0-6
    $workdays = cpabc_get_option( 'workingDates' );
    $workdays = explode(',', $workdays );

    if( ! in_array( $weekday, $workdays ) ) {
        return false;
    }

   
    // is this date before the min date?
    $mindate = cpabc_get_option( 'calendar_mindate' ); // string, could be anything.
    if( ! empty( $mindate ) ) {
        // see if this works. It seems that we should be able to construct a new
        // datetime from anything strtotime() can handle.
        $mindate = new DateTime( $mindate, $tz );
        if( $mindate > $date ) {
            return false;
        }
    }

  

    // is this date after the max date?
    $maxdate = cpabc_get_option( 'calendar_maxdate' );
    if( ! empty( $maxdate ) ) {
        // see if this works. It seems that we should be able to construct a new
        // datetime from anything strtotime() can handle.
        $maxdate = new DateTime( $maxdate, $tz );
        if( $maxdate < $date ) {
            return false;
        }
    }


    // well then, let's compute the availability
    // @TODO: See if this is affected by changing the week start setting
    $availability_src = cpabc_get_option( 'timeWorkingDates' . $weekday ); // eg. timeWorkingDates2 (Tuesday)

    // make an array of appointment times & capacities, then decrement 
    // capacities based on already-booked appointments
    $availability_src = explode( ',', $availability_src );
    $availability = array();
    foreach( $availability_src as $block ) {
        $divider = strrpos( $block, ':' );
        $time = substr( $block, 0, $divider );
        $capacity = intval( substr( $block, $divider + 1) );

        $availability[ $time ] = $capacity;
    }

    // now we've got the full list of appointments, let's subtract existing appointments from them.
    
    global $wpdb;
    $appointments = $wpdb->get_results( 
        $wpdb->prepare( 
            'SELECT * FROM ' . CPABC_APPOINTMENTS_CALENDARS_TABLE_NAME . ' WHERE appointment_calendar_id=%d AND DATE(datatime)=%s',
            CP_CALENDAR_ID,
            $date->format( 'Y-m-d' )
        )
    );


    foreach( $appointments as $appointment ) {
        $appointment_ts = mysql2date('U', $appointment->datatime);
        $appointment_time = date( 'G', $appointment_ts ) . ':' . intval( date( 'i', $appointment_ts ) );

        if( isset( $availability[ $appointment_time ] ) ) {
            $availability[ $appointment_time ] = $availability[ $appointment_time ] - 1;
        }
    }

    return $availability;
}


/**
 * Checks to see if there's an appointment available at the time specified by 
 * $desiredtime.
 * @param  int $desiredtimestamp A unix timestamp for when we would like the 
 *                          appointment to start.
 * @return bool              True if an appointment is available, false if not.
 */
function cpabc_appointment_is_available_at( $desiredtimestamp ) {

    $availability = cpabc_get_availability_on( $desiredtimestamp );

    $desiredtime = new DateTime();
    _cpabc_set_timestamp_on_datetime( $desiredtime, $desiredtimestamp );
    $desiredtime->setTimezone( new DateTimeZone( get_option( 'timezone_string' ) ) );

    $desired_time_key = $desiredtime->format('G') . ':' . intval( $desiredtime->format( 'i' ) );

    if( isset( $availability[ $desired_time_key ] ) && $availability[ $desired_time_key ] > 0 ) {
        return true;
    }

    return false;
}




// WIDGET CODE BELOW

class CPABC_App_Widget extends WP_Widget
{
  function CPABC_App_Widget()
  {
    $widget_ops = array('classname' => 'CPABC_App_Widget', 'description' => 'Displays a booking form' );
    $this->WP_Widget('CPABC_App_Widget', 'Appointment Booking Calendar', $widget_ops);
  }

  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
    $title = $instance['title'];
?>
  <p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
<?php
  }

  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    return $instance;
  }

  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);

    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);

    if (!empty($title))
      echo $before_title . $title . $after_title;;

    // WIDGET CODE GOES HERE
    define('CPABC_AUTH_INCLUDE', true);
    cpabc_appointments_get_public_form();

    echo $after_widget;
  }

}
add_action( 'widgets_init', create_function('', 'return register_widget("CPABC_App_Widget");') );




function _cpabc_set_timestamp_on_datetime( &$datetime, $timestamp ) {


    if ( method_exists( $datetime, 'setTimestamp' ) ) {
        $datetime->setTimestamp( $timestamp );
    } else {
        // switch to UTC for a moment
        $current_tz = date_default_timezone_get();
        date_default_timezone_set( 'UTC' );

        $obj_current_timezone = $datetime->getTimezone();
        $datetime->setTimezone( new DateTimeZone( 'UTC' ) );

        // set the date & time
        $datetime->setDate( date( 'Y', $timestamp ), date( 'm', $timestamp ), date( 'd', $timestamp) );
        $datetime->setTime( date( 'H', $timestamp ), date( 'i', $timestamp ), date( 's', $timestamp ) );

        // switch back to original TZ
        if ( $obj_current_timezone ) {
            $datetime->setTimezone( $obj_current_timezone );
        }
        date_default_timezone_set( $current_tz );
    }

}

?>