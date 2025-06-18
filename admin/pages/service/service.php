<?php
require_once __DIR__ . '/../../../config/config.php';

// Database connection
function getDBConnection() {
    $conn = mysqli_connect("localhost", "root", "", "vastacom_db_v1");
    
    if (!$conn) {
        die("Koneksi database gagal: " . mysqli_connect_error());
    }
    
    return $conn;
}

// Handle actions
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

switch ($action) {
    case 'get':
        handleGetService();
        break;
    case 'add':
        handleAddService();
        break;
    case 'edit':
        handleEditService();
        break;
    case 'delete':
        handleDeleteService();
        break;
    case 'history':
        handleGetStatusHistory();
        break;
    default:
        // No action or invalid action
        break;
}

function handleGetService() {
    error_log("GET Service Request Received"); // Log ke error log server
    
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    error_log("Requesting service ID: " . $id);
    
    $service = getServiceById($id);
    
    header('Content-Type: application/json');
    if ($service) {
        error_log("Service found: " . json_encode($service));
        echo json_encode([
            'success' => true,
            'service' => $service
        ]);
    } else {
        error_log("Service not found for ID: " . $id);
        echo json_encode([
            'success' => false,
            'message' => 'Service not found'
        ]);
    }
    exit;
}

function handleAddService() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }
    
    
    
    $requiredFields = ['id_pelanggan', 'jenis_kerusakan', 'deskripsi', 'status', 'tanggal_masuk'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => "Field $field is required"
            ]);
            exit;
        }
    }
    
    $id_pelanggan = (int)$_POST['id_pelanggan'];
    $jenis_kerusakan = trim($_POST['jenis_kerusakan']);
    $deskripsi = trim($_POST['deskripsi']);
    $status = trim($_POST['status']);
    $tanggal_masuk = trim($_POST['tanggal_masuk']);
    $id_teknisi = isset($_POST['id_teknisi']) ? (int)$_POST['id_teknisi'] : null;
    
    // Handle file upload
    $foto_kerusakan = null;
    if (isset($_FILES['foto_kerusakan']) && $_FILES['foto_kerusakan']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../../uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileExt = pathinfo($_FILES['foto_kerusakan']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('damage_') . '.' . $fileExt;
        $uploadPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['foto_kerusakan']['tmp_name'], $uploadPath)) {
            $foto_kerusakan = $fileName;
        }
    }
    
    $conn = getDBConnection();
    
    // Insert service
    $stmt = $conn->prepare("INSERT INTO service (id_pelanggan, id_teknisi, jenis_kerusakan, deskripsi, status, tanggal_masuk, foto_kerusakan, created_at) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("iisssss", $id_pelanggan, $id_teknisi, $jenis_kerusakan, $deskripsi, $status, $tanggal_masuk, $foto_kerusakan);
    
    // header('Content-Type: application/json');
    if ($stmt->execute()) {
        $serviceId = $stmt->insert_id;
        
        // Add initial status to history
        $userId = $_SESSION['user_id'] ?? 1; // Use logged-in user's ID
        addStatusHistory($serviceId, $status, $userId, 'Service created');
        
        // Redirect ke halaman service setelah berhasil
        header("Location: /fp-pemrograman-web/admin/index.php?page=service/index&success=add");
    } else {
        // Redirect ke halaman service setelah gagal
        header("Location: /fp-pemrograman-web/admin/index.php?page=service/index&error=1");
    }
    
    $stmt->close();
    $conn->close();
    exit;
}

