<?php
include "database.php";

  $errors =[];

  $id_student = $_GET['id_student'] ?? '';
$query="SELECT * FROM students WHERE id_student=?";
$req = $pdoconnect->prepare($query);
$req->execute([$id_student]);
$student = $req->fetch();

if(!$student){
    die("Edudiant introuvable");
}

// Vérification de la soumission du formulaire
 if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])){
// Récupération et nettoyage des données
    $nom_student = trim($_POST['nom_student'] ?? '');
    $email_student = trim($_POST['email_student'] ?? '');
    $password_student = trim($_POST['password_student'] ?? ''); 
    $password_confirm = trim($_POST['password_confirm'] ?? ''); 

    //Validation du nom
    if(empty($nom_student)){
        $errors['nom_student'] = 'Le nom est obligatoire';
    }elseif (strlen($nom_student) < 3) {
        $errors['nom_student'] ="Le nom doit contenir au moins 3 caractères";
    }elseif(preg_match('/^\d/', $nom_student)){
        $errors['nom_student'] ="Le nom ne doit pas commecer par un chiffre";
    }else{
        $query ="SELECT *FROM students WHERE nom_student=? AND id_student !=?";
        $req =$pdoconnect->prepare($query);
        $req->execute([$nom_student, $id_student]);
        $student=$req->fetch();
        if($student){
             $errors['nom_student'] = 'Ce nom existe deja';
        }
       
    }

    //Validation email
    if(empty($email_student)){
        $errors['email_student'] = 'Le email est obligatoire';
  }elseif(!filter_var($email_student, FILTER_VALIDATE_EMAIL)){
      $errors['email_student'] = "L'email n'est pas valide";
    }else{
        $query ="SELECT *FROM students WHERE email_student=? AND id_student !=?";
        $req =$pdoconnect->prepare($query);
        $req->execute([$email_student, $id_student]);
        $student=$req->fetch();
        if($student){
             $errors['email_student'] = 'Ce email existe deja';
        }
    }

     // Validation du mot de passe
   if (!empty($password_student) && strlen($password_student) < 8) {
        $errors['password_student'] = "Le mot de passe doit contenir au moins 8 caractères";
    }elseif($password_student !==  $password_confirm){
         $errors['password_student'] = "Le mot de passe ne correspond pas ";
    }

 // Si aucune erreur, mise à jour in database
     if (empty($errors)) {
        $sql = "UPDATE students SET  nom_student = ?, email_student = ?" .  (!empty($password_student) ? ", password_student = ?" : "") .  " WHERE id_student = ?";

        $stmt = $pdoconnect->prepare($sql);
        $params = [$nom_student, $email_student];
        if (!empty($password_student)) {
            $params[] = password_hash($password_student, PASSWORD_DEFAULT); // Hachage du mot de passe
        }
        $params[] = $id_student; // Ajouter l'ID de l'étudiant
        $stmt->execute($params);
        header("Location: index.php");
        exit();
    }

 }

?>




<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création Étudiant | Minimaliste</title>
    <style>
        :root {
            --primary: #7B2CBF;
            --text: #2D3748;
            --border: #E2E8F0;
            --bg: #F8FAFC;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background: var(--bg);
            display: grid;
            place-items: center;
            min-height: 100vh;
            padding: 1rem;
            line-height: 1.5;
        }

        .form-container {
            background: white;
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        h1 {
            color: var(--primary);
            font-size: 1.5rem;
            font-weight: 500;
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        label {
            display: block;
            color: var(--text);
            font-size: 0.875rem;
            margin-bottom: 0.375rem;
        }

        input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 0.375rem;
            font-size: 0.9375rem;
            transition: border 0.2s;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
        }

        input::placeholder {
            color: #A0AEC0;
        }

        .submit-btn {
            width: 100%;
            padding: 0.875rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 0.375rem;
            font-size: 0.9375rem;
            font-weight: 500;
            margin-top: 0.5rem;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .submit-btn:hover {
            opacity: 0.9;
        }
    </style>
</head>

<body>
    <div class="form-container">

        <a href="index.php">
            <button class="submit-btn">
                Retour
            </button>
        </a>

        <h1>Création d'un étudiant</h1>

        <form action='' method="POST">
            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom_student" 
                value="<?= isset($nom_student) ? $nom_student : $student['nom_student'] ?>"
                placeholder="Entrez le nom">
                <?php if (isset($errors['nom_student'])): ?>
                    <p style='color:red;'><?= $errors['nom_student'] ?></p>
                <?php endif; ?>

            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" id="email" 
                value="<?= isset($email_student) ? $email_student : $student['email_student']  ?>"
                name="email_student" placeholder="Entrez l'email">

                 <?php if (isset($errors['email_student'])): ?>
                    <p style='color:red;'><?= $errors['email_student'] ?></p>
                <?php endif; ?>

            </div>
            <div class="form-group">
                <label for="password">password</label>
                <input type="password" id="password" name="password_student" placeholder="Entrez l'password">
            
                 <?php if (isset($errors['password_student'])): ?>
                    <p style='color:red;'><?= $errors['password_student'] ?></p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password_confirm">Password Confirm</label>
                <input type="password" id="password_confirm" name="password_confirm" placeholder="Entrez l'password">
            </div>

            <button type="submit" name="update" class="submit-btn">Modifier</button>
        </form>

    </div>
</body>

</html>