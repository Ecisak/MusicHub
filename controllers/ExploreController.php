<?php
require_once __DIR__ . '/../models/music.php';

class ExploreController{
    public Music $music;
    public function __construct(){
        $pdo = Database::getInstance();
        $this->music = new Music($pdo);
    }
    public function showExplore($twig) {
        $exploreSongs = $this->music->getAllAccepted();
        echo $twig->render('explore.html.twig', ['exploreSongs' => $exploreSongs]);
    }
}