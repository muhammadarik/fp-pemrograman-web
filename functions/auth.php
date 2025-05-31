<?php
/**
 * Fungsi untuk memeriksa apakah user sudah login.
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && ($_SESSION['loggedin'] ?? false) === true;
}

/**
 * Fungsi untuk mendapatkan data user yang sedang login dari database.
 * @param mysqli $conn Objek koneksi database.
 * @return array|null Mengembalikan array data user (id, username, role) atau null jika tidak login atau data tidak ditemukan.
 */
function getCurrentUser($conn) {
    if (!isLoggedIn()) {
        return null;
    }

    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT id, username, role FROM users WHERE id = ?");
    if (!$stmt) {
        error_log("Error preparing getCurrentUser statement: " . $conn->error);
        return null;
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();
    $stmt->close();

    // Opsional: Perbarui session username dan role jika ada perubahan di DB
    if ($user_data) {
        $_SESSION['username'] = $user_data['username'];
        $_SESSION['role'] = $user_data['role'];
    }

    return $user_data;
}

/**
 * Fungsi untuk memeriksa hak akses berdasarkan role.
 * Fungsi ini menggunakan redirect melalui header() dan menyimpan pesan ke session.
 * @param array|null $user_data Data user yang sedang login (hasil dari getCurrentUser).
 * @param string|array $required_roles Role yang diizinkan (string tunggal atau array string).
 * @param string $redirect_path Jalur relatif setelah BASE_URL (misal: 'index.php?page=login' atau 'index.php?page=dashboard').
 */
function checkAccess($user_data, $required_roles, $redirect_path) {
    global $BASE_URL; // Akses konstanta BASE_URL dari config.php
    $full_redirect_url = $BASE_URL . '/' . ltrim($redirect_path, '/');

    if (!$user_data) {
        $_SESSION['message'] = "Anda harus login untuk mengakses halaman ini.";
        header("Location: " . $full_redirect_url);
        exit();
    }

    if (!is_array($required_roles)) {
        $required_roles = [$required_roles];
    }

    if (!in_array($user_data['role'], $required_roles)) {
        $_SESSION['message'] = "Akses ditolak! Anda tidak memiliki izin untuk mengakses halaman ini.";
        header("Location: " . $full_redirect_url);
        exit();
    }
}

/**
 * Mendaftarkan user baru sebagai pelanggan.
 * @param mysqli $conn Objek koneksi database.
 * @param string $username Username yang akan didaftarkan.
 * @param string $password Password yang akan didaftarkan.
 * @param string $nama Nama lengkap pelanggan.
 * @param string $email Email pelanggan.
 * @param string $no_hp Nomor HP pelanggan.
 * @return array Mengembalikan array status (true/false) dan pesan.
 */
function registerUserAsPelanggan($conn, $username, $password, $nama, $email, $no_hp) {
    // Validasi input
    if (empty($username) || empty($password) || empty($nama) || empty($email)) {
        return ['status' => false, 'message' => 'Username, password, nama, dan email tidak boleh kosong.'];
    }

    // Validasi format email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['status' => false, 'message' => 'Format email tidak valid.'];
    }

    // Cek apakah username atau email sudah terdaftar
    $stmt_check = $conn->prepare("
        SELECT u.id, u.username, p.email FROM users u
        LEFT JOIN pelanggan p ON u.id = p.user_id
        WHERE u.username = ? OR p.email = ?
    ");
    if (!$stmt_check) {
        error_log("Error preparing check user statement: " . $conn->error);
        return ['status' => false, 'message' => 'Terjadi kesalahan sistem (cek username/email).'];
    }
    $stmt_check->bind_param("ss", $username, $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    if ($result_check->num_rows > 0) {
        $message = '';
        while($row = $result_check->fetch_assoc()) {
            if ($row['username'] == $username) {
                $message .= 'Username sudah terdaftar. ';
            }
            if ($row['email'] == $email) {
                $message .= 'Email sudah terdaftar. ';
            }
        }
        $stmt_check->close();
        return ['status' => false, 'message' => trim($message)];
    }
    $stmt_check->close();

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // 1. Insert ke tabel users dengan role 'pelanggan'
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt_user = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'pelanggan')");
        if (!$stmt_user) {
            throw new Exception("Gagal menyiapkan statement user: " . $conn->error);
        }
        $stmt_user->bind_param("ss", $username, $hashed_password);
        if (!$stmt_user->execute()) {
            throw new Exception("Gagal menambahkan user: " . $stmt_user->error);
        }
        $user_id = $stmt_user->insert_id;
        $stmt_user->close();

        // 2. Insert ke tabel pelanggan dengan user_id yang baru
        $stmt_pelanggan = $conn->prepare("INSERT INTO pelanggan (user_id, nama, email, no_hp) VALUES (?, ?, ?, ?)");
        if (!$stmt_pelanggan) {
            throw new Exception("Gagal menyiapkan statement pelanggan: " . $conn->error);
        }
        $stmt_pelanggan->bind_param("isss", $user_id, $nama, $email, $no_hp);
        if (!$stmt_pelanggan->execute()) {
            throw new Exception("Gagal menambahkan data pelanggan: " . $stmt_pelanggan->error);
        }
        $stmt_pelanggan->close();

        $conn->commit();
        return ['status' => true, 'message' => 'Registrasi berhasil! Silakan login.'];
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Registrasi gagal: " . $e->getMessage());
        return ['status' => false, 'message' => 'Registrasi gagal. Silakan coba lagi nanti.'];
    }
}


