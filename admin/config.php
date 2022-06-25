<style>
    /* Style the tab */
    .tab {
        overflow: hidden;
        border: 1px solid #ccc;
        background-color: #f1f1f1;
    }

    /* Style the buttons that are used to open the tab content */
    .tab button {
        background-color: inherit;
        float: left;
        border: none;
        outline: none;
        cursor: pointer;
        padding: 14px 16px;
        transition: 0.3s;
    }

    /* Change background color of buttons on hover */
    .tab button:hover {
        background-color: #ddd;
    }

    /* Create an active/current tablink class */
    .tab button.active {
        background-color: #ccc;
    }

    /* Style the tab content */
    .tabcontent {
        display: none;
        padding: 6px 12px;
        border: 1px solid #ccc;
        border-top: none;
    }
</style>
<script>
    function updateButton() {
        aux = jQuery(".tablinks");
        for (let i = 0; i < aux.size(); i++) {
            if (aux[i].className == 'tablinks active') {
                aux[i].click();
                i = jQuery(".tablinks").size();
            }
        }
    }

    function openPersons(evt, Status, id = null) {
        // Declare all variables
        let i, tabcontent, tablinks;
        // Get all elements with class="tabcontent" and hide them
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }

        // Get all elements with class="tablinks" and remove the class "active"
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(Status).style.display = "block";
        evt.currentTarget.className += " active";

    }
</script>

<?php
global $wpdb;
$table_persons = $wpdb->prefix . 'persons';
$table_contacts = $wpdb->prefix . 'contacts';

if (isset($_POST['name'])) {
    $nome = $_POST['name'];
    $email = $_POST['email'];

    $wpdb->insert(
        $table_persons,
        array(
            'name' => $nome,
            'email' => $email,
        )
    );
}

if (isset($_POST['number'])) {
    $number = intval($_POST['number']);
    $id_person = intval($_POST['person_id']);
    $code = intval($_POST['code']);
    $code = intval(substr($code, 1));
    $wpdb->insert(
        $table_contacts,
        array(
            'id_person' => $id_person,
            'code' => $code,
            'number' => $number
        )
    );
}

if (isset($_POST['id_person'])) {
    global $wpdb;
    $table_persons =  'wp-persons';
    $id_person = intval($_POST['id_person']);
    $wpdb->delete($table_persons, array('id' => $id_person));
}

//Deletar
//$id = 2; 
//$wpdb->delete( $table_persons, array( 'id' => $id ) );

//Editar
//$id = 3;
//$wpdb->update( $table_persons, array( 'name' => 'josé' ), array('id' => $id) );


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
  code int(5) NOT NULL,
  number int(20) NOT NULL,
  PRIMARY KEY  (id)
) $charset_collate;";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);

?>
<!-- Tab links -->
<div class="tab">
    <button class="tablinks active" onclick="openPersons(event, 'Persons')">Persons</button>
    <button class="tablinks" onclick="openPersons(event, 'Contacts')">Contacts</button>
</div>




<!-- Tab content -->
<div id="Persons" class="tabcontent" style="display: block;">
    <form method="POST">
        <table class="form-table">
            <tbody>
                <tr>
                    <th><label for="name">Nome: </label></th>
                    <td>
                        <input type="text" name="name" id="name">
                    </td>
                </tr>
                <tr id="delay_field">
                    <th><label for="email">E-mail: </label></th>
                    <td><input type="text" name="email" id="email"></td>
                </tr>
            </tbody>
        </table>

        <?php submit_button(); ?>
    </form>
    <h3>Pessoas</h3>
    <?php
    $all_persons = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "persons`");

    foreach ($all_persons as $person) {
    ?>
        <?php echo $person->name ?><a href="" onclick="deletarPerson(<?php echo $person->id ?>)"> remover</a><br>
    <?php

    }
    ?>
</div>

