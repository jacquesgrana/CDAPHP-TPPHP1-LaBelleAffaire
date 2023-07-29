<?php
session_start();
require_once(dirname(__FILE__) . '/../core/security.php');
require_once(dirname(__FILE__) . '/../core/csv-manipulator.php');
$page = 'accueil.php';


if (isset($_SESSION['user']) && $_SESSION['user'] === true) {
  if (isset($_GET["action"]) && isset($_GET["page"])) {
    if ($_GET["action"] === "logout" && $_GET["page"] === "accueil") {
      //if (isset($_SESSION['user']) && $_SESSION['user'] === true) {
      disconnect();
      header('location: http://' . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php?page=accueil');
      exit();
      //}
    }
  }


  if (isset($_GET["page"]) && isset($_GET["action"]) && isset($_GET["index"])) {
    $id = $_GET["index"];
    if ($_GET["action"] === "edit" && $_GET["page"] === "membres") {
      //$page = 'membres.php';
      
      editUser($id);
      //$string = implode(",", $user);
      //$_SESSION['user'] = $tring;
      header('location: http://' . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php?page=membres');
      exit();
    } elseif ($_GET["action"] === "delete" && $_GET["page"] === "membres") {
      
      //$page = 'membres.php';
      
      deleteUser($id);
      //$string = implode(",", $user);
      //$_SESSION['user'] = $tring;
      header('location: http://' . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php?page=membres');
      exit();
    }
  }
}


// index.php?page=membres&action=delete&id=". $cpt ."

if (isset($_GET["page"])) {
  switch ($_GET["page"]) {
    case 'membres':
      $page = 'membres.php';
      break;
    case 'apropos':
      $page = 'apropos.php';
      break;
    default:
      $page = 'accueil.php';
      break;
  }
}

require_once(dirname(__FILE__) . '/../pages/' . $page);
