<?php
require_once "database.php";

if(isset($_GET['id_student'])){
  $id_student =$_GET['id_student'];
  
  $query=$pdoconnect->prepare("DELETE FROM students WHERE id_student=? ");
  $query->execute([$id_student]);
  header('Location: index.php');
  exit();
}else{
  echo "Id invalide ou non spécifié";
}

?>