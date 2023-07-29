<?php

/**
 * Traitement du formulaire de connexion
 *
 * @return void
 */
function connect()
{
    session_start();
    // soit l'utilisateur vient de remplir le formulaire et on vérifie login/pw
    if (isset($_POST['connexion']) && $_POST['connexion'] === 'connect') {
        session_destroy();
        if (isset($_POST['email']) && isset($_POST['password'])) {
            if (search_user($_POST['email'], $_POST['password']) === true) {
                session_start();
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