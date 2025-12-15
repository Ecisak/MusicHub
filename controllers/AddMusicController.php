<?php
/**
 * Controller for handling music submission (upload) functionality.
 */

// Define maximum allowed upload size (25 MB)
const maxUploadSize = 2.5e+7;

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/genre.php';
require_once __DIR__ . '/../models/music.php';

class AddMusicController {

    /**
     * Renders the music upload form.
     * @param Environment $twig Twig environment instance.
     * @return void
     */
    public function showForm($twig): void {
        // Generate CSRF token if one does not exist
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $genreModel = new genre(Database::getInstance());
        $genres = $genreModel->getAll();

        // Retrieve and clear old form data and errors from session (for repopulating the form on validation failure)
        $oldData = $_SESSION['old_data'] ?? [];
        unset($_SESSION['old_data']);

        $errors = $_SESSION['errors'] ?? '';
        unset($_SESSION['errors']);

        echo $twig->render('add_music.html.twig', [
            'csrf_token' => $_SESSION['csrf_token'] ?? '',
            'errors' => $errors,
            'genres' => $genres ?? [],
            'old_data' => $oldData
        ]);
    }

    /**
     * Processes the music upload and form submission.
     * @return void
     */
    public function addMusic(): void {
        $errors = [];

        // 1. CSRF Protection Check
        if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf'])) {
            $errors['general'] = "Chyba zabezpečení (CSRF token)";
        }

        // 2. Data Sanitization and Basic Validation
        $title = trim($_POST['title']) ?? '';
        $artist = trim($_POST['artist']) ?? '';
        $genre_id = $_POST['genre-id'] ?? '';
        $release_year = trim($_POST['release-year']) ?? '';

        if (empty($title)) {
            $errors['title'] = 'Název skladby je povinný';
        }
        if (empty($artist)) {
            $errors['artist'] = 'Interpret je povinný';
        }
        if (empty($genre_id)) {
            $errors['genre_id'] = 'Zvolení žánru je povinné';
        }
        if (empty($release_year)) {
            $errors["release-year"] = "rok vydání je povinné";
        }

        // 3. Cover Image File Handling and Validation
        $allowedExtensionsCover = ['jpg', 'jpeg', 'webp', 'png'];
        $newImageName = null;
        $imagePath = null;

        if (isset($_FILES['coverImage']) && $_FILES['coverImage']['error'] === UPLOAD_ERR_OK) {
            $coverImage = $_FILES['coverImage'];
            $uploadedExtension = strtolower(pathinfo($coverImage['name'], PATHINFO_EXTENSION));
            $newImageName = uniqid('cover_', true) . '.' . $uploadedExtension;
            $imagePath = __DIR__ . '/../public/uploads/covers/' . $newImageName;

            if ($coverImage['size'] >= maxUploadSize) {
                $errors['coverImage'] = "Příliš velký soubor, max je 25MB";
            }
            if (!in_array($uploadedExtension, $allowedExtensionsCover)) {
                $errors['coverImage'] = "soubor ma nepovolený typ:". $uploadedExtension .". povolene jsou jpg, jpeg, png a webp.";
            }
        } else {
            // Error if no file was uploaded (e.g., UPLOAD_ERR_NO_FILE)
            if (!isset($_FILES['coverImage']) || $_FILES['coverImage']['error'] !== UPLOAD_ERR_NO_FILE) {
                $errors['coverImage'] = "Obal alba je povinný.";
            }
        }

        // 4. Music File Handling and Validation
        $allowedExtensionsMusic = ["mp3", "m4a", "ogg", "wav", "flac"];
        $newMusicName = null;
        $musicPath = null;

        if (isset($_FILES['musicUpload']) && $_FILES['musicUpload']['error'] === UPLOAD_ERR_OK) {
            $music_upload = $_FILES['musicUpload'];
            $uploadedExtension = strtolower(pathinfo($music_upload['name'], PATHINFO_EXTENSION));
            $newMusicName = uniqid('music_', true) . '.' . $uploadedExtension;
            $musicPath = __DIR__ . '/../public/uploads/music/' . $newMusicName;

            if ($music_upload['size'] >= maxUploadSize) {
                $errors['musicUpload'] = "Příliš velký soubor, max je 25MB";
            }
            if (!in_array($uploadedExtension, $allowedExtensionsMusic)) {
                $errors['musicUpload'] = "soubor má nepovolený typ: ". $uploadedExtension .". Povolene jsou mp3, mp4a, ogg, wav a flac.";
            }
        } else {
            // Error if no file was uploaded
            if (!isset($_FILES['musicUpload']) || $_FILES['musicUpload']['error'] !== UPLOAD_ERR_NO_FILE) {
                $errors['musicUpload'] = "Zvuková stopa je povinná.";
            }
        }

        // 5. Handle Validation Errors
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;

            // Store old data to repopulate the form
            $_SESSION['old_data'] = [
                'title' => $title,
                'artist' => $artist,
                'genre_id' => $genre_id,
                'release-year' => $release_year
            ];

            header("Location: /MusicHub/public/index.php?page=add");
            exit;

        } else {
            // 6. File Upload and Database Insertion

            $songData = [
                'author' => $artist,
                'coverImage' => $newImageName, // DB column is cover_image
                'filename' => $newMusicName,
                'id_genre' => $genre_id,
                'id_user'=> $_SESSION['user_id'],
                'release_year' => $release_year,
                'status' => 'pending',
                'title' => $title,
                'uploaded_at' => date("Y-m-d H:i:s")
            ];

            // Attempt to move uploaded files
            if (move_uploaded_file($_FILES['coverImage']['tmp_name'], $imagePath) && move_uploaded_file($_FILES['musicUpload']['tmp_name'], $musicPath)) {

                // File move successful, attempt DB insert
                try {
                    $music = new Music(Database::getInstance());
                    $music->create($songData);
                } catch (PDOException $e) {
                    // DB INSERTION ERROR: Rollback by deleting files
                    unlink($imagePath);
                    unlink($musicPath);

                    $_SESSION['flash'] = [
                        'message' => "Písnička nebyla nahrána kvůli chybě databáze. Kontaktujte správce.",
                        'type' => 'danger'
                    ];
                    error_log("[FATAL] DB ERROR on upload: ". $e->getMessage() . "\n", 3, __DIR__ . '/../logs/music_log.log');
                    header("Location: /MusicHub/public/index.php?page=add");
                    exit;
                }

                // SUCCESS
                $_SESSION['flash'] = [
                    'message' => "Písnička byla nahrána a čeká na schválení.",
                    'type' => 'success'
                ];
                header("Location: /MusicHub/public/index.php?page=home");
                exit;

            } else {
                // FILE MOVE ERROR: Delete any potentially moved/created file entries
                if (file_exists($imagePath)) unlink($imagePath);
                if (file_exists($musicPath)) unlink($musicPath);

                $_SESSION['flash'] = [
                    'message' => "Písnička nebyla nahrána. Chyba serveru při ukládání souborů. Zkontrolujte velikost souboru.",
                    'type' => 'danger'
                ];
                header("Location: /MusicHub/public/index.php?page=add");
                exit;
            }
        }
    }
}