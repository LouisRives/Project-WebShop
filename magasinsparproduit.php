<?php
include("connexion.php");

// Récupération du product_id depuis le paramètre de requête GET
$product_id = $_GET["product_id"];

// Récupération des commandes qui contiennent le produit spécifié
/*$stmt = $conn->prepare("SELECT o.store_id, s.store_name, COUNT(oi.order_id) as nb_commandes
                        FROM orders o
                        INNER JOIN order_items oi ON o.order_id = oi.order_id
                        INNER JOIN stores s ON o.store_id = s.store_id
                        WHERE oi.product_id = :product_id
                        GROUP BY o.store_id, s.store_name
                        ORDER BY nb_commandes DESC");*/
//-- Order status: 1 = Pending; 2 = Processing; 3 = Rejected; 4 = Completed
$stmt = $conn->prepare("SELECT s.store_id, s.store_name, COUNT(DISTINCT oi.order_id) as nb_commandes, SUM(CASE WHEN o.order_status = 2 THEN oi.quantity ELSE 0 END) as nb_produits_en_cours, SUM(st.quantity) as stock_global
FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN products p ON oi.product_id = p.product_id
    JOIN stocks st ON st.product_id = p.product_id AND st.store_id = o.store_id
    JOIN stores s ON s.store_id = o.store_id
    WHERE p.product_id = :product_id
    GROUP BY s.store_id,s.store_name
    ORDER BY nb_commandes DESC;
");
$stmt->bindParam(":product_id", $product_id);
$stmt->execute();
$magasins = $stmt->fetchAll(PDO::FETCH_CLASS, "Magasin");

class Magasin {
    public $store_id;
    public $store_name;
    public $nb_commandes;
    public $nb_produits_en_cours;
    public $stock_global;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Magasins pour produit <?php echo $product_id; ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>
    <div class="w3-container">
        <h1 class="w3-text-teal">Magasins pour produit <?php echo $product_id; ?></h1>
        <p><a href="etatdescommandes.php" class="w3-button w3-teal">&lt;&lt; Retour à la liste des états des commandes</a></p>
        <table class="w3-table-all">
            <thead>
                <tr class="w3-teal">
                    <th>ID du magasin</th>
                    <th>Nom du magasin</th>
                    <th>Nombre de commandes</th>
                    <th>Nombre de produit en cours</th>
                    <th>Stock global</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($magasins as $magasin): ?>
                    <tr>
                        <td><?php echo $magasin->store_id; ?></td>
                        <td><?php echo $magasin->store_name; ?></td>
                        <td><?php echo $magasin->nb_commandes; ?></td>
                        <td><?php echo $magasin->nb_produits_en_cours; ?></td>
                        <td><?php echo $magasin->stock_global; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
