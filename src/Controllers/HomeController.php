<?php

namespace App\Controllers;

class HomeController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function index()
    {
        // Use city_id from session, default to 1
        $city_id = isset($_SESSION['city_id']) ? (int)$_SESSION['city_id'] : 1;
        
        $sql = "SELECT 
            cmap.cmap_id,
            lcn.genre,
            lcn.lcn,
            sid.sid,
            sid.freq,
            cm.channelName,
            cm.channel_id,
            br.broadcaster,
            cm.price
        FROM 
            channel_mapping_tb AS cmap
        INNER JOIN sid_tb AS sid ON cmap.sid_id = sid.sid_id
        INNER JOIN lcn_tb AS lcn ON cmap.lcn_id = lcn.lcn_id
        LEFT JOIN channel_master_tb AS cm ON cmap.channel_id = cm.channel_id
        LEFT JOIN broadcaster_tb AS br ON cm.broadcaster_id = br.broadcaster_id
        WHERE 
            sid.city_id = $city_id";

        $result = mysqli_query($this->db, $sql);
        if (!$result) {
            die('Invalid query: ' . mysqli_error($this->db));
        }

        $channels = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Load the view and pass the data to it
        require_once __DIR__ . '/../../views/dashboard.php';
    }

    public function setCity()
    {
        if (isset($_POST['city_id']) && in_array((int)$_POST['city_id'], [1,2,3,4])) {
            $_SESSION['city_id'] = (int)$_POST['city_id'];
        }
        header('Location: ' . BASE_PATH . '/');
        exit();
    }

    public function editChannelForm($channel_id)
    {
        $channel_id = (int)$channel_id;
        if (!$channel_id) {
            http_response_code(404);
            echo 'Channel not found.';
            exit;
        }
        // Get channel info
        $stmt = $this->db->prepare("SELECT * FROM channel_master_tb LEFT JOIN broadcaster_tb ON channel_master_tb.broadcaster_id = broadcaster_tb.broadcaster_id WHERE channel_master_tb.channel_id = ?");
        $stmt->bind_param('i', $channel_id);
        $stmt->execute();
        $channel = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$channel) {
            http_response_code(404);
            echo 'Channel not found.';
            exit;
        }
        // Get all broadcasters
        $broadcasters = [];
        $result = $this->db->query("SELECT * FROM broadcaster_tb");
        while ($row = $result->fetch_assoc()) {
            $broadcasters[] = $row;
        }
        require __DIR__ . '/../../views/edit_channel.php';
    }

    public function editChannelSubmit($channel_id)
    {
        $channel_id = (int)$channel_id;
        if (!$channel_id) {
            http_response_code(404);
            echo 'Channel not found.';
            exit;
        }
        $name = trim($_POST['channelName'] ?? '');
        $broadcaster_id = (int)($_POST['broadcaster_id'] ?? 0);
        $price = trim($_POST['price'] ?? '');
        if ($name === '' || !$broadcaster_id || $price === '') {
            echo 'All fields are required.';
            exit;
        }
        $stmt = $this->db->prepare("UPDATE channel_master_tb SET channelName=?, broadcaster_id=?, price=?, updated_at=NOW() WHERE channel_id=?");
        $stmt->bind_param('sidi', $name, $broadcaster_id, $price, $channel_id);
        $stmt->execute();
        $stmt->close();
        header('Location: ' . BASE_PATH . '/');
        exit();
    }
} 