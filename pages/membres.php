<?php
session_start(); //***************************************************** */
require_once(dirname(__FILE__) . '/../core/security.php');
?>

<h2 class="text-center mb-5">Membres</h2>

<?php
run();

function run()
{
    $isLogged = false;
    if (isset($_POST["email"]) && isset($_POST["password"])) {
        connect();
        if (isset($_SESSION['user']) && $_SESSION['user'] === true) {
            header('location: http://' . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php?page=accueil');
            //header('Location : .');    
        } 
        else {
            header('Location : .');
        }
    }
          
    
    if (isset($_SESSION['user']) && $_SESSION['user'] === true) {
        $isLogged = true;
    } 
    else {
        $isLogged = false;
    }
    if ($isLogged) {
        $users = readCsv();
        renderUserList($users);
    } 
    else {
        displayLoginForm();
    }

    if (isset($_SESSION['action_type'])) {
        if($_SESSION['action_type'] === 'delete') {
            //echo "membres.php 42 : delete user";
            //unset($_SESSION['action_type']);
        }
        elseif($_SESSION['action_type'] === 'edit') {
            
            if(isset($_SESSION['user_datas'])) {
                $string = $_SESSION['user_datas'];
                $user = explode(",", $string);
                updateUser($user);
                //unset($_SESSION['user']);
            }
            //unset($_SESSION['action_type']);
        }
        

    }

    if (isset($_SESSION['edit_message'])) {
        echo "<p>" . $_SESSION['edit_message'] . "</p>";
        //unset($_SESSION['edit_message']);
    }
    
    if (isset($_SESSION['delete_message'])) {
        echo "<p>" . $_SESSION['delete_message'] . "</p>";
        //unset($_SESSION['delete_message']);
    }
}

function displayLoginForm()
{
    echo "\n" . "<h3 class='h5 text-center mb-3'>Veuillez vous connecter</h3>";
    echo "\n" . "<form class='d-flex gap-3 justify-content-center flex-wrap' action='index.php?page=membres' method='post'>";
    echo "\n" . "<div class='d-flex'>";
    echo "\n" . "<label class='me-2 pt-1' for='email'>Email</label>";
    echo "\n" . "<input class='' type='email' id='email' name='email'>";
    echo "\n" . "</div>";
    echo "\n" . "<div class='d-flex'>";
    echo "\n" . "<label class='me-2 pt-1' for='password'>Mot de Passe</label>";
    echo "\n" . "<input class='' type='password' name='password' id='password'>";
    echo "\n" . "<input type='hidden' name='connexion' value='connect'>";
    echo "\n" . "</div>";
    echo "\n" . "<input class='btn btn-success btn-sm' type='submit' value='Envoyer'>";
    echo "\n" . "</form>";
}

function readCsv()
{
    $users = [];
    $file_path = "./src/datas/users.csv";
    if (file_exists($file_path)) {
        $file_pointer = fopen($file_path, "r");
        while ($data = fgetcsv($file_pointer, null, ",")) {
            $users[] = [
                "firstname" => $data[0],
                "lastname" => $data[1],
                "email" => $data[2]
            ];
        }
    }
    return $users;
}

function renderUserList($users)
{
    //$cpt = 0;
    echo "<table class='table'>";
    echo "\n" . "<tr>";
    echo "\n" . "<th>Prénom</th>";
    echo "\n" . "<th>Nom</th>";
    echo "\n" . "<th>Email</th>";
    echo "\n" . "<th>Actions</th>";
    echo "\n" . "</tr>";

    foreach ($users as $index => $user) {
        echo "\n" . "<tr>";
        echo "\n" . "<td>" . $user["firstname"] . "</td>";
        echo "\n" . "<td>" . $user["lastname"] . "</td>";
        echo "\n" . "<td>" . $user["email"] . "</td>";
        /*
        echo "\n" . "<td class='d-flex gap-3'><form action='index.php?page=membres&action=edit&id=". $cpt ."' method='get'><input class='btn btn-success btn-sm' type='submit' value='Editer'></form>"; //'index.php?page=membres&action=delete&id=". $cpt ."'
        echo "\n" . "<form action='index.php?page=membres' method='get'><input class='btn btn-warning btn-sm' type='submit' value='Supprimer'></form></td>";
        */
        echo "\n" . "<td class='d-flex gap-3'>";
        echo "\n" . "<form class='d-flex gap-2' method='get'>";
        echo "\n" . "<input type='hidden' name='page' value='membres'>";
        echo "\n" . "<input type='hidden' name='index' value='" . $index . "'>";
        echo "\n" . "<button class='btn btn-success btn-sm' type='submit' name='action' value='edit' formaction='index.php?page=membres&action=edit'>Editer</button>";
        echo "\n" . "<button class='btn btn-danger btn-sm' type='submit' name='action' value='delete' formaction='index.php?page=membres&action=delete'>Supprimer</button>";
        echo "\n" . "</form>";
        echo "\n" . "</td>";
        echo "\n" . "</tr>";
        //$cpt++;
    }

    echo "</table>";
}

function updateUser($user) {
    echo "\n" . "<h5 class='text-center mt-5 mb-3'>User</h5>";
    //var_dump($user);
    echo "\n" . "<form class='d-flex flex-column align-items-center gap-3' method='post' action=''>";
    echo "\n" . "<div>";
    echo "\n" . "<label class='' for='firstname'>Prénom</label>";
    echo "\n" . "<input type='text' name='firstname' id='firstname' value=" . $user[0] . ">";
    echo "\n" . "</div>";
    echo "\n" . "<div>";
    echo "\n" . "<label class='' for='lastname'>Nom</label>";
    echo "\n" . "<input type='text' name='lastname' id='lastname' value=" . $user[1] . ">";
    echo "\n" . "</div>";
    echo "\n" . "<div>";
    echo "\n" . "<label class='' for='email'>Email</label>";
    echo "\n" . "<input type='text' name='email' id='email' value=" . $user[2] . ">";
    echo "\n" . "</div>";
    echo "\n" . "<input type='hidden' name='id' id='id' value=" . $user[4] . ">";
    echo "\n" . "<input type='hidden' name='action' value='update'>";
    echo "\n" . "<button class='btn btn-primary btn-sm' type='submit' >Mettre à jour</button>";
    echo "\n" . "</form>";
}

?>