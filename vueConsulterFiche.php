<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>GSB – Consulter mes fiches</title>
    <link rel="stylesheet" href="<?php echo base_url('public/css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/css/consulter-fiche.css'); ?>">
</head>
<body>
<header class="header">
    <div class="header-left">
        <h1>GSB</h1>
        <span class="subtitle">Application interne – Laboratoire pharmaceutique</span>
    </div>
    <nav class="header-nav">
        <a href="getdata">Accueil</a>
        <a href="getdata?action=notedefrais">Notes de frais</a>
        <?php if (isset($_SESSION['id'])): ?>
            <a href="getdata?action=monprofil">Mon profil</a>
        <?php endif; ?>
    </nav>
    <div class="header-right">
        <?php if (isset($_SESSION['id'])): ?>
            <span class="login-btn">
                👤 <?= htmlspecialchars($_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom']) ?>
            </span>
            <a href="getdata?action=deconnexion" class="login-btn" style="margin-left:8px;">Déconnexion</a>
        <?php else: ?>
            <a href="getdata?action=connexion" class="login-btn">Connexion</a>
        <?php endif; ?>
    </div>
</header>

<main style="max-width:1100px; margin:40px auto; padding:0 20px;">
    <h2 style="text-align:center; color:#0a2a66; margin-bottom:30px;">Consulter mes fiches de frais</h2>

    <!-- Sélection du mois -->
    <div style="background:white; padding:25px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.05); margin-bottom:30px;">
        <form method="get" action="getdata" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
            <input type="hidden" name="action" value="consulterficheselection">
            <label style="font-weight:bold; color:#0a2a66;">Sélectionnez un mois :</label>
            <select name="mois" style="padding:8px 12px; border:1px solid #ddd; border-radius:6px; font-size:14px;">
                <option value="">-- Choisir un mois --</option>
                <?php foreach ($fiches as $f): ?>
                    <option value="<?= htmlspecialchars($f->mois) ?>" <?= ($f->mois === $moisLabel) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($f->mois) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn-primary">Consulter</button>
        </form>
    </div>

    <!-- Détail de la fiche -->
    <div style="background:white; padding:25px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.05);">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
            <h3 style="color:#0a2a66; margin:0;">Fiche de <?= $moisLabel ?></h3>
        </div>

        <!-- Frais forfaitisés -->
        <h4 style="color:#0a2a66; margin-bottom:10px;">Frais forfaitisés</h4>
        <?php if (empty($ligneForfait)): ?>
            <p style="color:#666; margin-bottom:20px;">Aucun frais forfaitisé.</p>
        <?php else: ?>
        <table class="table-fiches" style="margin-bottom:25px;">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Montant unitaire</th>
                    <th>Quantité</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ligneForfait as $ligne): ?>
                <tr>
                    <td><?= htmlspecialchars($ligne->libelle) ?></td>
                    <td><?= number_format($ligne->montant, 2, ',', ' ') ?> €</td>
                    <td><?= $ligne->quantite ?></td>
                    <td><?= number_format($ligne->total, 2, ',', ' ') ?> €</td>
                </tr>
                <?php endforeach; ?>
                <tr style="font-weight:bold; background:#f0f4fa;">
                    <td colspan="3">Total frais forfaitisés</td>
                    <td><?= number_format($totalForfait, 2, ',', ' ') ?> €</td>
                </tr>
            </tbody>
        </table>
        <?php endif; ?>

        <!-- Frais hors forfait -->
        <h4 style="color:#0a2a66; margin-bottom:10px;">Frais hors forfait</h4>
        <?php if (empty($ligneHorsForfait)): ?>
            <p style="color:#666; margin-bottom:20px;">Aucun frais hors forfait.</p>
        <?php else: ?>
        <table class="table-fiches" style="margin-bottom:25px;">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Libellé</th>
                    <th>Montant</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ligneHorsForfait as $ligne): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($ligne->date)) ?></td>
                    <td><?= htmlspecialchars($ligne->libelle) ?></td>
                    <td><?= number_format($ligne->montant, 2, ',', ' ') ?> €</td>
                </tr>
                <?php endforeach; ?>
                <tr style="font-weight:bold; background:#f0f4fa;">
                    <td colspan="2">Total frais hors forfait</td>
                    <td><?= number_format($totalHorsForfait, 2, ',', ' ') ?> €</td>
                </tr>
            </tbody>
        </table>
        <?php endif; ?>

        <!-- Total général -->
        <div style="text-align:right; font-size:18px; font-weight:bold; color:#0a2a66; padding:15px; background:#f0f4fa; border-radius:8px;">
            Total général : <?= number_format($totalForfait + $totalHorsForfait, 2, ',', ' ') ?> €
        </div>

    </div>

</main>

<footer class="footer">
    <p>&copy; 2026 – Galaxy Swiss Bourdin</p>
    <p>Application interne – Accès réservé aux collaborateurs</p>
</footer>
</body>
</html>