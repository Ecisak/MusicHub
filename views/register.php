<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../public/css/style.css">
    <title>MusicHub - Registrace</title>
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
            </div>
        </div>
        <div class="container">
            <h1 class="reg-header">Registrace</h1>

            <form class="registration-form needs-validation" novalidate method="post" action="/MusicHub/public/index.php?page=register">
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
                    <input type="text" class="form-control" id="InputUsername" name="username" placeholder="pepa123" required>
                    <label for="InputUsername">Přihlašovací jméno</label>
                    <div class="invalid-feedback">Zadej přihlašovací jméno.</div>
                    <div class="valid-feedback">Jméno je v pořádku</div>
                </div>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="InputPassword" name="password" placeholder="heslo" required>
                    <label for="InputPassword">Heslo</label>
                    <div class="invalid-feedback"></div>
                    <div class="valid-feedback">Heslo je dostatečně silné</div>
                </div>

                <div id="passwordHelpBlock" class="form-text mb-3"></div>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="InputPasswordConfirm" name="password_confirm" placeholder="heslo znovu" required>
                    <label for="InputPasswordConfirm">Potvrzení hesla</label>
                    <div class="invalid-feedback"></div>
                    <div class="valid-feedback">Hesla se shodují</div>
                </div>

                <button type="submit" class="btn btn-primary my-btn w-100">Registrovat</button>
                <p class="reg-txt mt-3">Registrován? Klikni <a href="login.php">zde</a></p>
                <?php
                // pokud jsou chyby z controlleru
                if (!empty($_SESSION['errors'])) {
                    echo "<ul>";
                    foreach ($_SESSION['errors'] as $e) {
                        echo "<li>" . htmlspecialchars($e) . "</li>";
                    }
                    echo "</ul>";
                    unset($_SESSION['errors']); // aby se znovu nezobrazily
                }
                ?>
            </form>


        </div>
    </div>
    <script>
        (() => {
            'use strict';

            // debounce pro omezeni poctu requestu
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            async function validateField(field, value, extraData = {}) {
                const formData = new FormData();
                formData.append('field', field);
                formData.append('value', value);

                //pro potvrzeni hesla potrebujeme taky heslo
                if (extraData.password) {
                    formData.append('password', extraData.password);
                }
                try {
                    const response = await fetch('/MusicHub/public/index.php?page=validate', {
                        method: 'POST',
                        body: formData
                    });
                    const text = await response.text();
                    console.log('Server response:', text);

                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        console.error('Invalid JSON response:', text);
                        return {valid: false, message: 'chyba při validaci'}
                    }
                } catch (error) {
                    console.error('Validation error:', error);
                    return {valid: false, message: "chyba při validaci"}
                }
            }

            function showValidationResult(input, result) {
                const invalidFeedback = input.parentElement.querySelector('.invalid-feedback');
                if (result.valid) {
                    input.classList.remove('is-invalid');
                    input.classList.add('is-valid');
                } else {
                    input.classList.remove('is-valid');
                    input.classList.add('is-invalid');
                    if (invalidFeedback) {
                        invalidFeedback.textContent = result.message;
                    }
                }
            }

            //email validace
            const emailInput = document.getElementById('InputEmail');
            const debouncedEmailValidation = debounce(async (e) => {
                const value = e.target.value.trim();
                if (value) {
                    const result = await validateField('email', value);
                    showValidationResult(emailInput, result);
                }
            }, 500);
            emailInput.addEventListener('input', debouncedEmailValidation);

            //username validace
            const usernameInput = document.getElementById('InputUsername');
            const debouncedUsernameValidation = debounce(async (e) => {
                const value = e.target.value.trim();
                if (value) {
                    const result = await validateField('username', value);
                    showValidationResult(usernameInput, result);
                }
            }, 500);
            usernameInput.addEventListener('input', debouncedUsernameValidation);

            //validace hesla
            const passwordInput = document.getElementById('InputPassword');
            const debouncedPasswordValidation = debounce(async (e) => {
                const value = e.target.value;
                if (value) {
                    const result = await validateField('password', value);
                    showValidationResult(passwordInput, result);

                    // Také zkontroluj potvrzení hesla, pokud je vyplněné
                    const confirmValue = document.getElementById('InputPasswordConfirm').value;
                    if (confirmValue) {
                        validatePasswordConfirm();
                    }
                }
            }, 500);

            passwordInput.addEventListener('input', debouncedPasswordValidation);

            //confirm password validace
            const passwordConfirmInput = document.getElementById('InputPasswordConfirm');

            async function validatePasswordConfirm() {
                const value = passwordConfirmInput.value;
                const password = passwordInput.value;
                if (value) {
                    const result = await validateField('password_confirm', value, {password});
                    showValidationResult(passwordConfirmInput, result);
                }
            }

            const debouncedPasswordConfirmValidation = debounce(validatePasswordConfirm, 300);

            passwordConfirmInput.addEventListener('input', debouncedPasswordConfirmValidation);

            const forms = document.querySelectorAll('.needs-validation');

            const submitBtn = document.querySelector('.my-btn'); // tvoje tlačítko Registrovat

            async function checkFormValidity() {
                const email = emailInput.value.trim();
                const username = usernameInput.value.trim();
                const password = passwordInput.value;
                const passwordConfirm = passwordConfirmInput.value;

                // validuj všechna pole
                const [emailResult, usernameResult, passwordResult, passwordConfirmResult] = await Promise.all([
                    validateField('email', email),
                    validateField('username', username),
                    validateField('password', password),
                    validateField('password_confirm', passwordConfirm, { password })
                ]);

                showValidationResult(emailInput, emailResult);
                showValidationResult(usernameInput, usernameResult);
                showValidationResult(passwordInput, passwordResult);
                showValidationResult(passwordConfirmInput, passwordConfirmResult);

                // pokud je vše validní, tlačítko se odemkne
                const allValid = emailResult.valid && usernameResult.valid && passwordResult.valid && passwordConfirmResult.valid;
                submitBtn.disabled = !allValid;

                return allValid;
            }

            Array.from(forms).forEach(form => {
                // při psaní do polí zkontroluj validitu celé formy
                form.addEventListener('input', debounce(checkFormValidity, 400));

                form.addEventListener('submit', async (event) => {
                    event.preventDefault();
                    event.stopPropagation();

                    const isValid = await checkFormValidity();

                    if (isValid) {
                        form.classList.add('was-validated');
                        form.submit();
                    } else {
                        form.classList.remove('was-validated');
                    }
                });
            });
        })();
    </script>
</body>
<footer class="footer">
    <p class="footer-text">made with &hearts; by <a href>Ecis</a></p>
</footer>
</html>



