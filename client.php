<?php
include("connexion.php");

session_start();

// Vérifier si le formulaire de connexion a été soumis
if (isset($_POST['submit'])) {
    $id = $_POST['customer_id_form'];
    $first_name = $_POST['first_name_form'];
    $last_name = $_POST['last_name_form'];
    $email = $_POST['email_form'];
    $phone = $_POST['phone_form'];
    $admin = isset($_POST['status']) && $_POST['status'] == 1 ? 1 : 0;
    
    $stmt_verif = $conn->prepare("SELECT * FROM customers WHERE customer_id = :id");
    $stmt_verif->bindParam(':id', $id);
    $stmt_verif->execute();

    if ($stmt_verif->rowCount() > 0) {
        // l'id existe déjà
        $row = $stmt_verif->fetch(PDO::FETCH_ASSOC);
        $admin_verif = $row['admin'];
        // vérifier si le client est un admin ou non
        if ($admin_verif == false) {
        // mise à jour d'un client existant
            $stmt = $conn->prepare("UPDATE customers SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone, admin = :admin WHERE customer_id = :id");
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':admin', $admin, PDO::PARAM_BOOL);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        }
    } else {
        // ajout d'un nouveau client
        $stmt = $conn->prepare("INSERT INTO customers (first_name, last_name, email, phone, street, city, state, zip_code, admin) VALUES (:first_name, :last_name, :email, :phone, NULL, NULL, NULL, NULL, :admin)");
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':admin', $admin, PDO::PARAM_BOOL);
        $stmt->execute();
    }
}

$stmt = $conn->prepare("SELECT customer_id, first_name, last_name, admin FROM customers ORDER BY customer_id");
$stmt->execute();
$clients = $stmt->fetchAll(PDO::FETCH_CLASS, 'Customer');

class Customer {
    public $customer_id;
    public $first_name;
    public $last_name;
    public $admin;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Liste des clients</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"> </script> 

    <style>
        .tr {
            font-size = 16px;
        }
    </style>
</head>
<body>
    <h1>Liste des clients</h1>
    <p><a href="etatdescommandes.php" class="w3-button w3-dark-grey">&lt;&lt; Retour à la liste des états des commandes</a></p>

    <?php if($_SESSION['user_admin'] == true){?>
        <button onclick="document.getElementById('modal').style.display='block'" class="w3-button w3-dark-grey">Ajouter/Modifier client</button>
    <?php } ?>
       
    <table class="w3-table-all">
        <thead>
            <tr class="w3-dark-grey">
                <th>ID</th>
                <th>Prénom</th>
                <th>Nom</th>
                <th>Admin</th>
                <th>Commandes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clients as $client): ?>
                <tr <?php if($client->admin === true){?> class="hover-admin" <?php }?>>
                    <td><?php echo $client->customer_id; ?></td>
                    <td><?php echo $client->first_name; ?></td>
                    <td><?php echo $client->last_name; ?></td>
                    <td><?php echo $client->admin; ?></td>
                    <td><a href="commandes.php?customer_id=<?php echo $client->customer_id; ?>">Voir les commandes</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div id="modal" class="w3-modal">
        <div class="w3-modal-content">
            <div class="w3-container">
                <span onclick="document.getElementById('modal').style.display='none'" class="w3-button w3-display-topright">&times;</span>
                <h2>Ajouter/Modifier client</h2>
                <form method="POST" action="client.php">
                    <label>ID :</label><br>
                    <input type="text" name="R_form" id="customer_id_form" value=""><br><br>
                    <label>Prénom :</label><br>
                    <input type="text" name="first_name_form" value=""><br><br>
                    <label>Nom :</label><br>
                    <input type="text" name="last_name_form" value=""><br><br>
                    <label>Email :</label><br>
                    <input type="text" name="email_form" value=""><br><br>
                    <label>Telephone :</label><br>
                    <input type="text" name="phone_form" value=""><br><br>
                    <label>Statut :</label><br>
                    <select name="status">
                        <option value="0">Client</option>
                        <option value="1">Admin</option>
                    </select><br><br>
                    <input type="submit" name="submit" value="Enregistrer">
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('tr.hover-admin').hover(
                function() {
                    $(this).css('font-size', '32px');
                },
                function() {
                    $(this).css('font-size', '16px');
                }
            );
        });

    </script>
</body>
</html>
