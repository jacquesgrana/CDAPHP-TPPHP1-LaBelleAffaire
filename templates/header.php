<header class="bg-success w-100 m-0 fixed-top d-flex flex-column justify-content-center align-items-center">
  <h1 class="mt-5 mx-5 mb-3 text-center">Association La belle affaire</h1>
  <?php
  //session_start();
if (isset($_SESSION['user']) && $_SESSION['user'] === true) {
  renderUser();
  //echo "\n" . "<button class='brn btn-warning' onclick=''>Déconnexion</button>";
  echo "\n" . "<form class='mt-5 me-5 w-100 d-flex position-fixed justify-content-end' action='index.php?page=accueil&action=logout' method='post'>";
  echo "\n" . "<input class='btn btn-sm btn-warning' type='submit' value='✘ Se Déconnecter'>";
  echo "\n" . "</form>";
}

function renderUser() {
  if(isset($_SESSION['user_firstname']) && isset($_SESSION['user_lastname']) && isset($_SESSION['user_email'])) {
      echo "\n" . "<div class='mb-5 me-5 w-100 d-flex position-fixed justify-content-end align-items-end flex-column'>";
      echo "\n" . "<h6 class=''><span class='text-warning'>" . $_SESSION['user_firstname'] . "</span> • <span class='text-warning'>" . $_SESSION['user_lastname'] . "</span></h6>";
      echo "\n" . "<h6 class='mb-2'><span class='text-warning'>" . $_SESSION['user_email'] . "</span></h6>";
      echo "\n" . "</div>";
  }
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