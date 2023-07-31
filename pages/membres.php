<?php
require_once(dirname(__FILE__) . '/../core/security.php');
require_once(dirname(__FILE__) . '/../core/csv-manipulator.php');

run();

/**
 * Fonction principale, gère les requêtes du client.
 */
function run()
{
    echo '<h2 class="text-center mb-3">Membres</h2>';
    $isLogged = false;
    if (isset($_SESSION['user']) && $_SESSION['user'] === true) {
        $isLogged = true;
    } else {
        $isLogged = false;
    }

    /**
     * Cas où on se logge ou pas
     */
    if (isset($_POST["email"]) && isset($_POST["password"])) {
        connect();
        if (isset($_SESSION['user']) && $_SESSION['user'] === true) {
            $_SESSION['show_add_form'] = false;
            redirect('http://' . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php?page=accueil');

            //header('location: http://' . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php?page=accueil');
        } else {
            //header('Location : .');
            redirect('http://' . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php?page=membres');

        }
    }

    /**
     * gestion de la requete d'édition et de la suppression : appel fonctions de csv-manipulator
     */
    if (isset($_SESSION['user']) && $_SESSION['user'] === true) {
        if (isset($_GET["page"]) && isset($_GET["action"]) && isset($_GET["index"])) {
            $id = $_GET["index"];
            if ($_GET["action"] === "edit" && $_GET["page"] === "membres") {
                editUser($id);
                //header('location: http://' . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php?page=membres');
                redirect('http://' . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php?page=membres');

                exit();
            } elseif ($_GET["action"] === "delete" && $_GET["page"] === "membres") {
                deleteUser($id);
                //header('location: http://' . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php?page=membres');
                redirect('http://' . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php?page=membres');

                exit();
            }
        }
    }

    /**
     * gestion de la requete des formulaires de mise a jour et d'ajout d'un user
     */
    if (isset($_POST["action"]) && isset($_POST["firstname"]) && isset($_POST["lastname"]) && isset($_POST["email"]) && isset($_GET["action"]) && isset($_GET["page"])) {
        if ($isLogged && $_GET["page"] === "membres") {
            if ($_POST["action"] === "update" && $_GET["action"] === "update") {
                $user = [];
                $user[0] = $_POST["firstname"];
                $user[1] = $_POST["lastname"];
                $user[2] = $_POST["email"];
                $user[3] = $_POST["password"];
                $user[4] = $_POST["id"];
                updateUser($user);
                //redirect('http://' . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php?page=membres');
                header('location: http://' . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php?page=membres');
            } elseif ($_POST["action"] === "add" && $_GET["action"] === "add") {
                $user = [];
                $user[0] = $_POST["firstname"];
                $user[1] = $_POST["lastname"];
                $user[2] = $_POST["email"];
                $user[3] = $_POST["password"];
                addUser($user);
                $_SESSION['show_add_form'] = false;
                //redirect('http://' . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php?page=membres');
                header('location: http://' . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php?page=membres');
            }
        }
    }

    /**
     * gestion des requêtes de tris et affichage des infos de l'user loggé, 
     * de la liste des user et du bouton d'ajout ou, si on n'est pas loggé, 
     * affiche le formulaire de login.
     */
    if ($isLogged) {
        if(isset($_SESSION['user_email'])) renderUserInfos();
        if (isset($_GET["page"]) && isset($_GET["action"]) && isset($_GET["cat"])) {
            if ($_GET["action"] === "sort" && $_GET["page"] === "membres") {
                $_SESSION['show_add_form'] = false;
                sortUsers($_GET["cat"]);
            }
        }
        $users = getUsersToRender();
        renderUserList($users);
        if (!$_SESSION['show_add_form']) renderAddUserButton();
    } else {
        renderLoginForm();
    }

    /**
     * Affichage du formulaire d'ajout d'un user.
     */
    if (isset($_GET["action"]) && isset($_GET["page"])) {
        if ($_GET["action"] === "new-user") {
            $_SESSION['show_add_form'] = true;
            
            //redirect('http://' . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php?page=membres');
            
            header('location: http://' . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php?page=membres');
            renderAddUser();
        }
    }

    /**
     * Affichage du formulaire d'édition d'un user.
     */
    if (isset($_SESSION['action_type'])) {
        if ($_SESSION['action_type'] === 'delete') {
            //unset($_SESSION['action_type']);
            $_SESSION['action_type'] = "";
        } elseif ($_SESSION['action_type'] === 'edit') {

            if (isset($_SESSION['user_datas'])) {
                $string = $_SESSION['user_datas'];
                $user = explode(",", $string);
                $_SESSION['action_type'] = "";
                //redirect('http://' . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php?page=membres');

                header('location: http://' . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php?page=membres');
                renderUpdateUser($user);
                
                //unset($_SESSION['user']);
            }
            //unset($_SESSION['action_type']);
        }
    }
}

function redirect($url) {
    echo '<script type="text/javascript"> window.location="' . $url . '";</script>';
}

/**
 * Fonction qui affiche les infos du l'user loggé.
 */
function renderUserInfos() {
    if(isset($_SESSION['user_firstname']) && isset($_SESSION['user_lastname']) && isset($_SESSION['user_email'])) {
        echo "\n" . "<h6 class='text-center mb-3'><span class='text-success'>" . $_SESSION['user_firstname'] . "</span> • <span class='text-success'>" . $_SESSION['user_lastname'] . "</span> • <span class='text-success'>" . $_SESSION['user_email'] . "</span></h6>";
    }
}

/**
 * Fonction qui affiche le formulaire de login.
 */
function renderLoginForm()
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
    echo "\n" . "<input class='btn btn-success btn-sm' type='submit' value='✔ Envoyer'>";
    echo "\n" . "</form>";
}

/**
 * Fonction qui affiche les users du tableau.
 * @param $users [[]] : tableau d'user à afficher.
 */
function renderUserList($users)
{
    echo "<table class='table'>";
    echo "\n" . "<tr>";
    echo "\n" . "<th><a href='index.php?page=membres&action=sort&cat=firstname' class='link-dark link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-100-hover'>Prénom</a></th>";
    echo "\n" . "<th><a href='index.php?page=membres&action=sort&cat=lastname' class='link-dark link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-100-hover'>Nom</a></th>";
    echo "\n" . "<th><a href='index.php?page=membres&action=sort&cat=email' class='link-dark link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-100-hover'>Email</a></th>";
    echo "\n" . "<th>Actions</th>";
    echo "\n" . "</tr>";
    foreach ($users as $index => $user) {
        echo "\n" . "<tr>";
        echo "\n" . "<td>" . $user["firstname"] . "</td>";
        echo "\n" . "<td>" . $user["lastname"] . "</td>";
        echo "\n" . "<td>" . $user["email"] . "</td>";
        echo "\n" . "<td class='d-flex gap-3'>";
        echo "\n" . "<form class='d-flex gap-2' method='get'>";
        echo "\n" . "<input type='hidden' name='page' value='membres'>";
        echo "\n" . "<input type='hidden' name='index' value='" . $index . "'>";
        echo "\n" . "<button class='btn btn-success btn-sm' type='submit' name='action' value='edit' formaction='index.php?page=membres&action=edit'>✎ Editer</button>";
        echo "\n" . "<button class='btn btn-danger btn-sm' type='submit' name='action' value='delete' formaction='index.php?page=membres&action=delete'>✘ Supprimer</button>";
        echo "\n" . "</form>";
        echo "\n" . "</td>";
        echo "\n" . "</tr>";
    }
    echo "</table>";
}

/**
 * Fonction qui affiche le bouton d'ajout d'user.
 */
function renderAddUserButton()
{
    echo "\n" . "<form class='d-flex justify-content-center' method='get'>";
    echo "\n" . "<input type='hidden' name='page' value='membres'>";
    echo "\n" . "<button class='btn btn-primary btn-sm' type='submit' name='action' value='new-user' formaction='index.php'>+ Ajouter un Utilisateur</button>";
    echo "\n" . "</form>";
}

/**
 * Fonction qui affiche le formulaire de modification de $user passé en paramètre.
 * @param $user [] : user à mettre à jour.
 */
function renderUpdateUser($user)
{
    echo "\n" . "<div id='div-edit-user'>";
    echo "\n" . "<h5 class='text-center mt-5 mb-3'>User</h5>";
    echo "\n" . "<form id='form-update-user' class='d-flex flex-column align-items-center gap-3' action='index.php?page=membres&action=update' method='post' >";
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
    echo "\n" . "<div>";
    echo "\n" . "<label class='' for='password'>Mot de passe</label>";
    echo "\n" . "<input type='password' name='password' id='password' value='' placeholder='Laisser vide si pas de nouveau mot de passe'>";
    echo "\n" . "</div>";
    echo "\n" . "<input type='hidden' name='id' id='id' value=" . $user[4] . ">";
    echo "\n" . "<input type='hidden' name='action' value='update'>";
    echo "\n" . "<button class='btn btn-primary btn-sm' type='submit'>✔ Mettre à jour</button>";
    echo "\n" . "</form>";
    echo "\n" . "</div>";
}

/**
 * Fonction qui affiche le formulaire d'ajout d'un user.
 */
function renderAddUser()
{
    if ($_SESSION['show_add_form']) {
        echo "\n" . "<div id='div-add-user'>";
        echo "\n" . "<h5 class='text-center mt-5 mb-3'>Nouvel utilisateur</h5>";
        echo "\n" . "<form id='form-add-user' class='d-flex flex-column align-items-center gap-3' action='index.php?page=membres&action=add' method='post' >";
        echo "\n" . "<div>";

        echo "\n" . "<label class='' for='firstname'>Prénom</label>";
        echo "\n" . "<input type='text' name='firstname' id='firstname' value=''>";
        echo "\n" . "</div>";
        echo "\n" . "<div>";
        echo "\n" . "<label class='' for='lastname'>Nom</label>";
        echo "\n" . "<input type='text' name='lastname' id='lastname' value=''>";
        echo "\n" . "</div>";
        echo "\n" . "<div>";
        echo "\n" . "<label class='' for='email'>Email</label>";
        echo "\n" . "<input type='text' name='email' id='email' value=''>";
        echo "\n" . "</div>";
        echo "\n" . "<div>";
        echo "\n" . "<label class='' for='password'>Mot de passe</label>";
        echo "\n" . "<input type='password' name='password' id='password' value=''>";
        echo "\n" . "</div>";
        echo "\n" . "<input type='hidden' name='action' value='add'>";
        echo "\n" . "<button class='btn btn-primary btn-sm' type='submit'>+ Ajouter</button>";
        echo "\n" . "</form>";
        echo "\n" . "</div>";
        $_SESSION['show_add_form'] = false;
    } else {
        renderAddUserButton();
    }
}

?>