<?php

namespace App\Controllers;

use App\LogHelper;

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
            sid.city_id = $city_id ORDER BY lcn.lcn ASC";

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
            LogHelper::log($this->db, 'Change City', 'Changed city to ID ' . $_SESSION['city_id']);
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
        // Get old data for log
        $old = $this->db->query("SELECT channelName, broadcaster_id, price FROM channel_master_tb WHERE channel_id=$channel_id")->fetch_assoc();
        $stmt = $this->db->prepare("UPDATE channel_master_tb SET channelName=?, broadcaster_id=?, price=?, updated_at=NOW() WHERE channel_id=?");
        $stmt->bind_param('sidi', $name, $broadcaster_id, $price, $channel_id);
        $stmt->execute();
        $stmt->close();
        // Log
        $details = "Channel ID $channel_id: Name changed from '{$old['channelName']}' to '$name', Broadcaster ID from {$old['broadcaster_id']} to $broadcaster_id, Price from {$old['price']} to $price";
        LogHelper::log($this->db, 'Edit Channel', $details);
        header('Location: ' . BASE_PATH . '/');
        exit();
    }

    public function modifyLcnForm($cmap_id)
    {
        $cmap_id = (int)$cmap_id;
        if (!$cmap_id) {
            http_response_code(404);
            echo 'Mapping not found.';
            exit;
        }
        // Get mapping and joined info
        $stmt = $this->db->prepare("SELECT cmap.cmap_id, cmap.lcn_id, sid.sid, lcn.lcn, lcn.genre, cm.channelName FROM channel_mapping_tb AS cmap INNER JOIN sid_tb AS sid ON cmap.sid_id = sid.sid_id INNER JOIN lcn_tb AS lcn ON cmap.lcn_id = lcn.lcn_id LEFT JOIN channel_master_tb AS cm ON cmap.channel_id = cm.channel_id WHERE cmap.cmap_id = ?");
        $stmt->bind_param('i', $cmap_id);
        $stmt->execute();
        $mapping = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$mapping) {
            http_response_code(404);
            echo 'Mapping not found.';
            exit;
        }
        // Get available blank LCNs
        $blanks = [];
        $result = $this->db->query("SELECT * FROM lcn_tb WHERE lcn_tb.lcn_id NOT IN (SELECT lcn_id FROM channel_mapping_tb)");
        while ($row = $result->fetch_assoc()) {
            $blanks[] = $row;
        }
        require __DIR__ . '/../../views/modify_lcn.php';
    }

    public function modifyLcnSubmit($cmap_id)
    {
        $cmap_id = (int)$cmap_id;
        $lcn_id = (int)($_POST['lcn_id'] ?? 0);
        if (!$cmap_id || !$lcn_id) {
            echo 'Invalid request.';
            exit;
        }
        // Get old/new for log
        $old = $this->db->query("SELECT cmap.lcn_id, lcn.lcn, cm.channelName, sid.sid FROM channel_mapping_tb cmap INNER JOIN lcn_tb lcn ON cmap.lcn_id=lcn.lcn_id LEFT JOIN channel_master_tb cm ON cmap.channel_id=cm.channel_id LEFT JOIN sid_tb sid ON cmap.sid_id=sid.sid_id WHERE cmap.cmap_id=$cmap_id")->fetch_assoc();
        $new = $this->db->query("SELECT lcn, genre FROM lcn_tb WHERE lcn_id=$lcn_id")->fetch_assoc();
        $stmt = $this->db->prepare("UPDATE channel_mapping_tb SET lcn_id=?, updated_at=NOW() WHERE cmap_id=?");
        $stmt->bind_param('ii', $lcn_id, $cmap_id);
        $stmt->execute();
        $stmt->close();
        // Log
        $details = "{$old['channelName']} with SID {$old['sid']} LCN changed from {$old['lcn']} to {$new['lcn']} ({$new['genre']})";
        LogHelper::log($this->db, 'Modify LCN', $details);
        header('Location: ' . BASE_PATH . '/');
        exit();
    }

    public function swapLcnForm($cmap_id)
    {
        $cmap_id = (int)$cmap_id;
        if (!$cmap_id) {
            http_response_code(404);
            echo 'Mapping not found.';
            exit;
        }
        // Get current mapping with channel info
        $stmt = $this->db->prepare("SELECT cmap.cmap_id, cmap.lcn_id, sid.sid, lcn.lcn, lcn.genre, cm.channelName, sid.city_id FROM channel_mapping_tb AS cmap INNER JOIN sid_tb AS sid ON cmap.sid_id = sid.sid_id INNER JOIN lcn_tb AS lcn ON cmap.lcn_id = lcn.lcn_id LEFT JOIN channel_master_tb AS cm ON cmap.channel_id = cm.channel_id WHERE cmap.cmap_id = ?");
        $stmt->bind_param('i', $cmap_id);
        $stmt->execute();
        $mapping = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$mapping) {
            http_response_code(404);
            echo 'Mapping not found.';
            exit;
        }
        // Get all other mappings for the same city (exclude itself)
        $city_id = (int)$mapping['city_id'];
        $others = [];
        $result = $this->db->query("SELECT cmap.cmap_id, lcn.lcn, lcn.genre, cm.channelName FROM channel_mapping_tb AS cmap INNER JOIN sid_tb AS sid ON cmap.sid_id = sid.sid_id INNER JOIN lcn_tb AS lcn ON cmap.lcn_id = lcn.lcn_id LEFT JOIN channel_master_tb AS cm ON cmap.channel_id = cm.channel_id WHERE sid.city_id = $city_id AND cmap.cmap_id != $cmap_id ORDER BY lcn.lcn ASC");
        while ($row = $result->fetch_assoc()) {
            $others[] = $row;
        }
        require __DIR__ . '/../../views/swap_lcn.php';
    }

    public function swapLcnSubmit($cmap_id)
    {
        $cmap_id = (int)$cmap_id;
        $target_cmap_id = (int)($_POST['target_cmap_id'] ?? 0);
        if (!$cmap_id || !$target_cmap_id || $cmap_id === $target_cmap_id) {
            echo 'Invalid request.';
            exit;
        }
        // Fetch both mappings
        $stmt = $this->db->prepare("SELECT cmap_id, lcn_id, channel_id FROM channel_mapping_tb WHERE cmap_id IN (?, ?)");
        $stmt->bind_param('ii', $cmap_id, $target_cmap_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[$row['cmap_id']] = $row;
        }
        $stmt->close();
        if (count($rows) !== 2) {
            echo 'Invalid mapping selection.';
            exit;
        }
        $lcn_id_1 = $rows[$cmap_id]['lcn_id'];
        $lcn_id_2 = $rows[$target_cmap_id]['lcn_id'];
        // Get channel names for log
        $c1 = $this->db->query("SELECT cm.channelName, sid.sid FROM channel_mapping_tb cmap LEFT JOIN channel_master_tb cm ON cmap.channel_id=cm.channel_id LEFT JOIN sid_tb sid ON cmap.sid_id=sid.sid_id WHERE cmap.cmap_id=$cmap_id")->fetch_assoc();
        $c2 = $this->db->query("SELECT cm.channelName, sid.sid FROM channel_mapping_tb cmap LEFT JOIN channel_master_tb cm ON cmap.channel_id=cm.channel_id LEFT JOIN sid_tb sid ON cmap.sid_id=sid.sid_id WHERE cmap.cmap_id=$target_cmap_id")->fetch_assoc();
        // Update both records
        $stmt1 = $this->db->prepare("UPDATE channel_mapping_tb SET lcn_id=?, updated_at=NOW() WHERE cmap_id=?");
        $stmt1->bind_param('ii', $lcn_id_2, $cmap_id);
        $stmt1->execute();
        $stmt1->close();
        $stmt2 = $this->db->prepare("UPDATE channel_mapping_tb SET lcn_id=?, updated_at=NOW() WHERE cmap_id=?");
        $stmt2->bind_param('ii', $lcn_id_1, $target_cmap_id);
        $stmt2->execute();
        $stmt2->close();
        // Log
        $details = "Swapped LCN between {$c1['channelName']} (SID {$c1['sid']}) and {$c2['channelName']} (SID {$c2['sid']})";
        LogHelper::log($this->db, 'Swap LCN', $details);
        header('Location: ' . BASE_PATH . '/');
        exit();
    }

    public function logsPage()
    {
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 25;
        $offset = ($page - 1) * $perPage;
        $logs = [];
        $result = $this->db->query("SELECT * FROM activity_log ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");
        while ($row = $result->fetch_assoc()) {
            $logs[] = $row;
        }
        // Get total count for pagination
        $total = $this->db->query("SELECT COUNT(*) as cnt FROM activity_log")->fetch_assoc()['cnt'];
        $totalPages = ceil($total / $perPage);
        require __DIR__ . '/../../views/logs.php';
    }

    public function irdInventoryPage()
    {
        $city_id = isset($_SESSION['city_id']) ? (int)$_SESSION['city_id'] : 1;
        $inventory = [];
        $result = $this->db->query("SELECT ird.ird_id, cm.channelName, br.broadcaster, ird.stbNum, ird.vcNum, ird.updated_at FROM ird_mapping_tb AS ird LEFT JOIN channel_master_tb AS cm ON ird.channel_id = cm.channel_id LEFT JOIN broadcaster_tb AS br ON ird.broadcaster_id = br.broadcaster_id WHERE ird.city_id = $city_id ORDER BY ird.updated_at DESC");
        while ($row = $result->fetch_assoc()) {
            $inventory[] = $row;
        }
        require __DIR__ . '/../../views/ird_inventory.php';
    }

    public function irdAddForm()
    {
        // Get channels and broadcasters for dropdowns
        $channels = [];
        $result = $this->db->query("SELECT channel_id, channelName FROM channel_master_tb ORDER BY channelName ASC");
        while ($row = $result->fetch_assoc()) {
            $channels[] = $row;
        }
        $broadcasters = [];
        $result = $this->db->query("SELECT broadcaster_id, broadcaster FROM broadcaster_tb ORDER BY broadcaster ASC");
        while ($row = $result->fetch_assoc()) {
            $broadcasters[] = $row;
        }
        require __DIR__ . '/../../views/ird_add.php';
    }

    public function irdAddSubmit()
    {
        $city_id = isset($_SESSION['city_id']) ? (int)$_SESSION['city_id'] : 1;
        $channel_id = (int)($_POST['channel_id'] ?? 0);
        $broadcaster_id = (int)($_POST['broadcaster_id'] ?? 0);
        $stbNum = trim($_POST['stbNum'] ?? '');
        $vcNum = trim($_POST['vcNum'] ?? '');
        if (!$channel_id || !$broadcaster_id || $stbNum === '' || $vcNum === '') {
            echo 'All fields are required.';
            exit;
        }
        $stmt = $this->db->prepare("INSERT INTO ird_mapping_tb (channel_id, broadcaster_id, city_id, stbNum, vcNum, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->bind_param('iiiss', $channel_id, $broadcaster_id, $city_id, $stbNum, $vcNum);
        $stmt->execute();
        $stmt->close();
        \App\LogHelper::log($this->db, 'Add IRD', "Added IRD for channel_id $channel_id, broadcaster_id $broadcaster_id, STB $stbNum, VC $vcNum");
        header('Location: ' . BASE_PATH . '/ird-inventory');
        exit();
    }

    public function irdEditForm($ird_id)
    {
        $ird_id = (int)$ird_id;
        $stmt = $this->db->prepare("SELECT * FROM ird_mapping_tb WHERE ird_id = ?");
        $stmt->bind_param('i', $ird_id);
        $stmt->execute();
        $ird = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$ird) {
            echo 'IRD entry not found.';
            exit;
        }
        $channels = [];
        $result = $this->db->query("SELECT channel_id, channelName FROM channel_master_tb ORDER BY channelName ASC");
        while ($row = $result->fetch_assoc()) {
            $channels[] = $row;
        }
        $broadcasters = [];
        $result = $this->db->query("SELECT broadcaster_id, broadcaster FROM broadcaster_tb ORDER BY broadcaster ASC");
        while ($row = $result->fetch_assoc()) {
            $broadcasters[] = $row;
        }
        require __DIR__ . '/../../views/ird_edit.php';
    }

    public function irdEditSubmit($ird_id)
    {
        $ird_id = (int)$ird_id;
        $channel_id = (int)($_POST['channel_id'] ?? 0);
        $broadcaster_id = (int)($_POST['broadcaster_id'] ?? 0);
        $stbNum = trim($_POST['stbNum'] ?? '');
        $vcNum = trim($_POST['vcNum'] ?? '');
        if (!$channel_id || !$broadcaster_id || $stbNum === '' || $vcNum === '') {
            echo 'All fields are required.';
            exit;
        }
        $stmt = $this->db->prepare("UPDATE ird_mapping_tb SET channel_id=?, broadcaster_id=?, stbNum=?, vcNum=?, updated_at=NOW() WHERE ird_id=?");
        $stmt->bind_param('iissi', $channel_id, $broadcaster_id, $stbNum, $vcNum, $ird_id);
        $stmt->execute();
        $stmt->close();
        \App\LogHelper::log($this->db, 'Edit IRD', "Edited IRD $ird_id: channel_id $channel_id, broadcaster_id $broadcaster_id, STB $stbNum, VC $vcNum");
        header('Location: ' . BASE_PATH . '/ird-inventory');
        exit();
    }

    public function irdDelete($ird_id)
    {
        $ird_id = (int)$ird_id;
        $stmt = $this->db->prepare("DELETE FROM ird_mapping_tb WHERE ird_id = ?");
        $stmt->bind_param('i', $ird_id);
        $stmt->execute();
        $stmt->close();
        \App\LogHelper::log($this->db, 'Delete IRD', "Deleted IRD $ird_id");
        header('Location: ' . BASE_PATH . '/ird-inventory');
        exit();
    }

    public function addSidForm()
    {
        require __DIR__ . '/../../views/add_sid.php';
    }

    public function addSidSubmit()
    {
        $sid = (int)($_POST['sid'] ?? 0);
        $ts = trim($_POST['ts'] ?? '');
        $freq = (int)($_POST['freq'] ?? 0);
        $sidhex = trim($_POST['sidhex'] ?? '');
        $city_id = isset($_SESSION['city_id']) ? (int)$_SESSION['city_id'] : 1;
        if (!$sid || $ts === '' || !$freq || $sidhex === '') {
            echo 'All fields are required.';
            exit;
        }
        $stmt = $this->db->prepare("INSERT INTO sid_tb (sid, ts, freq, sidhex, city_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('isisi', $sid, $ts, $freq, $sidhex, $city_id);
        $stmt->execute();
        $stmt->close();
        \App\LogHelper::log($this->db, 'Add SID', "Added SID $sid, TS $ts, Freq $freq, Hex $sidhex, City $city_id");
        header('Location: ' . BASE_PATH . '/');
        exit();
    }

    public function addChannelForm()
    {
        $broadcasters = [];
        $result = $this->db->query("SELECT broadcaster_id, broadcaster FROM broadcaster_tb ORDER BY broadcaster ASC");
        while ($row = $result->fetch_assoc()) {
            $broadcasters[] = $row;
        }
        require __DIR__ . '/../../views/add_channel.php';
    }

    public function addChannelSubmit()
    {
        $name = trim($_POST['channelName'] ?? '');
        $broadcaster_id = (int)($_POST['broadcaster_id'] ?? 0);
        $price = trim($_POST['price'] ?? '');
        if ($name === '' || !$broadcaster_id || $price === '') {
            echo 'All fields are required.';
            exit;
        }
        $stmt = $this->db->prepare("INSERT INTO channel_master_tb (channelName, broadcaster_id, price, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->bind_param('sid', $name, $broadcaster_id, $price);
        $stmt->execute();
        $stmt->close();
        \App\LogHelper::log($this->db, 'Add Channel', "Added Channel $name, Broadcaster $broadcaster_id, Price $price");
        header('Location: ' . BASE_PATH . '/');
        exit();
    }

    public function ajaxCheckSid()
    {
        header('Content-Type: application/json');
        $sid = (int)($_POST['sid'] ?? 0);
        $city_id = isset($_SESSION['city_id']) ? (int)$_SESSION['city_id'] : 1;
        $stmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM sid_tb WHERE sid = ? AND city_id = ?");
        $stmt->bind_param('ii', $sid, $city_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        echo json_encode(['exists' => ($result['cnt'] > 0)]);
        exit();
    }
} 