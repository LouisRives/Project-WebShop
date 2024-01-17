<?php
include("connexion.php");

// Récupération de l'id du client passé en paramètre de l'URL
if (!isset($_GET['customer_id'])) {
    header('Location: client.php');
    exit();
}
$customer_id = $_GET['customer_id'];

session_start();

// Récupération des commandes du client avec les informations sur le magasin et le statut
$stmt = $conn->prepare("SELECT o.order_id, o.order_status, s.store_name, o.order_date, o.required_date, o.shipped_date
                        FROM orders o
                        JOIN stores s ON o.store_id = s.store_id
                        WHERE o.customer_id = :customer_id
                        ORDER BY o.order_status");
$stmt->bindValue(':customer_id', $customer_id, PDO::PARAM_INT);
$stmt->execute();
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupération des produits pour chaque commande
$stmt_produits = $conn->prepare("SELECT op.order_id, op.product_id, p.product_name, t.category_name
                                 FROM order_items op
                                 JOIN products p ON op.product_id = p.product_id
                                 JOIN types t ON p.category_id = t.category_id
                                 WHERE op.order_id = :order_id");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Liste des commandes</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"> </script> 
    <style>
        .detail {
            display: none;//visible
        }
        .category {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="w3-container">
        <?php if($_SESSION['user_admin'] == true){?>
            <p><a href="client.php" class="w3-button w3-teal">&lt;&lt; Retour à la liste des clients</a></p>
        <?php } ?>
        <h1 class="w3-text-teal">Liste des commandes</h1>
        <!-- <p><a href="client.php" class="w3-button w3-blue">&lt;&lt; Retour à la liste des clients</a></p> -->
        <h2>Commandes pour le client <?php echo $customer_id; if($_SESSION['user_admin'] == true){ echo ' admin';}?></h2>
        <table class="w3-table-all">
            <thead>
                <tr class="w3-teal">
                    <th>ID</th>
                    <th>Statut</th>
                    <th>Magasin</th>
                    <th>Date de commande</th>
                    <th>Date requise</th>
                    <th>Date expédition</th>
                    <th>Détail des produits</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commandes as $commande): ?>
                    <tr>
                        <td><?php echo $commande['order_id']; ?></td>
                        <td><?php echo $commande['order_status']; ?></td>
						<td><?php echo $commande['store_name']; ?></td>
						<td><?php echo $commande['order_date']; ?></td>
						<td><?php echo $commande['required_date']; ?></td>
						<td><?php echo $commande['shipped_date']; ?></td>
						<td><button class="w3-button w3-teal toggle-detail">Afficher les produits</button></td>
					</tr>
					<tr>
						<td colspan="7">
							<div class="detail" id="detail">
								<table class="w3-table-all">
									<thead>
										<tr>
											<th>ID du produit</th>
											<th>Nom du produit</th>
											<th>Catégorie</th>
                                            <?php if($_SESSION['user_admin'] == true){?>
                                            <th>Lien vers les ventes des magasins</th>
                                            <?php } ?>
										</tr>
									</thead>
									<tbody>
										<?php
										$stmt_produits->bindValue(':order_id', $commande['order_id'], PDO::PARAM_INT);
										$stmt_produits->execute();
										$produits = $stmt_produits->fetchAll(PDO::FETCH_ASSOC);
										foreach ($produits as $produit): ?>
											<tr>
												<td><?php echo $produit['product_id']; ?></td>
												<td><?php echo $produit['product_name']; ?></td>
												<td class="category"><?php echo $produit['category_name']; ?></td>
                                                <?php if($_SESSION['user_admin'] == true){?>
                                                <td><a href="magasinsparproduit.php?product_id=<?php echo $produit['product_id']; ?>"><?php echo $produit['product_name']; ?></a></td>
                                                <?php } ?>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
    <script>
        $(document).ready(function() {
            $('button.toggle-detail').click(function() {
                var detail = $(this).closest('tr').next().find('div.detail');
                detail.slideToggle();
                var btnText = $(this).html().trim();
                if (btnText === 'Afficher les produits') {
                    $(this).html('Cacher les produits');
                } else {
                    $(this).html('Afficher les produits');
                }
            });
            $('tr').hover(
                function() {
                    $(this).css('background-color', 'yellow');
                },
                function() {
                    $(this).css('background-color', '');
                }
            );
        });

    </script>
</body>
</html>

<!-- 
La ligne de SQL pour modifier la BDD afin de rajouter une colonne admin est : 

ALTER TABLE customers ADD COLUMN admin BOOLEAN DEFAULT FALSE;
UPDATE customers SET admin=TRUE WHERE first_name = "Burks";

Soit toute la famille Burks est administrateur. 
-->