<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion – GSB</title>
    <link rel="stylesheet" href="<?php echo base_url('public/css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/css/connexion.css'); ?>">
</head>
<body class="page-connexion">

<header class="header">
    <div class="header-left">
        <h1>GSB</h1>
        <span class="subtitle">Application interne – Laboratoire pharmaceutique</span>
    </div>
    <nav class="header-nav">
        <a href="getdata">Accueil</a>
        <a href="getdata?action=notedefrais">Notes de frais</a>
    </nav>
</header>

<main>
    <section class="container small">
        <h2>Connexion à l’espace visiteur</h2>

        <?php if (isset($erreur)): ?>
            <div class="erreur" style="color:red; margin-bottom:10px;">
                <?= htmlspecialchars($erreur) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="postdata">
            <label for="login">Identifiant</label>
            <input type="text" id="login" name="login" required>

            <label for="mdp">Mot de passe</label>
            <input type="password" id="mdp" name="mdp" required>

            <button type="submit" class="btn-primary">Se connecter</button>
        </form>

        <p class="info"><br/>
            Accès réservé aux visiteurs médicaux et comptables de GSB.
        </p>
    </section>
</main>

<footer class="footer">
    <p>&copy; 2026 – Galaxy Swiss Bourdin</p>
    <p>Application interne – Accès réservé aux collaborateurs</p>
</footer>

</body>
</html>