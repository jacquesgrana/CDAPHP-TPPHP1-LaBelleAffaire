<?php
session_start();

function deleteUser($id) {
    // charger csv dans un tableau avec tout
    // enlever du tableau l'element selon l'id
    // ecrire le fichier
    //$_SESSION['delete_message'] = "delete user : id : " . $id;
    $_SESSION['action_type'] = "delete";
    $user = array(
        "firstname" => "John",
        "lastname" => "Doe",
        "email" => "john.doe@example.com"
    );
    $string = implode(",", $user);
    $_SESSION['user_datas'] = $string;
    //return $user;
}

function editUser($id) {
    // appeler fonction qui dessine renvoie les infos de l'user (avec pw)
      //$_SESSION['edit_message'] = "edit user : id : " . $id;
      //$_SESSION['edit_message'] = "edit user : id : " . $id;
      $_SESSION['action_type'] = "edit";
      $user = array(
        "firstname" => "John",
        "lastname" => "Doe",
        "email" => "john.doe@example.com"
    );
    $string = implode(",", $user);
    $_SESSION['user_datas'] = $string;
    //return $user;
}

?>