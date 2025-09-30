<?php
session_start();
require_once '../connect.php';

// ตรวจสอบสิทธิ์การเข้าถึง (ต้องเป็น admin)
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// --- Pagination Configuration ---
$records_per_page = 5; // กำหนดจำนวนรายการต่อหน้า
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $records_per_page;

// --- รับค่าการค้นหาและฟิลเตอร์ ---
$search_query = isset($_GET['search_query']) ? trim($_GET['search_query']) : '';
$filter_type = isset($_GET['filter_type']) && in_array(trim($_GET['filter_type']), ['admin', 'designer', 'client']) ? trim($_GET['filter_type']) : '';

// --- สร้าง SQL Query พื้นฐาน ---
$sql_base_select = "FROM verification_submissions vs JOIN users u ON vs.user_id = u.user_id WHERE vs.status = 'pending'";
$sql_where = "";
$params = [];
$types = '';

// --- เพิ่มเงื่อนไขการค้นหา (ถ้ามี) ---
if (!empty($search_query)) {
    $sql_where .= " AND (u.username LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)";
    $search_param = "%" . $search_query . "%";
    array_push($params, $search_param, $search_param, $search_param);
    $types .= 'sss';
}

// --- เพิ่มเงื่อนไขการกรองประเภท (ถ้ามี) ---
if (!empty($filter_type)) {
    $sql_where .= " AND u.user_type = ?";
    $params[] = $filter_type;
    $types .= 's';
}

// --- Query สำหรับนับจำนวนรายการทั้งหมด ---
$sql_count = "SELECT COUNT(vs.id) as total_records " . $sql_base_select . $sql_where;
$stmt_count = $conn->prepare($sql_count);
if (!empty($params)) {
    $stmt_count->bind_param($types, ...$params);
}
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$total_records = $result_count->fetch_assoc()['total_records'];
$stmt_count->close();

$total_pages = ceil($total_records / $records_per_page);

// --- Query สำหรับดึงข้อมูลมาแสดงผล (พร้อม Pagination) ---
$sql_submissions = "SELECT vs.id, vs.user_id, vs.document_path, vs.submitted_at, u.username, u.first_name, u.last_name, u.user_type "
    . $sql_base_select . $sql_where . " ORDER BY vs.submitted_at ASC LIMIT ? OFFSET ?";

$params[] = $records_per_page;
$params[] = $offset;
$types .= 'ii';

$stmt = $conn->prepare($sql_submissions);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$submissions = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// --- การแปลประเภทผู้ใช้ ---
$userTypeTranslation = [
    'admin' => 'ผู้ดูแลระบบ',
    'designer' => 'นักออกแบบ',
    'client' => 'ผู้ว่าจ้าง'
];

// Function to generate pagination URL
function getPaginationUrl($page, $search_query, $filter_type)
{
    $url = 'manage_verification_submissions.php?';
    $params = ['page=' . $page];
    if (!empty($search_query)) {
        $params[] = 'search_query=' . urlencode($search_query);
    }
    if (!empty($filter_type)) {
        $params[] = 'filter_type=' . urlencode($filter_type);
    }
    return $url . implode('&', $params);
}

// เรียกใช้ Header ของ Admin Panel
include 'header.php';
?>

