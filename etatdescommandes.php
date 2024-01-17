<?php
include("connexion.php");

// Récupération des produits classés par le nombre de ventes
$stmt = $conn->prepare("SELECT p.product_id, p.product_name, COUNT(op.order_id) as nb_ventes
                        FROM products p
                        LEFT JOIN order_items op ON p.product_id = op.product_id
                        GROUP BY p.product_id, p.product_name
                        ORDER BY nb_ventes DESC");
$stmt->execute();
$produits = $stmt->fetchAll(PDO::FETCH_CLASS, "Product");

class Product {
    public $product_id;
    public $product_name;
    public $nb_ventes;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>État des commandes</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>
    <div class="w3-container">
        <h1 class="w3-text-teal">État des commandes</h1>
        <p><a href="client.php" class="w3-button w3-teal">&lt;&lt; Retour à la liste des clients</a></p>
        <table class="w3-table-all">
            <thead>
                <tr class="w3-teal">
                    <th>ID du produit</th>
                    <th>Nom du produit</th>
                    <th>Nombre de ventes</th>
                    <th>Lien vers les ventes des magasins</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produits as $produit): ?>
                    <tr>
                        <td><?php echo $produit->product_id; ?></td>
                        <td><?php echo $produit->product_name; ?></td>
                        <td><?php echo $produit->nb_ventes; ?></td>
                        <td><a href="magasinsparproduit.php?product_id=<?php echo $produit->product_id; ?>"><?php echo $produit->product_name; ?></a></td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
