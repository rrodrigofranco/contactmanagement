<?php
require('/shared/httpd/wordpress/wp-load.php');
$id_person = $_POST['id_person'];
global $wpdb;
//die();

$all_contacts = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "contacts` where id_person = '$id_person'");

foreach ($all_contacts as $contact) {
?>
    <?php echo $contact->number ?></a><a href=""> remover</a><br>
<?php
}
?>