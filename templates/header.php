<header class="bg-success w-100 m-0 fixed-top d-flex flex-column justify-content-center align-items-center">
  <h1 class="mt-5 mx-5 mb-3 text-center">Association La belle affaire</h1>
  <?php
if (isset($_SESSION['user']) && $_SESSION['user'] === true) {
  //echo "\n" . "<button class='brn btn-warning' onclick=''>Déconnexion</button>";
  echo "\n" . "<form class='mb-3 w-100 d-flex justify-content-end' action='index.php?page=accueil&action=logout' method='post'>";
  echo "\n" . "<input class='btn btn-warning btn-sm me-5' type='submit' value='Se déconnecter'>";
  echo "\n" . "</form>";
}
  ?>
  <nav>
    <ul class="list-unstyled d-flex gap-3 justify-content-center">
      <li><a class="link-dark link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-100-hover" href="/index.php?page=accueil">Accueil</a></li>
      <li><a class="link-dark link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-100-hover" href="/index.php?page=apropos">Qui sommes nous ?</a></li>
      <li><a class="link-dark link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-100-hover" href="/index.php?page=membres">Membres</a></li>
    
    </ul>
  </nav>
</header>