/**
 * Fungsi untuk memproses login user.
 * @param mysqli $conn Objek koneksi database.
 * @param string $username Username yang dimasukkan.
 * @param string $password Password yang dimasukkan.
 * @return array Mengembalikan array status (true/false) dan pesan.
 */
function loginUser($conn, $username, $password) {
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    if (!$stmt) {
        error_log("Error preparing loginUser statement: " . $conn->error);
        return ['status' => false, 'message' => 'Terjadi kesalahan sistem saat login.'];
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Login berhasil, simpan data ke session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['loggedin'] = true; // Menandai user sudah login

            $stmt->close(); // Tutup statement setelah digunakan
            return ['status' => true, 'message' => 'Login berhasil!', 'role' => $user['role']];
        } else {
            $stmt->close();
            return ['status' => false, 'message' => 'Username atau password salah.'];
        }
    } else {
        $stmt->close();
        return ['status' => false, 'message' => 'Username atau password salah.'];
    }
}


/**
 * Fungsi untuk melakukan logout.
 * Melakukan redirect setelah logout.
 * @param string $redirect_path Jalur relatif setelah BASE_URL untuk diarahkan setelah logout (misal: 'index.php?page=login' atau 'index.php?page=home').
 */
function logoutUser($redirect_page_slug = 'login') { // Default redirect ke login via router
    // Penting: Pastikan session sudah aktif sebelum mencoba menghancurkannya.
    // session_start() sudah dilakukan di index.php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // 1. Hapus semua variabel sesi
    $_SESSION = array();

    // 2. Jika ingin menghancurkan session sepenuhnya, hapus juga cookie session.
    // Ini memastikan cookie session juga dihapus dari browser klien.
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, // Atur waktu kedaluwarsa di masa lalu
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    // 3. Terakhir, hancurkan sesi
    session_destroy();

    // 4. Redirect pengguna ke halaman yang ditentukan
    // Pastikan BASE_URL didefinisikan sebagai konstanta global
    $full_redirect_url = BASE_URL . '/' . ltrim($redirect_page_slug, '/');
    header("Location: " . $full_redirect_url);
    exit(); // Penting untuk menghentikan eksekusi skrip setelah redirect
}
?>