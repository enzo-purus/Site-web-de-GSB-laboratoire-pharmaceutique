<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>GSB – Renseigner une fiche</title>
    <link rel="stylesheet" href="<?php echo base_url('public/css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/css/renseigner-fiche.css'); ?>">
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

    <main class="container">
        <section class="form-card">
            <h2 class="form-title">Renseigner ma fiche de frais</h2>
            <!-- Choix du mois : ouvre la fiche du mois, ou la crée si elle n'existe pas encore -->
            <form method="get" action="getdata" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin-bottom:10px;">
                <input type="hidden" name="action" value="renseignerfiche">
                <label for="mois"><strong>Mois à renseigner :</strong></label>
                <input type="month" id="mois" name="mois" value="<?= htmlspecialchars($moisLabel) ?>" pattern="\d{4}-(0[1-9]|1[0-2])" placeholder="AAAA-MM" required>
                <button type="submit" class="btn-secondary">Ouvrir ou créer cette fiche</button>
            </form>

            <?php if (!empty($fiches)): ?>
                <p style="margin-bottom:15px;">Mes fiches :
                    <?php foreach ($fiches as $f): ?>
                        <?php if ($f->mois === $moisLabel): ?>
                            <strong style="margin:0 6px;">[<?= htmlspecialchars($f->mois) ?>]</strong>
                        <?php else: ?>
                            <a href="getdata?action=renseignerfiche&amp;mois=<?= urlencode($f->mois) ?>" style="margin:0 6px;"><?= htmlspecialchars($f->mois) ?></a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </p>
            <?php endif; ?>

            <p style="margin-bottom:15px;">Fiche en cours de saisie : <strong><?= htmlspecialchars($moisLabel) ?></strong></p>

            <?php if (isset($erreur)): ?>
                <p style="color:red; margin-bottom:15px;"><?= htmlspecialchars($erreur) ?></p>
            <?php endif; ?>
            <?php if (isset($succes)): ?>
                <p style="color:green; margin-bottom:15px;"><?= htmlspecialchars($succes) ?></p>
            <?php endif; ?>

            <!-- Frais forfaitaires : le contrôleur reçoit km, nuit, etape, repas -->
            <form method="post" action="getdata">
                <input type="hidden" name="mois" value="<?= htmlspecialchars($moisLabel) ?>">
                <h3 style="margin-top:20px;">Frais forfaitaires</h3>
                <div class="form-grid">
                    <div>
                        <label>Kilomètres</label>
                        <input type="number" name="km" min="0" value="<?= (int) ($quantites['KM'] ?? 0) ?>" required>
                    </div>
                    <div>
                        <label>Nuitées</label>
                        <input type="number" name="nuit" min="0" value="<?= (int) ($quantites['NUI'] ?? 0) ?>" required>
                    </div>
                    <div>
                        <label>Étapes</label>
                        <input type="number" name="etape" min="0" value="<?= (int) ($quantites['ETP'] ?? 0) ?>" required>
                    </div>
                    <div>
                        <label>Repas</label>
                        <input type="number" name="repas" min="0" value="<?= (int) ($quantites['REP'] ?? 0) ?>" required>
                    </div>
                </div>
                <button type="submit" class="btn-primary" style="margin-top:15px;">Enregistrer les frais forfaitaires</button>
            </form>

            <hr style="margin:25px 0;">

            <!-- Frais hors forfait : le contrôleur reçoit libelle, date, montant -->
            <h3>Frais hors forfait</h3>

            <?php if (empty($ligneHorsForfait)): ?>
                <p style="color:#666; margin:10px 0;">Aucun frais hors forfait ce mois-ci.</p>
            <?php else: ?>
                <table style="width:100%; margin:10px 0 20px; border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th style="text-align:left;">Date</th>
                            <th style="text-align:left;">Libellé</th>
                            <th style="text-align:right;">Montant</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ligneHorsForfait as $ligne): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($ligne->date)) ?></td>
                            <td><?= htmlspecialchars($ligne->libelle) ?></td>
                            <td style="text-align:right;"><?= number_format($ligne->montant, 2, ',', ' ') ?> €</td>
                            <td style="text-align:right;">
                                <form method="post" action="getdata?action=supprimerhorsforfait" style="display:inline;"
                                      onsubmit="return confirm('Supprimer ce frais hors forfait ?');">
                                    <input type="hidden" name="mois" value="<?= htmlspecialchars($moisLabel) ?>">
                                    <input type="hidden" name="id_hf" value="<?= (int) $ligne->id ?>">
                                    <button type="submit" class="btn-secondary" title="Supprimer ce frais">✕</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <form method="post" action="getdata" class="hors-forfait-item" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                <input type="hidden" name="mois" value="<?= htmlspecialchars($moisLabel) ?>">
                <input type="date" name="date" required>
                <input type="text" name="libelle" placeholder="Libellé" required>
                <input type="number" step="0.01" min="0.01" name="montant" placeholder="Montant (€)" required>
                <button type="submit" class="btn-primary">Ajouter ce frais</button>
            </form>

            <hr style="margin:25px 0;">

            <!-- Suppression de la fiche du mois ouvert (formulaire séparé, jamais imbriqué) -->
            <form method="post" action="getdata?action=supprimerfiche"
                  onsubmit="return confirm('Supprimer définitivement la fiche <?= htmlspecialchars($moisLabel) ?> et tous ses frais ?');">
                <input type="hidden" name="mois" value="<?= htmlspecialchars($moisLabel) ?>">
                <button type="submit" class="btn-secondary" style="color:#b00020;">Supprimer la fiche <?= htmlspecialchars($moisLabel) ?></button>
            </form>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; 2026 – Galaxy Swiss Bourdin</p>
        <p>Application interne – Accès réservé aux collaborateurs</p>
    </footer>
</body>
</html>