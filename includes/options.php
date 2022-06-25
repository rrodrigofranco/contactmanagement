<?php
global $wpdb;
$table_persons = $wpdb->prefix . 'persons';
$table_contacts = $wpdb->prefix . 'contacts';
$charset_collate = $wpdb->get_charset_collate();

$sql = "CREATE TABLE $table_persons (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  name text NOT NULL,
  email varchar(55) NOT NULL,
  PRIMARY KEY  (id)
) $charset_collate;
CREATE TABLE $table_contacts (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  id_person mediumint(20) NOT NULL,
  code int(10) NOT NULL,
  number int(20) NOT NULL,
  PRIMARY KEY  (id)
) $charset_collate;";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);
// Settings menu creation
function contact_management_admin_menu()
{
    add_menu_page('Contacts', 'Contacts', 'manage_options', CM_ROUTE . '/admin/config.php', '', 'dashicons-admin-comments');
}
add_action('admin_menu', 'contact_management_admin_menu');


// Settings route to remove person
function remove_person_endpoint($id_person)
{
    global $wpdb;
    $table_persons =  $wpdb->prefix . 'persons';
    $table_contacts = $wpdb->prefix . 'contacts';
    $wpdb->delete($table_persons, array('id' => $id_person));
    $wpdb->delete($table_contacts, array('id_person' => $id_person));
    return true;
}

function at_rest_init_remove_person()
{
    // route url: domain.com/wp-json/$namespace/$route
    $namespace = 'api/v2';
    $route     = 'removeperson/(?P<id_person>.*?)';

    register_rest_route($namespace, $route, array(
        'methods'   => WP_REST_Server::READABLE,
        'callback' => static function (WP_REST_Request $request) {
            $id_person = $request->get_param('id_person');
            return [
                'id_person' => $id_person,
                'remove' => remove_person_endpoint($id_person),
            ];
        },
    ));
}

add_action('rest_api_init', 'at_rest_init_remove_person');

// Settings route to remove contact
function remove_contact_endpoint($id_contact)
{
    global $wpdb;
    $table_contacts = $wpdb->prefix . 'contacts';
    $wpdb->delete($table_contacts, array('id' => $id_contact));
    return true;
}

function at_rest_init_remove_contact()
{
    // route url: domain.com/wp-json/$namespace/$route
    $namespace = 'api/v2';
    $route     = 'removecontact/(?P<id_contact>.*?)';

    register_rest_route($namespace, $route, array(
        'methods'   => WP_REST_Server::READABLE,
        'callback' => static function (WP_REST_Request $request) {
            $id_contact = $request->get_param('id_contact');
            return [
                'id_contact' => $id_contact,
                'remove' => remove_contact_endpoint($id_contact),
            ];
        },
    ));
}

add_action('rest_api_init', 'at_rest_init_remove_contact');

// Settings route to check email

function check_email_contact_endpoint($email)
{
    global $wpdb;
    $has_email = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "persons` where email = '$email'");
    if (empty($has_email)) {
        return false;
    } else {
        return true;
    }
}

function at_rest_init_check_email()
{
    // route url: domain.com/wp-json/$namespace/$route
    $namespace = 'api/v2';
    $route     = 'checkemail/(?P<email>.*?)';

    register_rest_route($namespace, $route, array(
        'methods'   => WP_REST_Server::READABLE,
        'callback' => static function (WP_REST_Request $request) {
            $email = $request->get_param('email');
            return [
                'email' => $email,
                'exists' => check_email_contact_endpoint($email),
            ];
        },
    ));
}

add_action('rest_api_init', 'at_rest_init_check_email');
