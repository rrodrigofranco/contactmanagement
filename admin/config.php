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
        if (id != null) {
            document.querySelector("#person_id").value = id;
        }
    }

    function editPerson(id, name, email) {
        // Declare all variables
        document.querySelector('#adicionar').textContent = 'Editar'
        document.querySelector('#voltar').style.display = 'block'
        document.querySelector("#id_person_update").value = id
        document.querySelector("#name").value = name;
        document.querySelector("#email").value = email;

    }

    function editContact(id, id_person, code, number) {
        // Declare all variables
        document.querySelector('#add-contact').textContent = 'Editar'
        document.querySelector('#voltar-contact').style.display = 'block'
        document.querySelector("#id_contact_update").value = id
        document.querySelector("#number").value = number;
        document.querySelector("#person_id").value = id_person
        document.querySelector("#code").value = '+' + code;
    }
    jQuery(function() {
        jQuery("#filter-person").on("keyup", function() {
            let value = jQuery(this).val().toLowerCase();

            jQuery("#persons-table > tbody > tr").filter(function() {
                jQuery(this).toggle(jQuery(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });

    jQuery(function() {
    jQuery('input[type="submit"]').prop('disabled', true);
    jQuery('#number').on('input', function(e) {
        if(this.value.length === 9) {
            jQuery('input[type="submit"]').prop('disabled', false);
        } else {
            jQuery('input[type="submit"]').prop('disabled', true);
        }
    });
});
</script>

<?php
global $wpdb;
$table_persons = $wpdb->prefix . 'persons';
$table_contacts = $wpdb->prefix . 'contacts';



if (isset($_POST['id_person_update'])) {
    $id_person = $_POST['id_person_update'];
    if (!empty($id_person)) {
        $name = $_POST['name'];
        $email = $_POST['email'];

        $wpdb->update($table_persons, array('name' => $name, 'email' =>  $email), array('id' => intval($id_person)));
    } else if (isset($_POST['name'])) {
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
}

if (isset($_POST['id_contact_update'])) {
    $id_contact = $_POST['id_contact_update'];
    if (!empty($id_contact)) {
        $number = intval($_POST['number']);
        $id_person = intval($_POST['person_id']);
        $code = $_POST['code'];
        $wpdb->update($table_contacts, array('id_person' => $id_person, 'code' =>  $code, 'number' => $number), array('id' => intval($id_contact)));
    } else if (isset($_POST['number'])) {
        $number = intval($_POST['number']);
        $id_person = intval($_POST['person_id']);
        $code = intval($_POST['code']);
        $code = intval($code);
        $wpdb->insert(
            $table_contacts,
            array(
                'id_person' => $id_person,
                'code' => $code,
                'number' => $number
            )
        );
    }
}

?>
<!-- Tab links -->
<div class="tab">
    <button class="tablinks active" onclick="openPersons(event, 'Persons')">Persons</button>
    <button class="tablinks" onclick="openPersons(event, 'Contacts')">Contacts</button>
</div>


<!-- Tab content -->
<div id="Persons" class="tabcontent" id="commentForm" style="display: block;">
    <h2 id="adicionar">Adicionar</h2>
    <a id="voltar" href="" style="display: none">Voltar</a>
    <form method="POST">
        <table class="form-table">
            <tbody>
                <tr id="name">
                    <th><label for="name">Nome: </label></th>
                    <td>
                        <input type="text" name="name" id="name" required minlength="5">
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="email">E-mail: </label>
                    </th>
                    <td> <input id="email" type="email" name="email" required></td>
                </tr>
                <input hidden type="number" name="id_person_update" id="id_person_update">
            </tbody>
        </table>
        <?php submit_button(); ?>
    </form>
    <h3>Pessoas</h3>
    <?php
    $all_persons = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "persons`");
    ?>
    <div class="form-group">
        <label for="filter">Filter:</label> <input type="text" id="filter-person">
    </div>
    <table id="persons-table">
        <tr>
            <th>Avatar</th>
            <th>Nome</th>
            <th>Email</th>
        </tr>
        <?php
        foreach ($all_persons as $person) {
        ?>
            <tr>
                <td>------</td>
                <td><?php echo $person->name ?> <a href="#" onclick="openPersons(event, 'Contacts', id = <?php echo $person->id ?>)"> AddContact |</a><a href="" onclick="deletarPerson(<?php echo $person->id ?>)"> remover</a> <a href="#" onclick="editPerson(<?php echo $person->id ?>, '<?php echo $person->name ?>', '<?php echo $person->email ?>'); event.preventDefault();">| Editar</a></td>
                <td><?php echo $person->email ?></td>
            </tr>
        <?php

        }
        ?>
    </table>
</div>

<!-- Tab content -->
<div id="Contacts" class="tabcontent">
    <h2 id="add-contact">Adicionar</h2>
    <a id="voltar-contact" href="" style="display: none">Voltar</a>
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
                            <option value="+351">Portugal (+351)</option>
                            <option value="+55">Brasil (+55)</option>
                            <option value="+54">Chile (+54)</option>
                            <option value="+56">Argentina (+56)</option>
                    </td>
                    </select>
                </tr>
                <tr>
                    <th><label for="name">Number: </label></th>
                    <td>
                        <input type="number" name="number" id="number" min="9" required>
                    </td>
                </tr>
                <input hidden type="number" name="id_contact_update" id="id_contact_update">
            </tbody>
        </table>

        <?php submit_button(); ?>
    </form>
    <h3>Contatos</h3>
    <table id="contacts-table">
        <tr>
            <th>Pessoa</th>
            <th>Contatos</th>
        </tr>
        <?php
        $all_persons = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "persons`");

        foreach ($all_persons as $person) {

            $all_contacts = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "contacts` where id_person = $person->id");
        ?>
            <tr>
                <td><?php echo $person->name; ?></td>

                <?php
                foreach ($all_contacts as $contact) {
                ?>
                    <td>(<?php echo $contact->code ?>) <?php echo $contact->number ?></a><a href="" onclick="deletarContact(<?php echo $contact->id ?>)"> remover</a><a href="#" onclick="editContact(<?php echo $contact->id ?>, '<?php echo $contact->id_person ?>', '<?php echo $contact->code ?>', '<?php echo $contact->number ?>'); event.preventDefault();"> Editar</a><br></td>
                <?php

                }
                ?>
            </tr>
        <?php
        }
        ?>
    </table>
</div>
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