<?php
//session_start();
/**
 * Librairie dédiée à la manipulation du fichier .csv de données.
 */


 /**
  * Fonction qui supprime un user du fichier .csv selon son id et renvoit user et action_type 
  * dans les variables de session $_SESSION['user_datas'] et $_SESSION['action_type'].
  * @param $id {int} : id de l'user à supprimer
  */
function deleteUser($id)
{
    // charger csv dans un tableau avec tout
    $users = getUsers();
    $user = $users[$id];
    // enlever du tableau l'element selon l'id
    $users = array_filter($users, fn ($u) => $u[4] != $id);
    // ecrire le fichier
    saveUsers($users);
    $_SESSION['action_type'] = "delete";
    $string = implode(",", $user);
    $_SESSION['user_datas'] = $string;
}

/**
 *  Fonction qui cherche un user du fichier .csv selon son id et renvoit user et action_type 
  * dans les variables de session $_SESSION['user_datas'] et $_SESSION['action_type']. 
 * @param $id {int} : id de l'user à renvoyer
 */
function editUser($id)
{
    $users = getUsers();
    $_SESSION['action_type'] = "edit";
    $user = $users[$id];
    $string = implode(",", $user);
    $_SESSION['user_datas'] = $string;
}

/**
 * Fonction qui renvoit le tableau des user depuis le fichier .csv.
 * chaque user est un tableau à indice et l'index de l'user est ajouté.
 * @returns $users [[]]
 */
function getUsers()
{
    if ($fp = fopen(dirname(__FILE__) . '/../src/datas/users.csv', 'r')) {
        $i = 0;
        while ($user = fgetcsv($fp, null, ',')) {
            $user[4] = $i;
            $return[$i] = $user;
            $i++;
        }
        fclose($fp);
        return $return;
    } else {
        echo 'Erreur pendant l\'ouverture du fichier<br>';
    }
}

/**
 * Fonction qui renvoit le tableau des user depuis le fichier .csv pour l'affichage.
 * chaque user est un tableau associatif.
 * @return $users [[]]
 */
function getUsersToRender()
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
    else {
        echo 'Erreur pendant l\'ouverture du fichier<br>';
    }
    return $users;
}

/**
 * Fonction qui sauvegarde le tableau $users dans le fichier .csv.
 * @param $users {[[]]} : tableau d'user à sauvegarder.
 */
function saveUsers($users)
{
    $newUsers = [];
    foreach ($users as $user) {
        $newUser = [];
        $newUser[0] = $user[0];
        $newUser[1] = $user[1];
        $newUser[2] = $user[2];
        $newUser[3] = $user[3];
        array_push($newUsers, $newUser);
    }
    if ($file = fopen(dirname(__FILE__) . '/../src/datas/users.csv', 'w')) {
        foreach ($newUsers as $user) {
            fputcsv($file, $user, ",");
        }
        fclose($file);
    } else {
        echo 'Erreur pendant l\'ouverture du fichier<br>';
    }
}

/**
 * Fonction qui met à jour dans le fichier .csv l'user passé en paramètre selon l'id.
 * @param $user {[]} : user à mettre à jour.
 */
function updateUser($user)
{
    // charger tous les user
    $id = $user[4];
    $users = getUsers();
    $users[$id][0] = trim($user[0]);
    $users[$id][1] = trim($user[1]);
    $users[$id][2] = trim($user[2]);
    if(trim($user[3]) !== '') {
        $pwd = trim($user[3]);
        $hash = password_hash($pwd, PASSWORD_DEFAULT);
        $users[$id][3] = $hash;
    }
    saveUsers($users);
}

/**
 * Fonction qui ajoute dans le fichier .csv l'user passé en paramètre selon l'id.
 * @param $user [] : user à ajouter.
 */
function addUser($user)
{
    $user[0] = trim($user[0]);
    $user[1] = trim($user[1]);
    $user[2] = trim($user[2]);
    $pwd = trim($user[3]);
    $hash = password_hash($pwd, PASSWORD_DEFAULT);
    $user[3] = $hash;
    $users = getUsers();
    array_push($users, $user);
    saveUsers($users);
}

/**
 * Fonction qui tri le fichier .csv selon $cat et le sauvegarde avec changement du
 * type de tri (asc ou desc) à chaque clic en utilisant 3 variables de session.
 * @param $cat string : critère de tri.
 */
function sortUsers($cat)
{
    $users = getUsers();
    switch ($cat) {
        case "firstname":
            if (!isset($_SESSION['sort_type_firstname'])) {
                $_SESSION['sort_type_firstname'] = 'asc';
            }
            if($_SESSION['sort_type_firstname'] === 'asc') {
                $_SESSION['sort_type_firstname'] = 'desc';
                usort($users, "compareFirstNameAsc");
            }
            elseif($_SESSION['sort_type_firstname'] === 'desc') {
                $_SESSION['sort_type_firstname'] = 'asc';
                usort($users, "compareFirstNameDesc");
            }
            saveUsers($users);
            break;
        case "lastname":
            if (!isset($_SESSION['sort_type_lastname'])) {
                $_SESSION['sort_type_lastname'] = 'asc';
            }
            if($_SESSION['sort_type_lastname'] === 'asc') {
                $_SESSION['sort_type_lastname'] = 'desc';
                usort($users, "compareLastNameAsc");
            }
            elseif($_SESSION['sort_type_lastname'] === 'desc') {
                $_SESSION['sort_type_lastname'] = 'asc';
                usort($users, "compareLastNameDesc");
            }
            saveUsers($users);
            break;
        case "email":
            if (!isset($_SESSION['sort_type_email'])) {
                $_SESSION['sort_type_email'] = 'asc';
            }
            if($_SESSION['sort_type_email'] === 'asc') {
                $_SESSION['sort_type_email'] = 'desc';
                usort($users, "compareEmailAsc");
            }
            elseif($_SESSION['sort_type_email'] === 'desc') {
                $_SESSION['sort_type_email'] = 'asc';
                usort($users, "compareEmailDesc");
            }
            saveUsers($users);
            break;
    }
}

/**
 * Comparator sur le firstname asc.
 */
function compareFirstNameAsc($userA, $userB) {
    return strcmp($userA[0], $userB[0]);
}

/**
 * Comparator sur le lastname asc.
 */
function compareLastNameAsc($userA, $userB) {
    return strcmp($userA[1], $userB[1]);
}

/**
 * Comparator sur l'email' asc.
 */
function compareEmailAsc($userA, $userB) {
    return strcmp($userA[2], $userB[2]);
}
/**
 * Comparator sur le firstname desc.
 */
function compareFirstNameDesc($userA, $userB) {
    return strcmp($userB[0], $userA[0]);
}
/**
 * Comparator sur le lastname desc.
 */
function compareLastNameDesc($userA, $userB) {
    return strcmp($userB[1], $userA[1]);
}

/**
 * Comparator sur l'email' desc.
 */
function compareEmailDesc($userA, $userB) {
    return strcmp($userB[2], $userA[2]);
}
?>