<!-- Tab content -->
<div id="Contacts" class="tabcontent">
    <form method="POST">
        <table class="form-table">
            <tbody>
                <tr>
                    <th><label for="person">Choose a person:</label></th>
                    <td>
                        <select name="person_id" id="person_id">
                            <?php
                            $all_persons = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "persons`");
                            foreach ($all_persons as $person) {
                            ?>
                                <option id="<?php echo $person->id ?>" value="<?php echo $person->id ?>"><?php echo $person->name ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </td>

                </tr>
                
                <tr>
                    <th><label for="code">Code:</label></th>
                    <td>
                        <select name="code" id="code">
                            <option value="+55">Brasil (+55)</option>
                            <option value="+54">+54</option>
                            <option value="+56">+56</option>
                            <option value="+351">Portugal (+351)</option>
                    </td>
                    </select>
                </tr>
                <tr>
                    <th><label for="name">Number: </label></th>
                    <td>
                        <input type="number" name="number" id="number">
                    </td>
                </tr>
            </tbody>
        </table>

        <?php submit_button(); ?>
    </form>
    <h3>Contatos</h3>
    <?php
    $all_persons = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "persons`");

    foreach ($all_persons as $person) {

        $all_contacts = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "contacts` where id_person = $person->id");
        echo $person->name . "<br>";
        foreach ($all_contacts as $contact) {
    ?>
            <?php echo $contact->number ?></a><a href="" onclick="deletarContact(<?php echo $contact->id ?>)"> remover</a><br>
    <?php

        }
        echo "---------------------<br>";
    }
    ?>

</div>
<?php




/*
// create & initialize a curl session
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "https://app.pixelencounter.com/api/basic/monsters/random/webp?size=100");
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$avatar = curl_exec($curl);
curl_close($curl);
print($avatar);
*/
?>

<!--AJAX PARA LISTAR OS DADOS -->
<script type="text/javascript">
    jQuery(document).ready(function() {
        listarContatos();
        listarPessoas();
    })
</script>

<script type="text/javascript">
    function listarContatos() {
        let pag = "<?php echo plugin_dir_url(__DIR__) . 'admin/'; ?>";
        let id_person = document.getElementById('id_person').value;
        jQuery.ajax({
            url: pag + "/listar-contatos.php",
            method: "post",
            data: {
                id_person
            },
            dataType: "html",
            success: function(result) {
                jQuery('#listar-contatos').html(result)
            },
        })
    }
</script>

<script type="text/javascript">
    function listarPessoas() {
        let pag = "<?php echo plugin_dir_url(__DIR__) . 'admin/'; ?>";
        jQuery.ajax({
            url: pag + "/listar-pessoas.php",
            method: "post",
            dataType: "html",
            success: function(result) {
                jQuery('#listar-pessoas').html(result)
            },
        })
    }
</script>

<script type="text/javascript">
    async function deletarPerson(id_person) {
        return new Promise((resolve, reject) => {
            jQuery.ajax({
                url: window.location.origin + "/wp-json/api/v2/removeperson/" + id_person,
                type: "GET",
                data: id_person,
                success: function(response) {
                    resolve(response['remove']);
                    id = '#' + id_person
                    jQuery(id).remove();
                },
                error: function(jqXHR) {
                    reject(new Error(`Could not check whether nickname exists or not.\nReason: ${ jqXHR.responseText }`));
                }
            });
        })
    }
</script>

<script type="text/javascript">
    async function deletarContact(id_contact) {
        return new Promise((resolve, reject) => {
            jQuery.ajax({
                url: window.location.origin + "/wp-json/api/v2/removecontact/" + id_contact,
                type: "GET",
                data: id_contact,
                success: function(response) {
                    resolve(response['remove']);
                    id = '#' + id_contact
                    jQuery(id).remove();
                },
                error: function(jqXHR) {
                    reject(new Error(`Could not check whether nickname exists or not.\nReason: ${ jqXHR.responseText }`));
                }
            });
        })
    }
</script>