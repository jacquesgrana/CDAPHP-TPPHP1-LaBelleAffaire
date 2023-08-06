<?php

/**
 * Traitement du formulaire de connexion
 *
 * @return void
 */
function connect()
{
    //session_start();
    // soit l'utilisateur vient de remplir le formulaire et on vérifie login/pw
    if (isset($_POST['connexion']) && $_POST['connexion'] === 'connect') {
        //session_destroy();
        if (isset($_POST['email']) && isset($_POST['password'])) {
            if (search_user($_POST['email'], $_POST['password']) === true) {
                //session_start();
                
                set_user_session_infos($_POST['email']);
                $_SESSION['user'] = true;
                //header('location: http://' . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php?page=accueil');
            }
        }
    } elseif (isset($_SESSION['user']) && $_SESSION['user'] === true) {
        header('location: http://' . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php?page=accueil');
    } else {
        session_destroy();
    }
}

/**
 * Fonction (ajoutée) qui met dans des variables de session les infos de l'user qui vient de se logger.
 */
function set_user_session_infos($email) {
    $users = getUsersToFilter();
    foreach ($users as $u) {
        if ($u["email"] === $email) {
             $user = $u;
        }
    }
    //var_dump($user);
    $_SESSION['user_firstname'] = $user["firstname"];
    $_SESSION['user_lastname'] = $user["lastname"];
    $_SESSION['user_email'] = $email;
}

/**
 * Fonction (ajoutée) qui charge les user depuis le fichier .csv et renvoit un tableau 
 * avec le firstname, lastname et email.
 */
function getUsersToFilter()
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
    //var_dump($users);
    return $users;
}

/**
 * Vérifie que l'utilisateur est connecté
 *
 * @return boolean
 */
function is_connected()
{
    session_start();
    if (isset($_SESSION['user']) && $_SESSION['user'] === true) {
        return true;
    }
    session_destroy();
    return false;
}

/**
 * Déconnecte l'utilisateur
 *
 * @return void
 */
function disconnect()
{
    if (isset($_SESSION['user'])) {
        session_destroy();
    }
}

/**
 * Lit le fichier des utilisateurs
 *
 * @return mixed
 */
function read_users()
{
    if ($fp = fopen(dirname(__FILE__) . '/../src/datas/users.csv', 'r')) {
        while ($user = fgetcsv($fp, null, ',')) {
            //echo $user[2] . $user[3];
            $return[$user[2]] = $user[3];
        }
        fclose($fp);
       // var_dump($return);
        return $return;
    } else {
        echo 'Erreur pendant l\'ouverture du fichier<br>';
    }
}

/**
 * Cherche un utilisateur dans le fichier users.csv avec nom d'utilisateur et mdp
 *
 * @param string $name le nom d'utilisateur
 * @param string $pwd le mot de passe de l'utilisateur
 * @return void
 */
function search_user($email, $pwd)
{
    $users = read_users();
    if (is_array($users) && array_key_exists($email, $users) &&  password_verify($pwd, $users[$email])) {
        //echo "pw ok";
        return true;
    }
    //echo "pw ko";
    return false;
}

?>