<?php
session_start();
require_once '../connect.php'; 

// ตรวจสอบสิทธิ์การเข้าถึง (ต้องเป็น admin)
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// ดึงข้อมูลคำขอยืนยันตัวตนทั้งหมดที่ยังรอการตรวจสอบ (pending)
$submissions = [];
$sql = "SELECT vs.id, vs.user_id, vs.document_path, vs.submitted_at, u.username, u.first_name, u.last_name, u.user_type
        FROM verification_submissions vs
        JOIN users u ON vs.user_id = u.user_id
        WHERE vs.status = 'pending'
        ORDER BY vs.submitted_at ASC";

$result = $conn->query($sql);
if ($result) {
    $submissions = $result->fetch_all(MYSQLI_ASSOC);
}

// เรียกใช้ Header ของ Admin Panel
include 'header.php';
?>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include 'navbar.php'; ?>
        <?php include 'sidebar_menu.php'; ?>

        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>จัดการคำขอยืนยันตัวตน</h1>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">รายการที่รอการตรวจสอบ</h3>
                                </div>
                                <div class="card-body">
                                    <table id="verificationTable" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>ชื่อผู้ใช้ (Username)</th>
                                                <th>ชื่อ-นามสกุล</th>
                                                <th>ประเภท</th>
                                                <th>เอกสาร</th>
                                                <th>วันที่ส่ง</th>
                                                <th>การดำเนินการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($submissions as $sub): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($sub['id']) ?></td>
                                                <td><?= htmlspecialchars($sub['username']) ?></td>
                                                <td><?= htmlspecialchars(trim($sub['first_name'] . ' ' . $sub['last_name'])) ?></td>
                                                <td><?= htmlspecialchars(ucfirst($sub['user_type'])) ?></td>
                                                <td>
                                                    <a href="../<?= htmlspecialchars(ltrim($sub['document_path'], '/')) ?>" target="_blank" class="btn btn-sm btn-info">
                                                        <i class="fas fa-file-alt"></i> ดูเอกสาร
                                                    </a>
                                                </td>
                                                <td><?= date("d/m/Y H:i", strtotime($sub['submitted_at'])) ?></td>
                                                <td>
                                                    <a href="update_verification_status.php?id=<?= $sub['id'] ?>&user_id=<?= $sub['user_id'] ?>&action=approve" class="btn btn-sm btn-success" onclick="return confirm('คุณต้องการอนุมัติการยืนยันตัวตนนี้ใช่หรือไม่?');">
                                                        <i class="fas fa-check"></i> อนุมัติ
                                                    </a>
                                                    <a href="update_verification_status.php?id=<?= $sub['id'] ?>&action=reject" class="btn btn-sm btn-danger" onclick="return confirm('คุณต้องการปฏิเสธการยืนยันตัวตนนี้ใช่หรือไม่?');">
                                                        <i class="fas fa-times"></i> ปฏิเสธ
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                </div>
                            </div>
                    </div>
                </div>
            </section>
            </div>
        <?php include 'footer.php'; ?>
    </div>
    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="dist/js/adminlte.min.js"></script>
    <script>
        $(function () {
            $('#verificationTable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>
</body>
</html>