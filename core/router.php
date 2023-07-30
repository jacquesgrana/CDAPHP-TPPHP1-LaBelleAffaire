<?php
$page = 'accueil.php';

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
