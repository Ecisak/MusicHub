<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../public/css/style.css">
    <title>MusicHub - Přihlášení</title>
    <link rel="apple-touch-icon" sizes="180x180" href="../public/images/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../public/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../public/images/favicon-16x16.png">
    <link rel="manifest" href="../public/images/site.webmanifest">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
</head>
<body>
    <div class="background">
        <div class="navbar">
            <div class="logo">
                <img src="../public/images/MusicHub..svg" alt="logo">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <p>Vítej, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
                    <a href="/MusicHub/public/index.php?page=logout">Odhlásit se</a>
                <?php else: ?>
                    <a href="/MusicHub/public/index.php?page=login">Přihlásit se</a>
                <?php endif; ?>

            </div>
        </div>
        <?php if (!empty($_SESSION['errors'])): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($_SESSION['errors'] as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>
        <div class="container">
            <h1 class="reg-header">Přihlášení</h1>

            <form class="login-form needs-validation" novalidate method="post" action="/MusicHub/public/index.php?page=login">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="InputEmail" name="email" placeholder="pepazdepa@seznam.cz" required>
                    <label for="InputEmail">Email address</label>
                    <div class="invalid-feedback">Zadej platný e-mail.</div>
                    <div class="valid-feedback">Email je v pořádku</div>
                </div>

                <div id="emailHelp" class="form-text mb-3">
We'll never share your email with anyone else.
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="InputPassword" name="password" placeholder="heslo" required>
                    <label for="InputPassword">Heslo</label>
                    <div class="invalid-feedback"></div>
                    <div class="valid-feedback">Heslo je dostatečně silné</div>
                </div>

                <div id="passwordHelpBlock" class="form-text mb-3"></div>


                <button type="submit" class="btn btn-primary my-btn w-100">Přihlásit se</button>
                <p class="reg-txt mt-3">Nejsi registrován? Klikni <a href="/MusicHub/public/index.php?page=register">zde</a></p>
            </form>

        </div>
    </div>
</body>
<footer class="footer">
    <p class="footer-text">made with &hearts; by <a href>Ecis</a></p>
</footer>
</html>



