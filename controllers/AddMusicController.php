<?php
const maxUploadSize = 2.5e+7;
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/genre.php';
require_once __DIR__ . '/../models/music.php';
class AddMusicController {
    public function showForm($twig):void {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $genre = new genre(Database::getInstance());
        $genres = $genre->getAll();
        echo $twig->render('add_music.html.twig', [
            'csrf_token' => $_SESSION['csrf_token'] ?? '',
            'errors' => $_SESSION['errors'] ??'',
            'genres' => $genres ??[]
        ]);
        unset($_SESSION['errors']);


// TODO: test the controller and the form
    }
    public function addMusic():void {
        $errors = [];

        if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf'])) {
            $errors[] = "CSRF token error";
        }
        $title = trim($_POST['title']) ?? '';
        $artist = trim($_POST['artist']) ?? '';
        $genre_id = $_POST['genre-id'] ?? '';
        $release_year = trim($_POST['release-year']) ?? '';
        $allowedExtensionsCover = ['jpg', 'jpeg', 'webp', 'png'];
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
            $errors["release-year"] = "rok vydani je povinny";
        }
        $newImageName = null;
        $imagePath = null;
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $cover_image = $_FILES['cover_image'];
            $uploadedExtension = strtolower(pathinfo($cover_image['name'], PATHINFO_EXTENSION));
            $newImageName = uniqid('cover_', true) . '.' . $uploadedExtension;
            $imagePath = __DIR__ . '/../public/images/covers/' . $newImageName;
            if ($cover_image['size'] >= maxUploadSize) {
                $errors['cover_image'] = "Příliš velký soubor, max je 25MB";
            }
            if (!in_array($uploadedExtension, $allowedExtensionsCover)) {
                $errors['cover_image'] = "soubor ma nepovolený typ:". $uploadedExtension .". povolene jsou jpg, jpeg, png a webp.";
            }

        }
        $newMusicName = null;
        $musicPath = null;
        if (isset($_FILES['musicUpload']) && $_FILES['musicUpload']['error'] === UPLOAD_ERR_OK) {
            $music_upload = $_FILES['musicUpload'];
            $uploadedExtension = strtolower(pathinfo($music_upload['name'], PATHINFO_EXTENSION));
            $allowedExtensionsMusic = ["mp3", "m4a", "ogg", "wav", "flac"];
            $newMusicName = uniqid('music_', true) . '.' . $uploadedExtension;
            $musicPath = __DIR__ . '/../public/uploads/music/' . $newMusicName;
            if ($music_upload['size'] >= maxUploadSize) {
                $errors['musicUpload'] = "Příliš velký soubor, max je 25MB";
            }
            if (!in_array($uploadedExtension, $allowedExtensionsMusic)) {
                $errors['musicUpload'] = "soubor má nepovolený typ: ". $uploadedExtension .". Povolene jsou mp3, mp4a, ogg, wav a flac.";
            }
        }
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header("Location: /MusicHub/public/index.php?page=add");
            exit;
        } else {
            // VŠE JE OK, Jdeme přesouvat a ukládat

            if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $imagePath) && move_uploaded_file($_FILES['musicUpload']['tmp_name'], $musicPath)) {
                // ÚSPĚCH! Soubory jsou na místě.
                // Zde přijde kód pro uložení do DB
                // Vytvoříme pole $songData s $title, $artist, $newImageName, $newMusicName atd.
                // $musicModel = new Music(Database::getInstance());
                // $musicModel->create($songData);
                $songData = [
                    'author' => $artist,
                    'cover_image' => $newImageName,
                    'filename' => $newMusicName,
                    'id_genre' => $genre_id,
                    'id_user'=> $_SESSION['user_id'],
                    'release_year' => $release_year,
                    'status' => 'pending',
                    'title' => $title,
                    'uploaded_at' => date("Y-m-d H:i:s")
                ];
                // A přesměrujeme
                $music = new Music(Database::getInstance());
                $music->create($songData);
                header("Location: /MusicHub/public/index.php?page=home&status=upload_success");
                exit;
            } else {
                unlink($imagePath);
                unlink($musicPath);
                $_SESSION['errors']['general'] = "Chyba serveru při přesouvání souborů.";
                header("Location: /MusicHub/public/index.php?page=add");
                exit;
            }
        }
    }
}