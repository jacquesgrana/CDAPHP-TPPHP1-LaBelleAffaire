<?php
//session_start();
require_once(dirname(__FILE__) . '/../core/security.php');


echo "\n" . '<h2 class="text-center mb-5">Accueil</h2>';
echo "\n" . '<p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Fuga id ex minus sint accusamus dicta ipsam quidem adipisci. Numquam dolorum aliquam ipsam perspiciatis recusandae porro ut fuga corporis explicabo veritatis voluptate, blanditiis voluptas quidem hic odit reiciendis atque, dolore quaerat quasi magnam quae? Sit, soluta eligendi dignissimos velit ipsam unde?</p>';


if (isset($_SESSION['user']) && $_SESSION['user'] === true) {
    if (isset($_GET["action"]) && isset($_GET["page"])) {
      if ($_GET["action"] === "logout" && $_GET["page"] === "accueil") {
        //if (isset($_SESSION['user']) && $_SESSION['user'] === true) {
        disconnect();
        //header('location: http://' . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php?page=accueil');
        redirect('http://' . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . '/index.php?page=accueil');
        //exit();
        //}
      }
    }
  }

  function redirect($url) {
    echo '<script type="text/javascript"> window.location="' . $url . '";</script>';
}
?>