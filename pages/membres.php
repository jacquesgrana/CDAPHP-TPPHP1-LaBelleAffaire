<?php
require_once(dirname(__FILE__) . '/../core/security.php');
require_once(dirname(__FILE__) . '/../core/csv-manipulator.php');

run();

/**
 * Fonction principale de la page, gère les requêtes et appelle les fonctions d'affichage.
 */
function run()
{

    $protocol = 'http://';

    // Cas où on se logge avec les bons credentials ou pas
    if (isset($_POST["email"]) && isset($_POST["password"])) {
        connect();
        if (isset($_SESSION['user']) && $_SESSION['user'] === true) {
            if (!isset($_SESSION['show_add_form'])) {
                $_SESSION['show_add_form'] = 'false';
            }
            $_SESSION['show_add_form'] = 'false';
            if (!isset($_SESSION['show_update_form'])) {
                $_SESSION['show_update_form'] = 'false';
            }
            redirect($protocol . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php', ['page' => 'membres']);
            //header('Location: http://' . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php?page=accueil');
        } else {
            //header('Location : .');
            redirect($protocol . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php', ['page' => 'accueil']);
        }
    }

    $isLogged = false;
    if (isset($_SESSION['user']) && $_SESSION['user'] === true) {
        $isLogged = true;
    } else {
        $isLogged = false;
    }

    if ($isLogged) {
        if (isset($_GET["action"]) && isset($_GET["page"])) {
            if ($_GET["page"] === "membres") {
                // affichage du formulaire d'ajout d'un nouvel user
                if ($_GET["action"] === "new-user") {
                    $_SESSION['show_add_form'] = 'true';
                    $_SESSION['show_update_form'] = 'false';
                }
                // recupération des données de la requête d'ajoût d'un nouvel user
                // et ajout du nouvel user dans la fichier .csv
                elseif ($_GET["action"] === "add") {
                    if (isset($_POST["firstname"]) && isset($_POST["lastname"]) && isset($_POST["email"]) && isset($_POST["password"])) {
                        $user = [];
                        $user[0] = $_POST["firstname"];
                        $user[1] = $_POST["lastname"];
                        $user[2] = $_POST["email"];
                        $user[3] = $_POST["password"];
                        addUser($user);
                        $_SESSION['show_add_form'] = 'false';
                        header('location: ' . $protocol . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php?page=membres');
                    }
                } 
                // affichage du formulaire d'éditeon/modification d'un user
                elseif ($_GET["action"] === "edit" && isset($_GET["index"])) {
                    $id = $_GET["index"];
                    editUser($id);
                    $_SESSION['show_add_form'] = 'false';
                    $_SESSION['show_update_form'] = 'true';
                } 
                // recupération des données de la requête d'édition/modification d'un user
                // et mise à jour de l'user dans la fichier .csv
                elseif ($_GET["action"] === "update" && isset($_POST["id"])) {
                    $user = [];
                    $user[0] = $_POST["firstname"];
                    $user[1] = $_POST["lastname"];
                    $user[2] = $_POST["email"];
                    $user[3] = $_POST["password"];
                    $user[4] = $_POST["id"];
                    updateUser($user);
                    $_SESSION['show_update_form'] = 'false';
                    header('location: ' . $protocol . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php?page=membres');
                } 
                // suppression d'un user selon son id
                elseif ($_GET["action"] === "delete" && isset($_GET["index"])) {
                    $id = $_GET["index"];
                    deleteUser($id);
                    redirect($protocol . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php', ['page' => 'membres']);
                } 
                // gestion des tris du fichier .csv
                elseif ($_GET["action"] === "sort" && isset($_GET["cat"])) {
                    $_SESSION['show_add_form'] = 'false';
                    sortUsers($_GET["cat"]);
                }
            }
        }
        if (isset($_SESSION['action_type'])) {
            // efface la variable de session modifiée par deleteUser($id)
            if ($_SESSION['action_type'] === 'delete') {
                $_SESSION['action_type'] = "";
            } 
            // commande l'affichage de l'user à éditer/modifier
            // efface la variable de session modifiée par updateUser($user)
            elseif ($_SESSION['action_type'] === 'edit') {
                //$string = $_SESSION['user_datas'];
                //$user = explode(",", $string);
                $_SESSION['action_type'] = "";
                //redirect('http://' . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php?page=membres');
                $_SESSION['show_update_form'] = 'true';
                $_SESSION['show_add_form'] === 'false';
                //renderUpdateUserForm($user); // *****************************************
                redirect($protocol . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php', ['page' => 'membres']);

                //header('location: ' . $protocol . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php?page=membres');
            }
        }
    }
    renderPage($isLogged);
}

/**
 * Fonction qui affiche la page.
 */
function renderPage($isLogged)
{
    echo '<h2 class="text-center mb-3">Membres</h2>';
    if ($isLogged) {
        $users = getUsersToRender();
        renderUserInfos();
        renderUserList($users);
        if ($_SESSION['show_add_form'] === 'false') renderAddUserButton();
        if ($_SESSION['show_add_form'] === 'true') renderAddUserForm();
        // && $_SESSION['show_update_form'] === 'false'
        if (isset($_SESSION['user_datas'])) {
            $string = $_SESSION['user_datas'];
            $user = explode(",", $string);
            if ($_SESSION['show_update_form'] === 'true') renderUpdateUserForm($user);
        // && $_SESSION['show_edit_form'] === 'false'

        }
    } else {
        renderLoginForm();
    }
}

/**
 * Fonction qui redirige vers $url avec des paramtres dans la query string.
 * Utilise du js pour moins utiliser la fonction 'header' de php.
 * @param string $url : destination de la redirection.
 * @param array $queryParameters : tableau associatif contenant les paramètres 
 * de la query string.
 */
function redirect($url, $queryParameters = [])
{
    $queryString = http_build_query($queryParameters);
    if (!empty($queryString)) {
        $url .= '?' . $queryString;
    }
    echo '<script type="text/javascript"> window.location="' . $url . '";</script>';
}

/**
 * Fonction qui effectue une redirection côté client vers $url avec des paramètres dans la query string.
 * Utilise une balise <meta> pour effectuer la redirection.
 * @param string $url : destination de la redirection.
 * @param array $queryParameters : tableau associatif contenant les paramètres de la query string.
 */
/*
function htmlMetaRedirect($url, $queryParameters = [])
{
    $queryString = http_build_query($queryParameters);
    if (!empty($queryString)) {
        $url .= '?' . $queryString;
    }

    echo '<!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="refresh" content="0; url=' . $url . '">
    </head>
    <body>
        <!-- Redirection en cours... -->
    </body>
    </html>';
    exit();
}*/

/**
 * Fonction qui affiche les infos du l'user loggé.
 */
function renderUserInfos()
{
    if (isset($_SESSION['user_firstname']) && isset($_SESSION['user_lastname']) && isset($_SESSION['user_email'])) {
        echo "\n" . "<h6 class='text-center mb-3'><span class='text-success'>" . $_SESSION['user_firstname'] . "</span> • <span class='text-success'>" . $_SESSION['user_lastname'] . "</span> • <span class='text-success'>" . $_SESSION['user_email'] . "</span></h6>";
    }
}

/**
 * Fonction qui affiche le formulaire de login.
 */
function renderLoginForm()
{
    echo "\n" . "<div class='w-100 d-flex flex-column align-items-center'>";
    echo "\n" . "<h3 class='h5 text-center mb-3'>Veuillez vous connecter</h3>";
    echo "\n" . "<div class='div-form d-flex justify-content-center border border-secondary rounded w-50 p-4 pb-3'>";
    echo "\n" . "<form class='d-flex gap-3 flex-column align-items-start flex-wrap' action='index.php?page=membres' method='post'>";
    echo "\n" . "<div class='d-flex'>";
    echo "\n" . "<label class='me-2 pt-1' for='email'>Email</label>";
    echo "\n" . "<input class='input-form px-2' type='email' id='email' name='email'>";
    echo "\n" . "</div>";
    echo "\n" . "<div class='d-flex'>";
    echo "\n" . "<label class='me-2 pt-1' for='password'>Mot de Passe</label>";
    echo "\n" . "<input class='input-form px-2' type='password' name='password' id='password'>";
    echo "\n" . "<input type='hidden' name='connexion' value='connect'>";
    echo "\n" . "</div>";
    echo "\n" . "<div class='w-100 d-flex justify-content-center'>";
    echo "\n" . "<input class='btn btn-success btn-sm' type='submit' value='✔ Envoyer'>";
    echo "\n" . "</div>";
    echo "\n" . "</form>";
    echo "\n" . "</div>";
    echo "\n" . "</div>";
}

/**
 * Fonction qui affiche les users du tableau.
 * @param $users [[]] : tableau d'user à afficher.
 */
function renderUserList($users)
{
    echo "<table class='table table-striped table-sm rounded'>";
    echo "\n" . "<tr class=''>";
    echo "\n" . "<th class='col-3 pt-2 px-2'><a href='index.php?page=membres&action=sort&cat=firstname' class='link-dark link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-100-hover'>Prénom</a></th>";
    echo "\n" . "<th class='col-3 pt-2'><a href='index.php?page=membres&action=sort&cat=lastname' class='link-dark link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-100-hover'>Nom</a></th>";
    echo "\n" . "<th class='col-5 pt-2'><a href='index.php?page=membres&action=sort&cat=email' class='link-dark link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-100-hover'>Email</a></th>";
    echo "\n" . "<th class='col-1 pt-2'>Actions</th>";
    echo "\n" . "</tr>";
    foreach ($users as $index => $user) {
        echo "\n" . "<tr>";
        echo "\n" . "<td class='pt-2 px-2'>" . $user["firstname"] . "</td>";
        echo "\n" . "<td class='pt-2'>" . $user["lastname"] . "</td>";
        echo "\n" . "<td class='pt-2'>" . $user["email"] . "</td>";
        echo "\n" . "<td class='d-flex gap-3'>";
        echo "\n" . "<form class='d-flex gap-2' method='get'>";
        echo "\n" . "<input type='hidden' name='page' value='membres'>";
        echo "\n" . "<input type='hidden' name='index' value='" . $index . "'>";
        echo "\n" . "<button class='btn btn-success btn-sm' type='submit' name='action' value='edit' formaction='index.php?page=membres&action=edit'>✎</button>";
        echo "\n" . "<button class='btn btn-danger btn-sm' type='submit' name='action' value='delete' formaction='index.php?page=membres&action=delete'>✘</button>";
        echo "\n" . "</form>";
        echo "\n" . "</td>";
        echo "\n" . "</tr>";
    }
    echo "</table>";
    /*
    if ($_SESSION['show_add_form'] === 'false') {
        renderAddUserButton();
    }*/
}

/**
 * Fonction qui affiche le bouton d'ajout d'user.
 */
function renderAddUserButton()
{
    echo "\n" . "<form class='d-flex justify-content-center' method='get'>";
    echo "\n" . "<input type='hidden' name='page' value='membres'>";
    echo "\n" . "<button class='btn btn-success btn-sm' type='submit' name='action' value='new-user' formaction='index.php'>+ Ajouter un Utilisateur</button>";
    echo "\n" . "</form>";
}

/**
 * Fonction qui affiche le formulaire d'édition/modification de l'$user passé en paramètre.
 * @param $user [] : user à éditer/modifier.
 */
function renderUpdateUserForm($user)
{
    echo "\n" . "<div id='div-edit-user' class='d-flex flex-column align-items-center'>";
    echo "\n" . "<h5 class='text-center mt-5 mb-3'>User</h5>";
    echo "\n" . "<div class='div-form d-flex justify-content-center border border-secondary rounded w-50 p-4 pb-3'>";
    echo "\n" . "<form id='form-update-user' class='d-flex flex-column align-items-start gap-3' action='index.php?page=membres&action=update' method='post'>";
    echo "\n" . "<div>";
    echo "\n" . "<label class='me-2' for='firstname'>Prénom</label>";
    echo "\n" . '<input class="input-form px-2" type="text" name="firstname" id="firstname" value=' . "'$user[0]'" . '>';
    echo "\n" . "</div>";
    echo "\n" . "<div>";
    echo "\n" . "<label class='me-2' for='lastname'>Nom</label>";
    echo "\n" . '<input class="input-form px-2" type="text" name="lastname" id="lastname" value=' . "'$user[1]'" . '>';
    echo "\n" . "</div>";
    echo "\n" . "<div>";
    echo "\n" . "<label class='me-2' for='email'>Email</label>";
    echo "\n" . '<input class="input-form px-2" type="email" name="email" id="email" value=' . "'$user[2]'" . '>';
    echo "\n" . "</div>";
    echo "\n" . "<div>";
    echo "\n" . "<label class='me-2' for='password'>Mot de passe</label>";
    echo "\n" . "<input class='input-form px-2' type='password' name='password' id='password' value='' placeholder='Laisser vide si pas de changement'>";
    echo "\n" . "</div>";
    //echo "\n" . "<input type='hidden' name='id' id='id' value=" . $user[4] . ">";
    echo "\n" . '<input type="hidden" name="id" id="id" value=' . "'$user[4]'" . '>';
    echo "\n" . "<div class='w-100 d-flex justify-content-center'>";
    echo "\n" . "<button class='btn btn-success btn-sm' type='submit' name='action' value='update'>✔ Valider</button>";
    echo "\n" . "</div>";
    echo "\n" . "</form>";
    echo "\n" . "</div>";
    echo "\n" . "</div>";
}

/**
 * Fonction qui affiche le formulaire d'ajout d'un user.
 */
function renderAddUserForm()
{
        echo "\n" . "<div id='div-add-user' class='d-flex flex-column align-items-center'>";
        echo "\n" . "<h5 class='text-center mt-5 mb-3'>Nouvel utilisateur</h5>";
        echo "\n" . "<div class='div-form d-flex justify-content-center border border-secondary rounded w-50 p-4 pb-3'>";
        echo "\n" . "<form id='form-add-user' class='d-flex flex-column align-items-start gap-3' action='index.php?page=membres&action=add' method='post' >";
        echo "\n" . "<div class=''>";
        echo "\n" . "<label class='me-2' for='firstname'>Prénom</label>";
        echo "\n" . "<input class='input-form px-2' type='text' name='firstname' id='firstname' value=''>";
        echo "\n" . "</div>";
        echo "\n" . "<div>";
        echo "\n" . "<label class='me-2' for='lastname'>Nom</label>";
        echo "\n" . "<input class='input-form px-2' type='text' name='lastname' id='lastname' value=''>";
        echo "\n" . "</div>";
        echo "\n" . "<div>";
        echo "\n" . "<label class='me-2' for='email'>Email</label>";
        echo "\n" . "<input class='input-form px-2' type='email' name='email' id='email' value=''>";
        echo "\n" . "</div>";
        echo "\n" . "<div>";
        echo "\n" . "<label class='me-2' for='password'>Mot de passe</label>";
        echo "\n" . "<input class='input-form px-2' type='password' name='password' id='password' value=''>";
        echo "\n" . "</div>";
        echo "\n" . "<div class='w-100 d-flex justify-content-center'>";
        echo "\n" . "<button class='btn btn-success btn-sm' type='submit' name='action' value='add'>✔ Valider</button>";
        echo "\n" . "</div>";
        echo "\n" . "</form>";
        echo "\n" . "</div>";
        echo "\n" . "</div>";
}