function handleEditService() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }
    
    $requiredFields = ['id', 'id_pelanggan', 'jenis_kerusakan', 'deskripsi', 'status'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => "Field $field is required"
            ]);
            exit;
        }
        
    }
    
    $id = (int)$_POST['id'];
    $id_pelanggan = (int)$_POST['id_pelanggan'];
    $jenis_kerusakan = trim($_POST['jenis_kerusakan']);
    $deskripsi = trim($_POST['deskripsi']);
    $status = trim($_POST['status']);
    // $tanggal_masuk = trim($_POST['tanggal_masuk']);
    $id_teknisi = isset($_POST['id_teknisi']) ? (int)$_POST['id_teknisi'] : null;
    
    // Get current service data to check for status change
    $currentService = getServiceById($id);
    $statusChanged = $currentService && $currentService['status'] !== $status;
    
    $conn = getDBConnection();
    
    // Handle file upload
    $foto_kerusakan = $currentService['foto_kerusakan'] ?? null;
    if (isset($_FILES['foto_kerusakan']) && $_FILES['foto_kerusakan']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../../uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Delete old photo if exists
        if ($foto_kerusakan && file_exists($uploadDir . $foto_kerusakan)) {
            unlink($uploadDir . $foto_kerusakan);
        }
        
        $fileExt = pathinfo($_FILES['foto_kerusakan']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('damage_') . '.' . $fileExt;
        $uploadPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['foto_kerusakan']['tmp_name'], $uploadPath)) {
            $foto_kerusakan = $fileName;
        }
    }
    
    // Update service
    $stmt = $conn->prepare("UPDATE service 
                           SET id_pelanggan = ?, id_teknisi = ?, jenis_kerusakan = ?, deskripsi = ?, 
                               status = ?, tanggal_update = NOW(), foto_kerusakan = ?
                           WHERE id = ?");
    $stmt->bind_param("iissssi", $id_pelanggan, $id_teknisi, $jenis_kerusakan, $deskripsi, $status, $foto_kerusakan, $id);
    
    // header('Content-Type: application/json');
    if ($stmt->execute()) {
        // Add status to history if changed
        if ($statusChanged) {
            $userId = $_SESSION['user_id'] ?? 1; // Use logged-in user's ID
            addStatusHistory($id, $status, $userId, 'Status updated');
        }
        
        // Redirect ke halaman service setelah berhasil
            header("Location: /fp-pemrograman-web/admin/index.php?page=service/index&success=edit");
        } else {
            // Redirect ke halaman service setelah gagal
            header("Location: /fp-pemrograman-web/admin/index.php?page=service/index&error=1");
        }
    $stmt->close();
    $conn->close();
    exit;
}

function handleDeleteService() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }
    
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    
    // Get service to delete photo if exists
    $service = getServiceById($id);
    
    $conn = getDBConnection();

    // Hapus semua history terkait service
    $deleteHistory = $conn->prepare("DELETE FROM history_status WHERE service_id = ?");
    $deleteHistory->bind_param("i", $id);
    $deleteHistory->execute();
    $deleteHistory->close();
    $stmt = $conn->prepare("DELETE FROM service WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    header('Content-Type: application/json');
    if ($stmt->execute()) {
        // Delete photo if exists
        if ($service && $service['foto_kerusakan']) {
            $uploadDir = __DIR__ . '/../../../uploads/';
            if (file_exists($uploadDir . $service['foto_kerusakan'])) {
                unlink($uploadDir . $service['foto_kerusakan']);
            }
        }
        
        // Redirect ke halaman service setelah berhasil
            header("Location: /fp-pemrograman-web/admin/index.php?page=service/index&success=delete");
        } else {
            // Redirect ke halaman service setelah gagal
            header("Location: /fp-pemrograman-web/admin/index.php?page=service/index&error=1");
        }
    
    $stmt->close();
    $conn->close();
    exit;
}

function handleGetStatusHistory() {
    $serviceId = isset($_GET['service_id']) ? (int)$_GET['service_id'] : 0;
    $history = getStatusHistory($serviceId);
    
    header('Content-Type: application/json');
    echo json_encode($history);
    exit;
}

// Helper functions
function getServiceById($id) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("SELECT s.*, p.nama AS customer_name, u.username AS technician_name 
                           FROM service s
                           LEFT JOIN pelanggan p ON s.id_pelanggan = p.id
                           LEFT JOIN users u ON s.id_teknisi = u.id
                           WHERE s.id = ?");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return false;
    }
    
    $stmt->bind_param("i", $id);
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        $stmt->close();
        $conn->close();
        return false;
    }
    
    $result = $stmt->get_result();
    $service = $result->fetch_assoc();
    
    $stmt->close();
    $conn->close();
    
    return $service;
}

