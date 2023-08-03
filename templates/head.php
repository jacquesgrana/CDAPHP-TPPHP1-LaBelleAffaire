<?php
//session_start();
?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="./favicon.ico" />
  <title>
    <?php
    echo (isset($_GET['page']) ?  $_GET['page'] : "accueil");
    ?>
  </title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">

  <style>
    header {
      margin-bottom: 0;
    }

    main {
      margin-top: 220px !important;
      margin-bottom: 160px;
    }

    .div-form {
      background-color: #e3f9eb;
    }

    .input-form {
      border-radius: 5px;
      border: gray 1px solid;
      height: 30px;
      width: 260px;
      background-color: #f2fcf6;
    }

    .table-striped>tbody>tr:nth-child(odd)>td, 
    .table-striped>tbody>tr:nth-child(odd)>th {
   background-color: #e3f9eb;
 }
    .table-striped>tbody>tr:nth-child(even)>td, 
    .table-striped>tbody>tr:nth-child(even)>th {
   background-color: #f2fcf6;
 }
  </style>
</head>