
<?php
session_start();

?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="./favicon.ico" />
  <title>
    <?php 
    echo (isset($_GET['page']) ?  $_GET['page'] : "Titre par défaut !!!");
    ?>
  </title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">

  <style>
    header {
      margin-bottom: 0;
    }
    main {
      margin-top: 220px !important;
    }
  </style>
  <script defer>
    function submitAndResetDiv(e) {
      //e.preventDefault();
      //document.getElementById("form-update-user").submit();
      document.getElementById('div-edit-user').style.display = 'none';
    }
  </script>
</head>