<style>
    /* Global Font */
    body,
    h1,
    h2,
    h3,
    h4,
    h5,
    h6,
    .navbar-nav,
    .content-wrapper,
    .main-sidebar,
    .btn,
    table,
    label,
    .form-control {
        font-family: 'Kanit', sans-serif !important;
        font-weight: 400;
    }

    /* AdminLTE Layout Overrides */
    .content-wrapper {
        padding-top: 20px;
        padding-left: 20px;
        padding-right: 20px;
        padding-bottom: 20px;
        min-height: calc(100vh - (var(--main-header-height, 60px) + var(--main-footer-height, 57px)));
        display: flex;
        flex-direction: column;
    }

    .content {
        flex-grow: 1;
    }

    html,
    body {
        height: 100%;
    }

    .wrapper {
        min-height: 100%;
    }

    /* Custom Message Container */
    .message-container {
        position: fixed;
        top: 65px;
        right: 24px;
        z-index: 10000;
        pointer-events: none;
        opacity: 0;
        transform: translateZ(0);
        transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
        max-width: 350px;
        width: auto;
    }

    .message-container.show {
        opacity: 1;
        pointer-events: auto;
    }

    .message-wrapper {
        display: flex;
        align-items: center;
        padding: 9px 16px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .15);
        min-height: 40px;
    }

    .message-icon-wrapper {
        margin-right: 8px;
        display: flex;
        align-items: center;
    }

    .message-icon {
        font-size: 16px;
        line-height: 1;
    }

    .message-text {
        font-size: 14px;
        color: rgba(0, 0, 0, 0.88);
        word-break: break-word;
    }

    .success-type .message-icon {
        color: #52c41a;
    }

    .error-type .message-icon {
        color: #ff4d4f;
    }

    /* Content Container for Table and Controls */
    .content-container {
        padding: 24px;
        background: white;
        border-radius: 20px;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 10px;
    }

    /* Ant Design-like Row/Column Styling */
    .ant-row.css-ee1yud {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        margin-bottom: 20px;
    }

    .ant-row-center {
        justify-content: center;
    }

    .ant-col.ant-col-12.css-ee1yud {
        display: flex;
        align-items: center;
    }

    .ant-divider.css-ee1yud.ant-divider-horizontal {
        border-color: rgb(204, 204, 204);
        margin-top: 0px;
        margin-bottom: 0px;
        border-top-width: 1px;
        border-top-style: solid;
    }

    /* Table Styling */
    .ant-table-wrapper.css-ee1yud {
        margin-top: 20px;
    }
    
    .table-responsive-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .ant-table-thead>tr>th {
        background-color: #fafafa;
        border-bottom: 1px solid #f0f0f0;
        padding: 12px 16px;
        text-align: center;
        font-weight: 500;
        color: rgba(0, 0, 0, 0.85);
        white-space: nowrap;
    }

    .ant-table-tbody>tr>td {
        padding: 12px 16px;
        border-bottom: 1px solid #f0f0f0;
        color: rgba(0, 0, 0, 0.85);
        text-align: center;
        vertical-align: middle;
    }
    
    .btn-modern {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 400;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        cursor: pointer;
        border: 1px solid transparent;
        transition: all 0.2s cubic-bezier(0.645, 0.045, 0.355, 1);
        text-decoration: none;
    }

    .btn-primary-modern {
        color: #fff;
        background-color: #1890ff;
        border-color: #1890ff;
        box-shadow: 0 2px 0 rgba(0, 0, 0, 0.045);
    }

    .btn-primary-modern:hover {
        color: #fff;
        background-color: #40a9ff;
        border-color: #40a9ff;
    }
    
    .btn-default-modern {
        color: rgba(0, 0, 0, 0.65);
        background-color: #fff;
        border-color: #d9d9d9;
        box-shadow: 0 2px 0 rgba(0, 0, 0, 0.02);
    }

    .btn-default-modern:hover {
        color: #40a9ff;
        border-color: #40a9ff;
    }

    /* Pagination Styles */
    .ant-pagination {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
        align-items: center;
        flex-wrap: wrap;
        justify-content: center;
    }
    .ant-pagination li {
        margin: 0 4px;
        min-width: 32px;
        height: 32px;
        line-height: 30px;
        text-align: center;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s;
        border: 1px solid #d9d9d9;
        background-color: #fff;
    }
    .ant-pagination-item a {
        color: rgba(0,0,0,0.88);
        text-decoration: none;
        display: block;
    }
    .ant-pagination-item:hover {
        border-color: #1677ff;
    }
    .ant-pagination-item:hover a {
        color: #1677ff;
    }
    .ant-pagination-item-active {
        border-color: #1677ff;
        background-color: #1677ff;
    }
    .ant-pagination-item-active a {
        color: #fff;
    }
    .ant-pagination-disabled, .ant-pagination-disabled a {
        cursor: not-allowed;
        opacity: 0.6;
    }

    @media (max-width: 768px) {
        .controls-header {
            flex-direction: column;
            gap: 1rem;
            align-items: stretch !important;
        }
        .controls-header h4 {
            text-align: center;
        }
        .search-group {
            flex-direction: column;
            width: 100%;
            gap: 0.5rem !important;
        }
        .search-group .ant-input, .search-group .form-control {
            width: 100% !important;
        }
        .search-group .btn-modern {
            width: 100%;
            justify-content: center;
        }
        .hide-on-sm {
            display: none !important;
        }
    }