function getServices($search = '', $limit = 10, $offset = 0) {
    $conn = getDBConnection();
    
    if ($conn->connect_error) {
        return [
            'success' => false,
            'message' => 'Database connection failed',
            'data' => []
        ];
    }

    try {
        $sql = "SELECT s.*, p.nama AS customer_name, u.username AS technician_name 
               FROM service s
               LEFT JOIN pelanggan p ON s.id_pelanggan = p.id
               LEFT JOIN users u ON s.id_teknisi = u.id";
        
        $where = [];
        $params = [];
        $types = "";
        
        if (!empty($search)) {
            $where[] = "(s.deskripsi LIKE ? OR s.jenis_kerusakan LIKE ? OR p.nama LIKE ?)";
            $searchParam = "%$search%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
            $types .= "sss";
        }
        
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        $sql .= " ORDER BY s.tanggal_masuk DESC LIMIT ? OFFSET ?";
        $params = array_merge($params, [$limit, $offset]);
        $types .= "ii";
        
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            return [
                'success' => false,
                'message' => 'Prepare failed: ' . $conn->error,
                'data' => []
            ];
        }
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        if (!$stmt->execute()) {
            return [
                'success' => false,
                'message' => 'Execute failed: ' . $stmt->error,
                'data' => []
            ];
        }
        
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        
        return [
            'success' => true,
            'message' => '',
            'data' => $data
        ];
        
    } finally {
        if (isset($stmt)) $stmt->close();
        $conn->close();
    }
}

function getTotalServices($search = '') {
    $conn = getDBConnection();
    
    try {
        $sql = "SELECT COUNT(*) as total FROM service s LEFT JOIN pelanggan p ON s.id_pelanggan = p.id";
        $params = [];
        $types = "";
        
        if (!empty($search)) {
            $sql .= " WHERE (s.deskripsi LIKE ? OR s.jenis_kerusakan LIKE ? OR p.nama LIKE ?)";
            $searchParam = "%$search%";
            $params = [$searchParam, $searchParam, $searchParam];
            $types = "sss";
        }
        
        $stmt = $conn->prepare($sql);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return [
            'success' => true,
            'total' => (int)$row['total']
        ];
        
    } finally {
        if (isset($stmt)) $stmt->close();
        $conn->close();
    }
}

function getAllCustomers() {
    $conn = getDBConnection();
    
    $result = $conn->query("SELECT id, nama FROM pelanggan ORDER BY nama");
    $customers = $result->fetch_all(MYSQLI_ASSOC);
    
    $conn->close();
    
    return $customers;
}

function getAllTechnicians() {
    $conn = getDBConnection();
    
    $result = $conn->query("SELECT id, username FROM users WHERE role = 'teknisi' ORDER BY username");
    $technicians = $result->fetch_all(MYSQLI_ASSOC);
    
    $conn->close();
    
    return $technicians;
}

function getStatusHistory($serviceId) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("SELECT h.*, u.username AS updated_by_name 
                           FROM history_status h
                           LEFT JOIN users u ON h.updated_by = u.id
                           WHERE h.service_id = ?
                           ORDER BY h.updated_on DESC");
    $stmt->bind_param("i", $serviceId);
    $stmt->execute();
    $result = $stmt->get_result();
    $history = $result->fetch_all(MYSQLI_ASSOC);
    
    $stmt->close();
    $conn->close();
    
    return $history;
}

function addStatusHistory($serviceId, $status, $updatedBy, $note = '') {
    $conn = getDBConnection();
    
    // Periksa koneksi
    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error);
        return false;
    }

    // Perbaiki typo 'catalan' menjadi 'catatan' jika perlu
    $stmt = $conn->prepare("INSERT INTO history_status 
                          (service_id, status, updated_on, updated_by, catatan, created_at) 
                          VALUES (?, ?, NOW(), ?, ?, NOW())");
    
    // Debug jika prepare gagal
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
        $conn->close();
        return false;
    }
    
    // Bind parameter
    $bindResult = $stmt->bind_param("isis", $serviceId, $status, $updatedBy, $note);
    if ($bindResult === false) {
        error_log("Bind failed: " . $stmt->error);
        $stmt->close();
        $conn->close();
        return false;
    }
    
    // Eksekusi
    $executeResult = $stmt->execute();
    if ($executeResult === false) {
        error_log("Execute failed: " . $stmt->error);
    }
    
    $stmt->close();
    $conn->close();
    
    return $executeResult;
}