<?php
/**
 * Controller for handling API requests, specifically the leaderboard data.
 */

require_once __DIR__ . '/../models/music.php';

class ApiController {
    private Music $music;

    /**
     * Initializes the Music model.
     */
    public function __construct() {
        $this->music = new Music(Database::getInstance());
    }

    /**
     * Retrieves the top songs and returns them as a JSON response (Standard REST API).
     * @return void
     */
    public function getLeaderboard(): void {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $topSongs = $this->music->getTopSongs(10);

            echo json_encode([
                'status' => 'success',
                'data' => $topSongs,
            ]);
        } catch (Exception $e) {
            // Handle database or other exceptions
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
        exit;
    }

    /**
     * Streams the leaderboard data using Server-Sent Events (SSE).
     * Sends new data only if the content has changed (using hash checking).
     * @return void
     */
    public function streamLeaderboard(): void {
        // Set necessary headers for SSE
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');

        // Allow script to run indefinitely
        set_time_limit(0);

        // Close the session to allow other scripts to run concurrently
        session_write_close();
        $lastHash = null;

        while (true) {
            $topSongs = $this->music->getTopSongs(10);

            // Encode data and create a hash to check for changes
            $jsonData = json_encode(['status' => 'success', 'data' => $topSongs]);
            $currentHash = md5($jsonData);

            // Send data only if it has changed since the last poll
            if ($currentHash != $lastHash) {
                // SSE data format: "data: [JSON payload]\n\n"
                echo "data: {$jsonData}\n\n";

                // Ensure data is sent immediately to the client
                if (ob_get_level() > 0) ob_flush();
                flush();

                $lastHash = $currentHash;
            }

            // Exit loop if the client disconnects
            if (connection_aborted()) {
                break;
            }

            // Wait for 3 seconds before checking for updates again
            sleep(3);
        }
        exit;
    }
}