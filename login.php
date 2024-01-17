<?php
include("connexion.php");

session_start(); // Démarrer la session PHP

// Vérifier si le formulaire de connexion a été soumis
if(isset($_POST['submit'])) {
    
    // Récupérer les données du formulaire
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Vérifier si les champs ont été saisis
    if(empty($username) || empty($password)) {
        $error = "Veuillez saisir votre nom et votre identifiant.";
    }
    else {
        // Vérifier si l'utilisateur existe dans la base de données
        $sql = "SELECT customer_id, first_name, last_name, admin FROM customers WHERE CONCAT(first_name, ' ', last_name) = ? AND customer_id = ?"; //"Concat" permet de récupérer le first_name et le last_name dans l'unique variable $username
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username, $password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($user && $user['admin']) {
            // Enregistrer les données de l'utilisateur dans la session PHP
            $_SESSION['user_id'] = $user['customer_id'];
            $_SESSION['user_name'] = $user['first_name'] . " " . $user['last_name'];
            $_SESSION['user_admin'] = true;
            
            header('Location: commandes.php?customer_id=' . $_SESSION['user_id']); // Rediriger l'utilisateur vers sa page de commande
            exit();
        }
        elseif($user) {
            // Enregistrer les données de l'utilisateur dans la session PHP
            $_SESSION['user_id'] = $user['customer_id'];
            $_SESSION['user_name'] = $user['first_name'] . " " . $user['last_name'];
            $_SESSION['user_admin'] = false;

            
            header('Location: commandes.php?customer_id=' . $_SESSION['user_id']); // Rediriger l'utilisateur vers sa page  de commande
            exit();
        }
        // Vérifier si l'utilisateur existe et s'il est un admin

        else {
            $error = "Nom d'utilisateur ou identifiant incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Connexion</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>
    <div class="w3-container">
        <h1 class="w3-text-blue">Connexion :</h1>
        <?php
        if(isset($error)) {
            echo '<p style="color:red;">' . $error . '</p>'; // Afficher le message d'erreur si une erreur s'est produite
        }
        ?>
        <form method="post" class="w3-form" action="">
            <label for="username">Nom d'utilisateur:</label>
            <input type="text" id="username" name="username"><br><br>
            <label for="password">Mot de passe:</label>
            <input type="password" id="password" name="password"><br><br>
            <input type="submit" name="submit" value="Se connecter" class="w3-blue w3-button">
        </form>
    </div>
</body>
</html>
