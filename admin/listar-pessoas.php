<?php
require('/shared/httpd/wordpress/wp-load.php');
global $wpdb;

$all_persons = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "persons`");

foreach ($all_persons as $person) {
?>
    <?php echo $person->name ?><a href=""> remover</a><br>
<?php

}
?>