</style>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include 'navbar.php'; ?>
        <?php include 'sidebar_menu.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">จัดการคำขอยืนยันตัวตน</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="main.php">หน้าหลัก</a></li>
                                <li class="breadcrumb-item active">จัดการคำขอยืนยันตัวตน</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <main class="content">
                <div class="content-container">
                    <div class="d-flex justify-content-between align-items-center mb-3 controls-header">
                        <h4>รายการที่รอการตรวจสอบ</h4>
                         <div class="search-group" style="display: flex; align-items: center; gap: 5px;">
                            <input type="text" id="userSearchInput" class="form-control" placeholder="ค้นหาผู้ใช้..." value="<?= htmlspecialchars($search_query) ?>" style="width: 200px;">
                            <button id="applyMainFilter" class="btn-modern btn-primary-modern" style="padding: 6px 10px;">
                                <i class="fas fa-search"></i> <span class="d-none d-sm-inline">ค้นหา</span>
                            </button>
                            <button id="clearAllFilters" class="btn-modern btn-default-modern" style="padding: 6px 10px;">
                                <i class="fas fa-times"></i> <span class="d-none d-sm-inline">ล้าง</span>
                            </button>
                        </div>
                    </div>
                    <div class="ant-divider css-ee1yud ant-divider-horizontal" role="separator" style="border-color: rgb(204, 204, 204); margin-top: 0px; margin-bottom: 0px;"></div>
                    
                    <div class="table-responsive-wrapper mt-3">
                        <table class="table table-hover">
                            <thead class="ant-table-thead">
                                <tr>
                                    <th class="ant-table-cell">ชื่อผู้ใช้</th>
                                    <th class="ant-table-cell hide-on-sm">ชื่อจริง</th>
                                    <th class="ant-table-cell hide-on-sm">นามสกุล</th>
                                    <th class="ant-table-cell">ประเภท</th>
                                    <th class="ant-table-cell">วันที่ส่ง</th>
                                    <th class="ant-table-cell">เอกสาร</th>
                                    <th class="ant-table-cell">การดำเนินการ</th>
                                </tr>
                            </thead>
                            <tbody class="ant-table-tbody">
                                <?php if (!empty($submissions)): ?>
                                    <?php foreach ($submissions as $submission): ?>
                                        <tr>
                                            <td class="ant-table-cell"><?php echo htmlspecialchars($submission['username']); ?></td>
                                            <td class="ant-table-cell hide-on-sm"><?php echo htmlspecialchars($submission['first_name']); ?></td>
                                            <td class="ant-table-cell hide-on-sm"><?php echo htmlspecialchars($submission['last_name']); ?></td>
                                            <td class="ant-table-cell">
                                                <span class="user-type-<?= htmlspecialchars($submission['user_type']); ?>">
                                                    <?php echo htmlspecialchars($userTypeTranslation[$submission['user_type']] ?? ucfirst($submission['user_type'])); ?>
                                                </span>
                                            </td>
                                            <td class="ant-table-cell"><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($submission['submitted_at']))); ?></td>
                                            <td class="ant-table-cell">
                                                <a href="../<?php echo htmlspecialchars($submission['document_path']); ?>" target="_blank" class="btn btn-info btn-sm">ดูเอกสาร</a>
                                            </td>
                                            <td class="ant-table-cell">
                                                <button class="btn btn-success btn-sm" onclick="handleVerification(<?php echo $submission['id']; ?>, 'approved')">อนุมัติ</button>
                                                <button class="btn btn-danger btn-sm" onclick="handleVerification(<?php echo $submission['id']; ?>, 'rejected')">ปฏิเสธ</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr class="ant-table-placeholder">
                                        <td class="ant-table-cell" colspan="7">
                                            <div class="ant-empty-description">ไม่พบข้อมูลที่ตรงกับเงื่อนไข หรือยังไม่มีคำขอยืนยันตัวตน</div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($total_pages > 1): ?>
                        <div class="ant-row ant-row-center" style="margin-top: 20px;">
                            <ul class="ant-pagination">
                                <li class="ant-pagination-prev <?= ($current_page <= 1) ? 'ant-pagination-disabled' : '' ?>">
                                    <a href="<?= getPaginationUrl($current_page - 1, $search_query, $filter_type) ?>" class="ant-pagination-item-link">‹</a>
                                </li>
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="ant-pagination-item <?= ($i == $current_page) ? 'ant-pagination-item-active' : '' ?>">
                                        <a href="<?= getPaginationUrl($i, $search_query, $filter_type) ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="ant-pagination-next <?= ($current_page >= $total_pages) ? 'ant-pagination-disabled' : '' ?>">
                                    <a href="<?= getPaginationUrl($current_page + 1, $search_query, $filter_type) ?>" class="ant-pagination-item-link">›</a>
                                </li>
                            </ul>
                        </div>
                    <?php endif; ?>

                </div>
            </main>
        </div>
    </div>
    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/adminlte.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {

            function buildUrl(searchQuery, filterType, page = 1) {
                const url = new URL(window.location.href.split('?')[0]);
                url.searchParams.set('page', page);
                if (searchQuery) {
                    url.searchParams.set('search_query', searchQuery);
                }
                if (filterType) {
                    url.searchParams.set('filter_type', filterType);
                }
                return url.toString();
            }

            $('#applyMainFilter').on('click', function() {
                const searchQuery = $('#userSearchInput').val();
                const urlParams = new URLSearchParams(window.location.search);
                const typeFilter = urlParams.get('filter_type') || '';
                window.location.href = buildUrl(searchQuery, typeFilter, 1);
            });

            $('#userSearchInput').on('keypress', function(e) {
                if (e.which === 13) {
                    $('#applyMainFilter').click();
                }
            });

            $('#clearAllFilters').on('click', function() {
                window.location.href = 'manage_verification_submissions.php';
            });
        });

        function handleVerification(submissionId, status) {
        const actionText = status === 'approved' ? 'อนุมัติ' : 'ปฏิเสธ';
        Swal.fire({
            title: `ยืนยันการ${actionText}?`,
            text: `คุณต้องการที่จะ ${actionText} คำขอนี้ใช่หรือไม่?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'update_verification_status.php',
                    type: 'POST',
                    data: {
                        submission_id: submissionId,
                        status: status
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('สำเร็จ!', `คำขอได้ถูก ${actionText} แล้ว`, 'success')
                                .then(() => { location.reload(); });
                        } else {
                            Swal.fire('ผิดพลาด!', response.message || 'ไม่สามารถดำเนินการได้', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('ผิดพลาด!', 'เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์', 'error');
                    }
                });
            }
        });
    }
    </script>
</body>

</html>