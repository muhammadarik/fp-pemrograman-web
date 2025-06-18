<?php
require_once 'service.php';
// include_once '../../../config/config.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = max(1, isset($_GET['page']) ? (int)$_GET['page'] : 1);
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Dapatkan data
$servicesResult = getServices($search, $perPage, $offset);
$services = $servicesResult['success'] ? $servicesResult['data'] : [];

// Hitung total data
$totalResult = getTotalServices($search);
$totalServices = $totalResult['success'] ? $totalResult['total'] : 0;
$totalPages = max(1, ceil($totalServices / $perPage));

?>

<!-- NOTIFIKASI KETIKA BERHASIL ATAU GAGAL -->
<?php if (isset($_GET['success'])): ?>
    <?php if ($_GET['success'] === 'add'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Data service berhasil <strong>ditambahkan</strong>.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif ($_GET['success'] === 'edit'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Data service berhasil <strong>diperbarui</strong>.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif ($_GET['success'] === 'delete'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Data service berhasil <strong>dihapus</strong>.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        Terjadi kesalahan saat memproses data service.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>


    <style>
        /* body {
            padding: 20px;
            background-color: #f8f9fa;
        } */
        .card {
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .search-container {
            margin-bottom: 20px;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
        }

        .page-link {
            color: #007bff;
        }

        .page-item.disabled .page-link {
            color: #6c757d;
            pointer-events: none;
            cursor: auto;
        }
        .status-badge {
            font-size: 0.85rem;
            padding: 5px 10px;
            border-radius: 20px;
        }
        .status-menunggu {
            background-color: #ffc107;
            color: #000;
        }
        .status-diproses {
            background-color: #17a2b8;
            color: #fff;
        }
        .status-selesai {
            background-color: #28a745;
            color: #fff;
        }
        .status-batal {
            background-color: #dc3545;
            color: #fff;
        }
    </style>
    <div class="container">
        <h1 class="mb-4">Service Management</h1>
        
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                            <i class="fas fa-plus"></i> Add New Service
                        </button>
                    </div>
                    <div class="col-md-6">
                        <form method="GET" action="">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" placeholder="Search by description or damage type..." value="<?= htmlspecialchars($search) ?>">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Customer</th>
                                <th>Damage Type</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Arrival Date</th>
                                <th>Technician</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($services)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">No services found</td>
                                </tr>
                            <?php else: ?>
                                <?php $counter = 1 + ($page - 1) * $perPage; ?>
                                <?php foreach ($services as $service): ?>
                                    <tr>
                                        <td><?= $counter ?></td>
                                        <td><?= htmlspecialchars($service['customer_name']) ?></td>
                                        <td><?= htmlspecialchars($service['jenis_kerusakan']) ?></td>
                                        <td><?= htmlspecialchars(substr($service['deskripsi'], 0, 50)) . (strlen($service['deskripsi']) > 50 ? '...' : '') ?></td>
                                        <td>
                                            <span class="status-badge status-<?= $service['status'] ?>">
                                                <?= ucfirst($service['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('d M Y H:i', strtotime($service['tanggal_masuk'])) ?></td>
                                        <td><?= htmlspecialchars($service['technician_name'] ?? 'N/A') ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning edit-service" data-id="<?= $service['id'] ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-service" data-id="<?= $service['id'] ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <button class="btn btn-sm btn-info view-service" data-id="<?= $service['id'] ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php $counter++; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <!-- Ganti bagian pagination dengan ini -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <!-- Previous Page Link -->
                            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>

                            <!-- Page Number Links -->
                            <?php 
                            // Tentukan range halaman yang akan ditampilkan
                            $start = max(1, $page - 2);
                            $end = min($totalPages, $page + 2);
                            
                            // Tampilkan ellipsis jika perlu
                            if ($start > 1): ?>
                                <li class="page-item"><a class="page-link" href="?page=1&search=<?= urlencode($search) ?>">1</a></li>
                                <?php if ($start > 2): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php for ($i = $start; $i <= $end; $i++): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <!-- Tampilkan ellipsis jika perlu -->
                            <?php if ($end < $totalPages): ?>
                                <?php if ($end < $totalPages - 1): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                                <li class="page-item"><a class="page-link" href="?page=<?= $totalPages ?>&search=<?= urlencode($search) ?>"><?= $totalPages ?></a></li>
                            <?php endif; ?>

                            <!-- Next Page Link -->
                            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add Service Modal -->
    <div class="modal fade" id="addServiceModal" tabindex="-1" aria-labelledby="addServiceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addServiceModalLabel">Add New Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= BASE_URL ?>admin/pages/service/service.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label for="id_pelanggan" class="form-label">Customer</label>
                            <select class="form-select" id="id_pelanggan" name="id_pelanggan" required>
                                <option value="">Select Customer</option>
                                <?php foreach (getAllCustomers() as $customer): ?>
                                    <option value="<?= $customer['id'] ?>"><?= htmlspecialchars($customer['nama']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jenis_kerusakan" class="form-label">Damage Type</label>
                            <input type="text" class="form-control" id="jenis_kerusakan" name="jenis_kerusakan" required>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Description</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="menunggu">Menunggu</option>
                                <option value="diposes">diproses</option>
                                <option value="selesai">Selesai</option>
                                <option value="batal">batal</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_masuk" class="form-label">Arrival Date</label>
                            <input type="datetime-local" class="form-control" id="tanggal_masuk" name="tanggal_masuk" required>
                        </div>
                        <div class="mb-3">
                            <label for="id_teknisi" class="form-label">Technician</label>
                            <select class="form-select" id="id_teknisi" name="id_teknisi">
                                <option value="">Select Technician</option>
                                <?php foreach (getAllTechnicians() as $technician): ?>
                                    <option value="<?= $technician['id'] ?>"><?= htmlspecialchars($technician['username']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="foto_kentsakan" class="form-label">Damage Photo</label>
                            <input class="form-control" type="file" id="foto_kentsakan" name="foto_kentsakan">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Service</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Service Modal -->
<div class="modal fade" id="editServiceModal" tabindex="-1" aria-labelledby="editServiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editServiceModalLabel">Edit Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editServiceForm" action="pages/service/service.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" id="edit_id" name="id">
                    <input type="hidden" id="edit_id_pelanggan" name="id_pelanggan">
                    
                    <!-- Customer Display (read-only) -->
                    <div class="mb-3">
                        <label class="form-label">Customer</label>
                        <input type="text" class="form-control" id="edit_customer_display" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_jenis_kerusakan" class="form-label">Damage Type</label>
                        <input type="text" class="form-control" id="edit_jenis_kerusakan" name="jenis_kerusakan" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_deskripsi" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status</label>
                        <select class="form-select" id="edit_status" name="status" required>
                            <option value="menunggu">Menunggu</option>
                            <option value="diproses">diproses</option>
                            <option value="selesai">Selesai</option>
                            <option value="batal">batal</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_tanggal_masuk" class="form-label">Arrival Date</label>
                        <input type="datetime-local" class="form-control" id="edit_tanggal_masuk" name="tanggal_masuk" disabled>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_id_teknisi" class="form-label">Technician</label>
                        <select class="form-select" id="edit_id_teknisi" name="id_teknisi">
                            <option value="">Select Technician</option>
                            <?php foreach (getAllTechnicians() as $technician): ?>
                                <option value="<?= $technician['id'] ?>"><?= htmlspecialchars($technician['username']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_foto_kerusakan" class="form-label">Damage Photo</label>
                        <input class="form-control" type="file" id="edit_foto_kerusakan" name="foto_kerusakan">
                        <div id="currentPhotoContainer" class="mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Service</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- View Service Modal -->
    <div class="modal fade" id="viewServiceModal" tabindex="-1" aria-labelledby="viewServiceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewServiceModalLabel">Service Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>ID:</strong> <span id="view_id"></span></p>
                            <p><strong>Customer:</strong> <span id="view_customer"></span></p>
                            <p><strong>Damage Type:</strong> <span id="view_jenis_kerusakan"></span></p>
                            <p><strong>Description:</strong> <span id="view_deskripsi"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status:</strong> <span id="view_status" class="status-badge"></span></p>
                            <p><strong>Arrival Date:</strong> <span id="view_tanggal_masuk"></span></p>
                            <p><strong>Update Date:</strong> <span id="view_tanggal_update"></span></p>
                            <p><strong>Technician:</strong> <span id="view_technician"></span></p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <strong>Damage Photo:</strong>
                        <div id="view_photo_container" class="mt-2"></div>
                    </div>
                    
                    <div class="mt-4">
                        <h5>Status History</h5>
                        <div id="statusHistory" class="list-group"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteServiceModal" tabindex="-1" aria-labelledby="deleteServiceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteServiceModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="deleteServiceForm" action="<?= BASE_URL ?>admin/pages/service/service.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" id="delete_id" name="id">
                        <p>Are you sure you want to delete this service?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // View Service Button Click
        document.querySelectorAll('.view-service').forEach(button => {
            button.addEventListener('click', function() {
                const serviceId = this.getAttribute('data-id');
                
                fetch(`pages/service/service.php?action=get&id=${serviceId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const service = data.service;
                            
                            document.getElementById('view_id').textContent = service.id;
                            document.getElementById('view_customer').textContent = service.customer_name;
                            document.getElementById('view_jenis_kerusakan').textContent = service.jenis_kerusakan;
                            document.getElementById('view_deskripsi').textContent = service.deskripsi;
                            
                            const statusBadge = document.getElementById('view_status');
                            statusBadge.textContent = service.status.charAt(0).toUpperCase() + service.status.slice(1);
                            statusBadge.className = 'status-badge status-' + service.status;
                            
                            document.getElementById('view_tanggal_masuk').textContent = new Date(service.tanggal_masuk).toLocaleString();
                            document.getElementById('view_tanggal_update').textContent = service.tanggal_update ? new Date(service.tanggal_update).toLocaleString() : 'N/A';
                            document.getElementById('view_technician').textContent = service.technician_name || 'N/A';
                            
                            // Show photo if exists
                            const photoContainer = document.getElementById('view_photo_container');
                            photoContainer.innerHTML = '';
                            
                            if (service.foto_kentsakan) {
                                const photoLink = document.createElement('a');
                                photoLink.href = 'uploads/' + service.foto_kentsakan;
                                photoLink.target = '_blank';
                                
                                const photoImg = document.createElement('img');
                                photoImg.src = 'uploads/' + service.foto_kentsakan;
                                photoImg.style.maxWidth = '100%';
                                photoImg.style.maxHeight = '300px';
                                photoImg.classList.add('img-thumbnail');
                                
                                photoLink.appendChild(photoImg);
                                photoContainer.appendChild(photoLink);
                            } else {
                                photoContainer.textContent = 'No photo available';
                            }
                            
                            // Load status history
                            fetch(`service.php?action=history&service_id=${serviceId}`)
                                .then(response => response.json())
                                .then(historyData => {
                                    const historyContainer = document.getElementById('statusHistory');
                                    historyContainer.innerHTML = '';
                                    
                                    if (historyData.length > 0) {
                                        historyData.forEach(item => {
                                            const historyItem = document.createElement('div');
                                            historyItem.className = 'list-group-item';
                                            
                                            const statusBadge = document.createElement('span');
                                            statusBadge.className = 'badge bg-primary me-2';
                                            statusBadge.textContent = item.status;
                                            
                                            const dateSpan = document.createElement('span');
                                            dateSpan.className = 'text-muted small me-2';
                                            dateSpan.textContent = new Date(item.updated_on).toLocaleString();
                                            
                                            const updatedBySpan = document.createElement('span');
                                            updatedBySpan.className = 'text-muted small me-2';
                                            updatedBySpan.textContent = 'by ' + item.updated_by_name;
                                            
                                            const noteDiv = document.createElement('div');
                                            noteDiv.className = 'small mt-1';
                                            noteDiv.textContent = item.catalan || 'No notes';
                                            
                                            historyItem.appendChild(statusBadge);
                                            historyItem.appendChild(dateSpan);
                                            historyItem.appendChild(updatedBySpan);
                                            historyItem.appendChild(noteDiv);
                                            
                                            historyContainer.appendChild(historyItem);
                                        });
                                    } else {
                                        historyContainer.innerHTML = '<div class="list-group-item">No status history available</div>';
                                    }
                                });
                            
                            // Show the modal
                            const viewModal = new bootstrap.Modal(document.getElementById('viewServiceModal'));
                            viewModal.show();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while fetching service data.');
                    });
            });
        });
        
        // Delete Service Button Click
        document.querySelectorAll('.delete-service').forEach(button => {
            button.addEventListener('click', function() {
                const serviceId = this.getAttribute('data-id');
                document.getElementById('delete_id').value = serviceId;
                
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteServiceModal'));
                deleteModal.show();
            });
        });
        
        // Set current datetime for the arrival date field in add form
        document.addEventListener('DOMContentLoaded', function() {
            const now = new Date();
            const formattedNow = now.toISOString().slice(0, 16);
            document.getElementById('tanggal_masuk').value = formattedNow;
        });

        // Fungsi untuk memformat waktu lokal ke format datetime-local
function getCurrentLocalDateTime() {
    const now = new Date();
    const timezoneOffset = now.getTimezoneOffset() * 60000; // offset in milliseconds
    const localISOTime = new Date(now - timezoneOffset).toISOString().slice(0, 16);
    return localISOTime;
}

// Edit Service Button Click
document.querySelectorAll('.edit-service').forEach(button => {
    button.addEventListener('click', function() {
        const serviceId = this.getAttribute('data-id');
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        
        fetch(`pages/service/service.php?action=get&id=${serviceId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const service = data.service;
                    
                    // Set form values
                    setFormValue('edit_id', service.id);
                    setFormValue('edit_id_pelanggan', service.id_pelanggan);
                    setFormValue('edit_customer_display', service.customer_name);
                    setFormValue('edit_jenis_kerusakan', service.jenis_kerusakan);
                    setFormValue('edit_deskripsi', service.deskripsi);
                    setFormValue('edit_status', service.status);
                    setFormValue('edit_id_teknisi', service.id_teknisi || '');
                    
                    // Set current time as default, but keep original time if needed
                    const currentDateTime = getCurrentLocalDateTime();
                    setFormValue('edit_tanggal_masuk', currentDateTime);
                    
                    // Show current photo if exists
                    const photoContainer = document.getElementById('currentPhotoContainer');
                    photoContainer.innerHTML = '';
                    
                    if (service.foto_kerusakan) {
                        const img = document.createElement('img');
                        img.src = `uploads/${service.foto_kerusakan}`;
                        img.style.maxWidth = '200px';
                        img.classList.add('img-thumbnail');
                        
                        const link = document.createElement('a');
                        link.href = `uploads/${service.foto_kerusakan}`;
                        link.target = '_blank';
                        link.appendChild(img);
                        
                        photoContainer.appendChild(link);
                    }
                    
                    // Show modal
                    new bootstrap.Modal(document.getElementById('editServiceModal')).show();
                } else {
                    alert('Error: ' + (data.message || 'Service not found'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error fetching service data');
            })
            .finally(() => {
                this.innerHTML = '<i class="fas fa-edit"></i>';
            });
    });
});

// Helper function to safely set form values
function setFormValue(elementId, value) {
    const element = document.getElementById(elementId);
    if (element) {
        element.value = value;
        console.log(`Set ${elementId} to:`, value);
    } else {
        console.error(`Element with ID "${elementId}" not found`);
    }
}

// Untuk form tambah data, set waktu saat ini secara default
document.addEventListener('DOMContentLoaded', function() {
    const currentDateTime = getCurrentLocalDateTime();
    document.getElementById('tanggal_masuk').value = currentDateTime;
});
    </script>