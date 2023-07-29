<?php
session_start();

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
    //return $user;
}

function editUser($id)
{
    $users = getUsers();
    $_SESSION['action_type'] = "edit";
    $user = $users[$id];
    $string = implode(",", $user);
    $_SESSION['user_datas'] = $string;
}

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

function saveUsers($users)
{
    $newUsers = [];
    foreach($users as $user) {
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

function updateUserInCsv($user) {
    // charger tous les user
    $id = $user[4];
    $users = getUsers();
    $users[$id][0] = $user[0];
    $users[$id][1] = $user[1];
    $users[$id][2] = $user[2];
    saveUsers($users);
}
