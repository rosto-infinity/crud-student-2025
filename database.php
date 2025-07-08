<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'crud_student_2025');
define('DB_USER', 'valet');  //"root"
define('DB_PASSWORD', 'valet');// ""
try {
  $pdoconnect = new PDO(
    "mysql:host=" . DB_HOST . 
    "; dbname=" . DB_NAME . 
    "; charset=utf8",
    DB_USER,
    DB_PASSWORD
  );
//  echo '
//     <div style="
//         background: #d4edda;
//         color: #155724;
       
//     ">
//         ✅ Connexion réussie à la base de données 
       
//     </div>';
} catch (PDOException $error) {
  echo "Echec de la connexion a la base de donnees :" .
    $error->getMessage();
  exit;
}
?>