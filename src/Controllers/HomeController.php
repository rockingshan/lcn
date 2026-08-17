<?php
/**
 * HomeController.php
 *
 * Main controller for all business logic and page rendering in the LCN Management System.
 * Handles dashboard, CRUD for channels, SIDs, LCNs, IRD inventory, channel mapping, logs, and IRD Challan uploads.
 *
 * Each method is documented with its purpose and any important queries.
 */

namespace App\Controllers;

use App\LogHelper;

class HomeController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * index()
     * Dashboard page. Lists all channel mappings for the current city.
     * Joins channel_mapping_tb, sid_tb, lcn_tb, channel_master_tb, broadcaster_tb, and ird_mapping_tb.
     *
     * Query: See method body for full SQL.
     */
    public function index()
    {
        // Use city_id from session, default to 1
        $city_id = isset($_SESSION['city_id']) ? (int)$_SESSION['city_id'] : 1;
        
        // Fetch city name and set as global variable for export functions
        $city_result = $this->db->query("SELECT city_name FROM city_tb WHERE city_id = $city_id");
        $city_name = 'Kolkata'; // Default fallback
        if ($city_result && $city_result->num_rows > 0) {
            $city_name = $city_result->fetch_assoc()['city_name'];
        }
        $GLOBALS['city_name'] = $city_name;
        
        $sql = "SELECT 
            cmap.cmap_id,
            lcn.genre,
            lcn.lcn,
            sid.sid,
            sid.freq,
            cm.channelName,
            cm.channel_id,
            br.broadcaster,
            cm.price,
            ird.ird_id
        FROM 
            channel_mapping_tb AS cmap
        INNER JOIN sid_tb AS sid ON cmap.sid_id = sid.sid_id
        INNER JOIN lcn_tb AS lcn ON cmap.lcn_id = lcn.lcn_id
        LEFT JOIN channel_master_tb AS cm ON cmap.channel_id = cm.channel_id
        LEFT JOIN broadcaster_tb AS br ON cm.broadcaster_id = br.broadcaster_id
        LEFT JOIN ird_mapping_tb AS ird ON ird.channel_id = cm.channel_id AND ird.city_id = $city_id
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

    /**
     * setCity()
     * Sets the current city in session and logs the change.
     */
    public function setCity()
    {
        if (isset($_POST['city_id']) && in_array((int)$_POST['city_id'], [1,2,3,4])) {
            $_SESSION['city_id'] = (int)$_POST['city_id'];
            LogHelper::log($this->db, 'Change City', 'Changed city to ID ' . $_SESSION['city_id']);
        }
        header('Location: ' . BASE_PATH . '/');
        exit();
    }

    /**
     * editChannelForm($channel_id)
     * Shows the edit form for a channel. Fetches channel and all broadcasters.
     */
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

    /**
     * editChannelSubmit($channel_id)
     * Handles channel edit form submission. Updates channel_master_tb and logs changes.
     */
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

    /**
     * modifyLcnForm($cmap_id)
     * Shows the form to modify LCN for a channel mapping. Fetches mapping and available blank LCNs.
     */
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

    /**
     * modifyLcnSubmit($cmap_id)
     * Handles LCN modification. Updates channel_mapping_tb and logs the change.
     */
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
        $this->trackFrequency($cmap_id);
        // Log
        $details = "{$old['channelName']} with SID {$old['sid']} LCN changed from {$old['lcn']} to {$new['lcn']} ({$new['genre']})";
        LogHelper::log($this->db, 'Modify LCN', $details);
        header('Location: ' . BASE_PATH . '/');
        exit();
    }

    /**
     * swapLcnForm($cmap_id)
     * Shows the form to swap LCNs between two channels in the same city.
     */
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

    /**
     * swapLcnSubmit($cmap_id)
     * Handles LCN swap. Updates both mappings and logs the swap.
     */
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
        $this->trackFrequency($cmap_id);
        $this->trackFrequency($target_cmap_id);
        // Log
        $details = "Swapped LCN between {$c1['channelName']} (SID {$c1['sid']}) and {$c2['channelName']} (SID {$c2['sid']})";
        LogHelper::log($this->db, 'Swap LCN', $details);
        header('Location: ' . BASE_PATH . '/');
        exit();
    }

    private function trackFrequency(int $cmapId): void
    {
        $stmt = $this->db->prepare('SELECT sid.freq FROM channel_mapping_tb cmap JOIN sid_tb sid ON sid.sid_id = cmap.sid_id WHERE cmap.cmap_id = ?');
        $stmt->bind_param('i', $cmapId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($row) {
            $_SESSION['changed_frequencies'] = array_values(array_unique(array_merge($_SESSION['changed_frequencies'] ?? [], [(int)$row['freq']])));
        }
    }

    public function lcnStringsPage()
    {
        $frequencies = $_SESSION['changed_frequencies'] ?? [];
        sort($frequencies, SORT_NUMERIC);
        $generated = [];
        $cityId = (int)($_SESSION['city_id'] ?? 1);
        $stmt = $this->db->prepare('SELECT sid.sidhex, lcn.lcnhex FROM channel_mapping_tb cmap JOIN sid_tb sid ON sid.sid_id=cmap.sid_id JOIN lcn_tb lcn ON lcn.lcn_id=cmap.lcn_id WHERE sid.city_id=? AND sid.freq=? ORDER BY lcn.lcn');
        foreach ($frequencies as $frequency) {
            $stmt->bind_param('ii', $cityId, $frequency);
            $stmt->execute();
            $result = $stmt->get_result();
            $parts = [];
            while ($row = $result->fetch_assoc()) {
                $hex = strtoupper(preg_replace('/[^0-9A-F]/i', '', $row['sidhex'] . $row['lcnhex']));
                if (strlen($hex) === 8) { $parts[] = trim(chunk_split($hex, 2, ' ')); }
            }
            $generated[] = ['frequency' => $frequency, 'string' => implode(' ', $parts)];
        }
        $stmt->close();
        require __DIR__ . '/../../views/lcn_strings.php';
    }

    /**
     * logsPage()
     * Shows the paginated activity log.
     */
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

    /**
     * irdInventoryPage()
     * Lists all IRD inventory for the current city.
     */
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

    /**
     * irdAddForm()
     * Shows the form to add a new IRD entry. Fetches channels and broadcasters.
     */
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

    /**
     * irdAddSubmit()
     * Handles IRD add form submission. Inserts into ird_mapping_tb and logs the action.
     */
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

    /**
     * irdEditForm($ird_id)
     * Shows the form to edit an IRD entry. Fetches entry, channels, and broadcasters.
     */
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

    /**
     * irdEditSubmit($ird_id)
     * Handles IRD edit form submission. Updates ird_mapping_tb and logs the action.
     */
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

    /**
     * irdDelete($ird_id)
     * Deletes an IRD entry and logs the action.
     */
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

    /**
     * addSidForm()
     * Shows the form to add a new SID.
     */
    public function addSidForm()
    {
        require __DIR__ . '/../../views/add_sid.php';
    }

    /**
     * addSidSubmit()
     * Handles SID add form submission. Inserts into sid_tb and logs the action.
     */
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

    /**
     * addChannelForm()
     * Shows the form to add a new channel. Fetches broadcasters.
     */
    public function addChannelForm()
    {
        $broadcasters = [];
        $result = $this->db->query("SELECT broadcaster_id, broadcaster FROM broadcaster_tb ORDER BY broadcaster ASC");
        while ($row = $result->fetch_assoc()) {
            $broadcasters[] = $row;
        }
        require __DIR__ . '/../../views/add_channel.php';
    }

    /**
     * addChannelSubmit()
     * Handles channel add form submission. Inserts into channel_master_tb and logs the action.
     */
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

    /**
     * ajaxCheckSid()
     * AJAX endpoint to check if a SID exists for the current city. Returns JSON.
     */
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

    /**
     * addChannelMappingForm()
     * Shows the form to add a new channel mapping. Fetches blank SIDs, blank LCNs, and channels.
     */
    public function addChannelMappingForm()
    {
        $city_id = isset($_SESSION['city_id']) ? (int)$_SESSION['city_id'] : 1;
        // SIDs not yet mapped for this city
        $sids = [];
        $result = $this->db->query("SELECT sid_id, sid FROM sid_tb WHERE city_id = $city_id AND sid_id NOT IN (SELECT sid_id FROM channel_mapping_tb)");
        while ($row = $result->fetch_assoc()) {
            $sids[] = $row;
        }
        // LCNs not yet mapped for this city
        $lcns = [];
        $result = $this->db->query("SELECT lcn_id, lcn, genre FROM lcn_tb WHERE lcn_id NOT IN (SELECT lcn_id FROM channel_mapping_tb)");
        while ($row = $result->fetch_assoc()) {
            $lcns[] = $row;
        }
        // Channel names (optional)
        $channels = [];
        $result = $this->db->query("SELECT channel_id, channelName FROM channel_master_tb ORDER BY channelName ASC");
        while ($row = $result->fetch_assoc()) {
            $channels[] = $row;
        }
        require __DIR__ . '/../../views/add_channel_mapping.php';
    }

    /**
     * addChannelMappingSubmit()
     * Handles channel mapping add form submission. Inserts into channel_mapping_tb and logs the action.
     */
    public function addChannelMappingSubmit()
    {
        $sid_id = (int)($_POST['sid_id'] ?? 0);
        $lcn_id = (int)($_POST['lcn_id'] ?? 0);
        $channel_id = isset($_POST['channel_id']) && $_POST['channel_id'] !== '' ? (int)$_POST['channel_id'] : null;
        if (!$sid_id || !$lcn_id) {
            echo 'SID and LCN are required.';
            exit;
        }
        // Check if SID or LCN already mapped
        $exists = $this->db->query("SELECT 1 FROM channel_mapping_tb WHERE sid_id = $sid_id OR lcn_id = $lcn_id")->fetch_assoc();
        if ($exists) {
            echo 'Selected SID or LCN is already mapped.';
            exit;
        }
        $stmt = $this->db->prepare("INSERT INTO channel_mapping_tb (sid_id, lcn_id, channel_id, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->bind_param('iii', $sid_id, $lcn_id, $channel_id);
        $stmt->execute();
        $stmt->close();
        \App\LogHelper::log($this->db, 'Add Channel Mapping', "Mapped SID $sid_id to LCN $lcn_id" . ($channel_id ? ", Channel $channel_id" : ''));
        header('Location: ' . BASE_PATH . '/');
        exit();
    }

    /**
     * getCityName()
     * Helper function to get current city name with fallback
     */
    private function getCityName()
    {
        if (isset($GLOBALS['city_name'])) {
            return $GLOBALS['city_name'];
        }
        
        // Fallback: fetch from database
        $city_id = isset($_SESSION['city_id']) ? (int)$_SESSION['city_id'] : 1;
        $city_result = $this->db->query("SELECT city_name FROM city_tb WHERE city_id = $city_id");
        $city_name = 'Kolkata'; // Default fallback
        if ($city_result && $city_result->num_rows > 0) {
            $city_name = $city_result->fetch_assoc()['city_name'];
        }
        return $city_name;
    }

    /**
     * exportLcnExcel()
     * Exports the LCN mapping as an Excel file using PhpSpreadsheet. Uses a custom SQL query.
     */
    public function exportLcnExcel()
    {
        require_once __DIR__ . '/../../vendor/autoload.php';
        $city_id = isset($_SESSION['city_id']) ? (int)$_SESSION['city_id'] : 1;
        $sql = "WITH ranked AS (
            SELECT 
                lcn.genre,
                lcn.lcn,
                cm.channelName,
                br.broadcaster,
                ROW_NUMBER() OVER (
                    PARTITION BY lcn.genre 
                    ORDER BY lcn.lcn
                ) AS raw_rank
            FROM 
                channel_mapping_tb AS cmap
            INNER JOIN sid_tb AS sid ON cmap.sid_id = sid.sid_id
            INNER JOIN lcn_tb AS lcn ON cmap.lcn_id = lcn.lcn_id
            LEFT JOIN channel_master_tb AS cm ON cmap.channel_id = cm.channel_id
            LEFT JOIN broadcaster_tb AS br ON cm.broadcaster_id = br.broadcaster_id
            WHERE 
                sid.city_id = $city_id
                AND br.broadcaster <> 'LOCAL'
        ),
        full_data AS (
            SELECT 
                lcn.genre,
                lcn.lcn,
                cm.channelName,
                br.broadcaster
            FROM 
                channel_mapping_tb AS cmap
            INNER JOIN sid_tb AS sid ON cmap.sid_id = sid.sid_id
            INNER JOIN lcn_tb AS lcn ON cmap.lcn_id = lcn.lcn_id
            LEFT JOIN channel_master_tb AS cm ON cmap.channel_id = cm.channel_id
            LEFT JOIN broadcaster_tb AS br ON cm.broadcaster_id = br.broadcaster_id
            WHERE sid.city_id = $city_id
        )
        SELECT 
            f.genre,
            f.lcn,
            f.channelName,
            f.broadcaster,
            COALESCE(r.raw_rank, 0) AS genre_rank
        FROM full_data f
        LEFT JOIN ranked r ON 
            f.lcn = r.lcn AND 
            f.channelName = r.channelName AND
            f.broadcaster = r.broadcaster
        ORDER BY f.lcn, genre_rank";

        $result = mysqli_query($this->db, $sql);
        if (!$result) {
            die('Invalid query: ' . mysqli_error($this->db));
        }
        $rowcount=2;

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()->setCreator("Meghbela Digital")
                                 ->setLastModifiedBy("Meghbela")
                                 ->setTitle("MeghbelaLCN")
                                 ->setSubject("Meghbela LCN")
                                 ->setDescription("Meghbela LCN")
                                 ->setKeywords("LCN Excel")
                                 ->setCategory("LCN");
        // Add Heading
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'GENRE')
            ->setCellValue('D1', 'LCN')
            ->setCellValue('B1', 'CHANNEL NAME')
            ->setCellValue('C1', 'BROADCASTER')
            ->setCellValue('E1', 'RANK');
        
        while($row = mysqli_fetch_array($result)){
            $spreadsheet->getActiveSheet()->SetCellValue('A'.$rowcount, $row['genre']);
            $spreadsheet->getActiveSheet()->SetCellValue('D'.$rowcount, $row['lcn']);
            $spreadsheet->getActiveSheet()->SetCellValue('B'.$rowcount, $row['channelName']);
            $spreadsheet->getActiveSheet()->SetCellValue('C'.$rowcount, $row['broadcaster']);
            $spreadsheet->getActiveSheet()->SetCellValue('E'.$rowcount, $row['genre_rank']);
            $rowcount++;
        }
        $file_name = $this->getCityName() . "_LCN_Export_" . date('Y-m-d') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $file_name);
        header('Cache-Control: max-age=0');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }

    /**
     * exportIrdInventoryExcel()
     * Exports the IRD inventory as an Excel file using PhpSpreadsheet.
     */
    public function exportIrdInventoryExcel()
    {
        require_once __DIR__ . '/../../vendor/autoload.php';
        $city_id = isset($_SESSION['city_id']) ? (int)$_SESSION['city_id'] : 1;
        
        $sql = "SELECT 
                    cm.channelName,
                    br.broadcaster,
                    ird.stbNum,
                    ird.vcNum,
                    ird.updated_at
                FROM ird_mapping_tb ird
                INNER JOIN channel_master_tb cm ON ird.channel_id = cm.channel_id
                INNER JOIN broadcaster_tb br ON cm.broadcaster_id = br.broadcaster_id
                INNER JOIN channel_mapping_tb cmap ON cm.channel_id = cmap.channel_id
                INNER JOIN sid_tb sid ON cmap.sid_id = sid.sid_id
                WHERE ird.city_id = $city_id
                ORDER BY cm.channelName ASC";

        $result = mysqli_query($this->db, $sql);
        if (!$result) {
            die('Invalid query: ' . mysqli_error($this->db));
        }
        $rowcount = 2;

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()->setCreator("Meghbela Digital")
                                 ->setLastModifiedBy("Meghbela")
                                 ->setTitle("IRD Inventory")
                                 ->setSubject("IRD Inventory Export")
                                 ->setDescription("IRD Inventory Export")
                                 ->setKeywords("IRD Excel")
                                 ->setCategory("IRD");
        
        // Add Headings
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Channel Name')
            ->setCellValue('B1', 'Broadcaster')
            ->setCellValue('C1', 'STB Number')
            ->setCellValue('D1', 'VC Number')
            ->setCellValue('E1', 'Last Updated');
        
        while($row = mysqli_fetch_array($result)){
            $spreadsheet->getActiveSheet()->SetCellValue('A'.$rowcount, $row['channelName']);
            $spreadsheet->getActiveSheet()->SetCellValue('B'.$rowcount, $row['broadcaster']);
            $spreadsheet->getActiveSheet()->SetCellValue('C'.$rowcount, $row['stbNum']);
            $spreadsheet->getActiveSheet()->SetCellValue('D'.$rowcount, $row['vcNum']);
            $spreadsheet->getActiveSheet()->SetCellValue('E'.$rowcount, $row['updated_at']);
            $rowcount++;
        }
        
        
        $file_name = $this->getCityName() . "_IRD_Inventory_" . date('Y-m-d') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $file_name);
        header('Cache-Control: max-age=0');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }

    /**
     * exportLogsExcel()
     * Exports the activity logs as an Excel file using PhpSpreadsheet.
     */
    public function exportLogsExcel()
    {
        require_once __DIR__ . '/../../vendor/autoload.php';
        
        $sql = "SELECT 
                    username,
                    action,
                    details,
                    ip_address,
                    created_at
                FROM activity_log   
                ORDER BY created_at DESC";

        $result = mysqli_query($this->db, $sql);
        if (!$result) {
            die('Invalid query: ' . mysqli_error($this->db));
        }
        $rowcount = 2;

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()->setCreator("Meghbela Digital")
                                 ->setLastModifiedBy("Meghbela")
                                 ->setTitle("Activity Logs")
                                 ->setSubject("Activity Logs Export")
                                 ->setDescription("Activity Logs Export")
                                 ->setKeywords("Logs Excel")
                                 ->setCategory("Logs");
        
        // Add Headings
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'User')
            ->setCellValue('B1', 'Action')
            ->setCellValue('C1', 'Details')
            ->setCellValue('D1', 'IP Address')
            ->setCellValue('E1', 'Date');
        
        while($row = mysqli_fetch_array($result)){
            $spreadsheet->getActiveSheet()->SetCellValue('A'.$rowcount, $row['username']);
            $spreadsheet->getActiveSheet()->SetCellValue('B'.$rowcount, $row['action']);
            $spreadsheet->getActiveSheet()->SetCellValue('C'.$rowcount, $row['details']);
            $spreadsheet->getActiveSheet()->SetCellValue('D'.$rowcount, $row['ip_address']);
            $spreadsheet->getActiveSheet()->SetCellValue('E'.$rowcount, $row['created_at']);
            $rowcount++;
        }
        
        $file_name = "Activity_Logs_" . date('Y-m-d') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $file_name);
        header('Cache-Control: max-age=0');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }

    /**
     * exportIrdChallanExcel()
     * Exports the IRD challan details as an Excel file using PhpSpreadsheet.
     */
    public function exportIrdChallanExcel()
    {
        require_once __DIR__ . '/../../vendor/autoload.php';
        $city_id = isset($_SESSION['city_id']) ? (int)$_SESSION['city_id'] : 1;
        
        $sql = "SELECT 
                    br.broadcaster,
                    c.challan_date,
                    c.details,
                    c.created_at
                FROM ird_challan_tb c
                INNER JOIN broadcaster_tb br ON c.broadcaster_id = br.broadcaster_id
                INNER JOIN city_tb ct ON c.city_id = ct.city_id
                WHERE c.city_id = $city_id
                ORDER BY c.challan_date DESC, c.created_at DESC";

        $result = mysqli_query($this->db, $sql);
        if (!$result) {
            die('Invalid query: ' . mysqli_error($this->db));
        }
        $rowcount = 2;

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()->setCreator("Meghbela Digital")
                                 ->setLastModifiedBy("Meghbela")
                                 ->setTitle("IRD Challan Details")
                                 ->setSubject("IRD Challan Export")
                                 ->setDescription("IRD Challan Export")
                                 ->setKeywords("Challan Excel")
                                 ->setCategory("Challan");
        
        // Add Headings
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Broadcaster')
            ->setCellValue('B1', 'Challan Date')
            ->setCellValue('C1', 'Details')
            ->setCellValue('D1', 'Added Date');
        
        while($row = mysqli_fetch_array($result)){
            $spreadsheet->getActiveSheet()->SetCellValue('A'.$rowcount, $row['broadcaster']);
            $spreadsheet->getActiveSheet()->SetCellValue('B'.$rowcount, $row['challan_date']);
            $spreadsheet->getActiveSheet()->SetCellValue('C'.$rowcount, $row['details']);
            $spreadsheet->getActiveSheet()->SetCellValue('D'.$rowcount, $row['created_at']);
            $rowcount++;
        }
        
        $file_name = $this->getCityName() . "_IRD_Challan_" . date('Y-m-d') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $file_name);
        header('Cache-Control: max-age=0');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }

    /**
     * irdChallanList()
     * Lists all IRD challans with broadcaster, date, details, and file link.
     */
    public function irdChallanList()
    {
        
        $city_id = isset($_SESSION['city_id']) ? (int)$_SESSION['city_id'] : 1;
        $challans = [];
        $result = $this->db->query("SELECT c.*, b.broadcaster FROM ird_challan_tb c INNER JOIN broadcaster_tb b ON c.broadcaster_id = b.broadcaster_id INNER JOIN city_tb ct ON c.city_id = ct.city_id
                WHERE c.city_id = $city_id ORDER BY c.challan_date DESC, c.created_at DESC");
        while ($row = $result->fetch_assoc()) {
            $challans[] = $row;
        }
        require __DIR__ . '/../../views/ird_challan_list.php';
    }

    /**
     * irdChallanAddForm()
     * Shows the form to add a new IRD challan. Fetches broadcasters.
     */
    public function irdChallanAddForm()
    {
        $broadcasters = [];
        $result = $this->db->query("SELECT broadcaster_id, broadcaster FROM broadcaster_tb ORDER BY broadcaster ASC");
        while ($row = $result->fetch_assoc()) {
            $broadcasters[] = $row;
        }
        require __DIR__ . '/../../views/ird_challan_add.php';
    }

    /**
     * irdChallanAddSubmit()
     * Handles IRD challan add form submission. Validates and uploads PDF, inserts record, logs action.
     */
    public function irdChallanAddSubmit()
    {
        $city_id = isset($_SESSION['city_id']) ? (int)$_SESSION['city_id'] : 1;
        $city_name = $this->getCityName();
        $broadcaster_id = (int)($_POST['broadcaster_id'] ?? 0);
        $challan_date = $_POST['challan_date'] ?? '';
        $details = trim($_POST['details'] ?? '');
        if (!$broadcaster_id || !$challan_date || $details === '') {
            echo 'All fields are required.';
            exit;
        }
        if (!isset($_FILES['challan_file']) || $_FILES['challan_file']['error'] !== UPLOAD_ERR_OK) {
            echo 'File upload failed.';
            exit;
        }
        $file = $_FILES['challan_file'];
        // Validate file type and size
        if ($file['type'] !== 'application/pdf') {
            echo 'Only PDF files are allowed.';
            exit;
        }
        if ($file['size'] > 5 * 1024 * 1024) {
            echo 'File size exceeds 5MB.';
            exit;
        }
        $uploadDir = __DIR__ . '/../../uploads/challans/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $basename = 'challan_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $targetPath = $uploadDir . $basename;
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            echo 'Failed to save uploaded file.';
            exit;
        }
        $file_path = 'uploads/challans/' . $basename;
        $stmt = $this->db->prepare("INSERT INTO ird_challan_tb (broadcaster_id, challan_date, details, file_path, city_id,created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->bind_param('isssi', $broadcaster_id, $challan_date, $details, $file_path, $city_id);
        $stmt->execute();
        $stmt->close();
        \App\LogHelper::log($this->db, 'Add IRD Challan', "Broadcaster $broadcaster_id, Date $challan_date, File $file_path, For city $city_name ");
        header('Location: ' . BASE_PATH . '/ird-challan');
        exit();
    }
}
