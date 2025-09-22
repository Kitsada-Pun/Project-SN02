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

    /* Custom Message Container (Ant Design-like - Top-Right Fixed) */
    .message-container {
        position: fixed;
        top: 65px;
        /* Adjust based on your navbar height */
        right: 24px;
        z-index: 10000;
        pointer-events: none;
        /* Allows clicks to pass through when not active */
        opacity: 0;
        transform: translateZ(0);
        /* For smoother animation */
        transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
        max-width: 350px;
        /* Limit width */
        width: auto;
    }

    .message-container.show {
        opacity: 1;
        pointer-events: auto;
        /* Allow interaction when shown */
    }

    .message-wrapper {
        display: flex;
        align-items: center;
        padding: 9px 16px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .15);
        min-height: 40px;
        /* Minimum height for consistency */
    }

    .message-icon-wrapper {
        margin-right: 8px;
        display: flex;
        /* To center the icon vertically */
        align-items: center;
    }

    .message-icon {
        font-size: 16px;
        line-height: 1;
        /* Ensure icon doesn't add extra line height */
    }

    .message-text {
        font-size: 14px;
        color: rgba(0, 0, 0, 0.88);
        word-break: break-word;
        /* Ensure long messages wrap */
    }

    /* Message Type Specific Colors */
    .success-type .message-icon {
        color: #52c41a;
        /* Ant Green */
    }

    .error-type .message-icon {
        color: #ff4d4f;
        /* Ant Red */
    }

    /* You can add more types like warning, info etc. */


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

    /* Added for pagination row centering */
    .ant-row-center {
        justify-content: center;
    }

    .ant-col.ant-col-12.css-ee1yud {
        display: flex;
        align-items: center;
    }

    /* Ant Design Divider */
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

    .ant-table.css-ee1yud {
        font-size: 14px;
        border-collapse: collapse;
        width: 100%;
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
    }

    .ant-table-tbody>tr:last-child>td {
        border-bottom: none;
    }

    .ant-table-placeholder {
        text-align: center;
        color: rgba(0, 0, 0, 0.25);
        padding: 16px;
    }

    /* Button Styles (Modern/Ant Design-like) */
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

    .btn-success-modern {
        color: #fff;
        background-color: #52c41a;
        border-color: #52c41a;
        box-shadow: 0 2px 0 rgba(0, 0, 0, 0.045);
    }

    .btn-success-modern:hover {
        color: #fff;
        background-color: #73d13d;
        border-color: #73d13d;
    }

    .btn-default-modern {
        /* Added for the Clear button */
        color: rgba(0, 0, 0, 0.65);
        background-color: #fff;
        border-color: #d9d9d9;
        box-shadow: 0 2px 0 rgba(0, 0, 0, 0.02);
    }

    .btn-default-modern:hover {
        color: #40a9ff;
        border-color: #40a9ff;
    }

    /* These are the new styles for the circular buttons */
    .action-circle-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        /* Adjust size to match the image */
        height: 30px;
        /* Adjust size to match the image */
        padding: 0;
        border-radius: 50%;
        /* Makes it circular */
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.645, 0.045, 0.355, 1);
        font-size: 14px;
        /* Adjust icon size */
        margin: 0 4px;
        /* Add some space between buttons */
        box-shadow: none;
        /* Remove any default box-shadow if present */
    }

    .edit-btn {
        background-color: #fff;
        /* White background */
        border: 1px solid #d9d9d9;
        /* Light grey border */
        color: rgba(0, 0, 0, 0.65);
        /* Dark grey icon color */
    }

    .edit-btn:hover {
        border-color: #1890ff;
        /* Blue border on hover */
        color: #1890ff;
        /* Blue icon on hover */
    }

    .delete-btn {
        background-color: #fff;
        /* White background */
        border: 1px solid #ffa39e;
        /* Light red border */
        color: #ff4d4f;
        /* Red icon color */
    }

    .delete-btn:hover {
        border-color: #ff7875;
        /* Lighter red border on hover */
        color: #ff7875;
        /* Lighter red icon on hover */
    }

    /* Specific colors for user types */
    .user-type-admin {
        color: #ff4d4f;
        /* Red for Admin */
        font-weight: 500;
    }

    .user-type-designer {
        color: #1890ff;
        /* Blue for Designer */
        font-weight: 500;
    }

    .user-type-client {
        color: #52c41a;
        /* Green for Client */
        font-weight: 500;
    }

    /* Ant Design Filter Dropdown Styles for TH (Table Header) */
    .filter-dropdown-container {
        position: relative;
        display: inline-flex;
        /* Use inline-flex to center text and icon */
        align-items: center;
        justify-content: center;
        width: 100%;
        /* Ensure it takes full width of TH if text is centered */
        height: 100%;
        /* Ensure it takes full height of TH */
        box-sizing: border-box;
    }

    .ant-dropdown-trigger {
        cursor: pointer;
        display: inline-flex;
        /* To align text and icon */
        align-items: center;
        gap: 4px;
        /* Space between text and icon */
        height: 100%;
        width: 100%;
        /* Ensure trigger covers the area for clicking */
        justify-content: center;
        /* Center content within the trigger */
    }

    .ant-table-filter-trigger .anticon-filter {
        color: rgba(0, 0, 0, 0.45);
        font-size: 14px;
        line-height: 1;
        vertical-align: middle;
        transition: all 0.3s;
    }

    .ant-table-filter-trigger:hover .anticon-filter {
        color: #1890ff;
    }

    /* Style for active filter */
    .ant-table-filter-trigger.active .anticon-filter {
        color: #1890ff;
        /* Active color */
    }

    .ant-dropdown {
        position: fixed;
        /* Changed to fixed for better viewport positioning */
        z-index: 1050;
        box-sizing: border-box;
        min-width: 20px;
        opacity: 0;
        visibility: hidden;
        transform: scaleY(0.8);
        /* Initial scale for animation */
        transform-origin: top;
        /* Animation from top */
        transition: all 0.2s cubic-bezier(0.23, 1, 0.32, 1);
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 3px 6px -4px rgba(0, 0, 0, .12), 0 6px 16px 0 rgba(0, 0, 0, .08), 0 9px 28px 8px rgba(0, 0, 0, .05);
        padding: 4px 0;
        /* Padding for options */
    }

    .ant-dropdown.ant-dropdown-open {
        opacity: 1;
        visibility: visible;
        transform: scaleY(1);
    }

    .ant-table-filter-dropdown {
        width: 160px;
        /* Adjust width as needed */
        padding: 8px;
        /* Internal padding */
    }

    .ant-select-dropdown-options-list {
        max-height: 200px;
        /* Limit height of options list */
        overflow-y: auto;
        /* Enable scrolling if too many options */
    }

    .ant-select-item {
        padding: 7px 12px;
        /* Adjusted padding for better click area */
        cursor: pointer;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        transition: background-color 0.3s ease;
        font-size: 14px;
    }

    .ant-select-item:hover {
        background-color: #f5f5f5;
    }

    .ant-select-item-option-selected:not(.ant-select-item-option-disabled) {
        background-color: #e6f7ff;
        font-weight: 600;
        color: #1890ff;
        /* Selected item color */
    }

    /* Style for "Clear Filter" button in dropdown */
    .ant-dropdown-footer {
        padding: 8px 12px;
        border-top: 1px solid #f0f0f0;
        display: flex;
        justify-content: flex-end;
        /* Align to right */
        gap: 8px;
        /* Space between buttons */
        margin-top: 8px;
        /* Space from options list */
    }

    .ant-dropdown-footer button {
        padding: 4px 10px;
        font-size: 13px;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .ant-dropdown-footer .btn-link {
        background: none;
        border: none;
        color: #1890ff;
    }

    .ant-dropdown-footer .btn-link:hover {
        color: #40a9ff;
    }

    .ant-dropdown-footer .btn-primary {
        background-color: #1890ff;
        color: #fff;
        border: 1px solid #1890ff;
    }

    .ant-dropdown-footer .btn-primary:hover {
        background-color: #40a9ff;
        border-color: #40a9ff;
    }

    /* Toggle Switch Styles (Ant Design inspired) */
    .ant-switch {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        color: rgba(0, 0, 0, .88);
        font-size: 14px;
        line-height: 1;
        list-style: none;
        font-family: 'Kanit', sans-serif;
        position: relative;
        display: inline-block;
        min-width: 44px;
        /* Increased width for "อนุมัติ" / "ไม่อนุมัติ" */
        height: 22px;
        vertical-align: middle;
        border: 1px solid transparent;
        border-radius: 100px;
        cursor: pointer;
        transition: all .2s;
        user-select: none;
        background: rgba(0, 0, 0, .25);
        /* Default off background */
    }

    .ant-switch.ant-switch-checked {
        background: #1677ff;
        /* On background color */
    }

    .ant-switch-handle {
        position: absolute;
        top: 2px;
        left: 2px;
        width: 18px;
        height: 18px;
        background-color: #fff;
        border-radius: 50%;
        transition: all .2s ease-in-out;
    }

    .ant-switch-checked .ant-switch-handle {
        left: calc(100% - 20px);
        /* Adjust based on min-width and handle size */
    }

    .ant-switch-inner {
        color: #fff;
        font-size: 12px;
        /* Smaller font for text inside */
        line-height: 1;
        display: block;
        padding: 0;
        /* Remove padding */
        text-align: left;
        margin-left: 24px;
        /* Space for the handle on the left when unchecked */
        margin-right: 6px;
        /* Space from right edge when unchecked */
        transition: margin .2s ease-in-out;
    }

    .ant-switch-checked .ant-switch-inner {
        margin-left: 6px;
        /* Space from left edge when checked */
        margin-right: 24px;
        /* Space for the handle on the right when checked */
    }

    .ant-switch-inner .ant-switch-inner-checked {
        display: none;
        /* Hide 'ไม่อนุมัติ' when checked */
    }

    .ant-switch-inner .ant-switch-inner-unchecked {
        display: inline-block;
        /* Show 'ไม่อนุมัติ' when unchecked */
    }

    .ant-switch-checked .ant-switch-inner .ant-switch-inner-checked {
        display: inline-block;
        /* Show 'อนุมัติ' when checked */
    }

    .ant-switch-checked .ant-switch-inner .ant-switch-inner-unchecked {
        display: none;
        /* Hide 'อนุมัติ' when unchecked */
    }

    .ant-switch.ant-switch-disabled {
        cursor: not-allowed;
        opacity: 0.6;
        /* Dim disabled switch */
    }

    /* Ant Design Pagination Styles - Adjusted for better visibility */
    .ant-pagination {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
        align-items: center;
        flex-wrap: wrap;
        /* Allow items to wrap if screen is small */
        justify-content: center;
        /* Ensure overall centering */
    }

    .ant-pagination li {
        box-sizing: border-box;
        margin: 0 4px;
        /* Space between pagination items */
        display: inline-block;
        min-width: 32px;
        height: 32px;
        line-height: 30px;
        text-align: center;
        font-family: 'Kanit', sans-serif;
        font-size: 14px;
        vertical-align: middle;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s;
        border: 1px solid #d9d9d9;
        background-color: #fff;
    }

    .ant-pagination-item a,
    .ant-pagination-prev button,
    .ant-pagination-next button {
        color: rgba(0, 0, 0, 0.88);
        text-decoration: none;
        display: flex;
        /* Use flex for button content centering */
        align-items: center;
        justify-content: center;
        height: 100%;
        width: 100%;
        padding: 0 4px;
    }

    .ant-pagination-item:hover,
    .ant-pagination-item:focus {
        border-color: #1677ff;
    }

    .ant-pagination-item:hover a,
    .ant-pagination-item:focus a,
    .ant-pagination-prev button:hover,
    .ant-pagination-next button:hover {
        color: #1677ff;
    }

    .ant-pagination-item-active {
        border-color: #1677ff;
        background-color: #1677ff;
    }

    .ant-pagination-item-active a {
        color: #fff;
    }

    .ant-pagination-prev button,
    .ant-pagination-next button {
        border: none;
        background: transparent;
        padding: 0;
    }

    .ant-pagination-disabled {
        cursor: not-allowed;
        opacity: 0.6;
        /* Dim disabled pagination items */
    }

    .ant-pagination-disabled button {
        cursor: not-allowed;
        color: rgba(0, 0, 0, 0.25) !important;
    }

    .ant-pagination-disabled.ant-pagination-prev,
    .ant-pagination-disabled.ant-pagination-next {
        border-color: #d9d9d9;
        background-color: #fff;
    }

    .ant-pagination-jump-prev,
    .ant-pagination-jump-next {
        /* These are usually for ellipses (...) but Ant Design's basic pagination
           doesn't typically show them unless there are many pages.
           You can keep these styles if you plan to implement more complex pagination. */
        position: relative;
        margin-right: 8px;
    }

    .ant-pagination-jump-prev .ant-pagination-item-link-icon,
    .ant-pagination-jump-next .ant-pagination-item-link-icon {
        opacity: 0;
        font-size: 12px;
        transition: all 0.3s;
    }

    .ant-pagination-jump-prev:hover .ant-pagination-item-link-icon,
    .ant-pagination-jump-next:hover .ant-pagination-item-link-icon {
        opacity: 1;
    }

    .ant-pagination-jump-prev .ant-pagination-item-ellipsis,
    .ant-pagination-jump-next .ant-pagination-item-ellipsis {
        position: absolute;
        top: 0;
        left: 0;
        display: block;
        width: 100%;
        font-size: 12px;
        line-height: 30px;
        text-align: center;
        opacity: 1;
        transition: all 0.3s;
    }

    .ant-pagination-jump-prev:hover .ant-pagination-item-ellipsis,
    .ant-pagination-jump-next:hover .ant-pagination-item-ellipsis {
        opacity: 0;
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
                    <div class="ant-row css-ee1yud">
                        <div class="ant-col ant-col-12 css-ee1yud" style="justify-content: flex-start;">
                            <h4>รายการที่รอการตรวจสอบ</h4>
                        </div>
                        <div class="ant-col ant-col-12 css-ee1yud"
                            style="justify-content: flex-end; align-items: center; gap: 10px;">
                            <div style="display: flex; align-items: center; gap: 5px;">
                                <input type="text" id="userSearchInput" class="ant-input"
                                    placeholder="ค้นหาผู้ใช้..." value="<?= htmlspecialchars($search_query) ?>"
                                    style="width: 200px;">
                                <button id="applyMainFilter" class="btn-modern btn-primary-modern" style="padding: 6px 10px;">
                                    <i class="fas fa-search"></i> ค้นหา
                                </button>
                                <button id="clearAllFilters" class="btn-modern btn-default-modern"
                                    style="padding: 6px 10px;">
                                    <i class="fas fa-times"></i> ล้างทั้งหมด
                                </button>
                            </div>
                            <a href="add_user.php" class="btn-modern btn-success-modern">
                                <i class="fas fa-plus"></i> เพิ่มผู้ใช้ใหม่
                            </a>
                        </div>
                    </div>
                    <div class="ant-divider css-ee1yud ant-divider-horizontal" role="separator"
                        style="border-color: rgb(204, 204, 204); margin-top: 0px; margin-bottom: 0px;"></div>
                    <div style="margin-top: 20px;">
                        <div class="ant-table-wrapper css-ee1yud">
                            <div class="ant-spin-nested-loading css-ee1yud">
                                <div class="ant-spin-container">
                                    <div class="ant-table css-ee1yud">
                                        <div class="ant-table-container">
                                            <div class="ant-table-content">
                                                <table style="table-layout: fixed; width: 100%;">
                                                    <colgroup></colgroup>
                                                    <thead class="ant-table-thead">
                                                        <tr>
                                                            <th class="ant-table-cell" scope="col"
                                                                style="text-align: center; width: 12%;">ชื่อผู้ใช้</th>
                                                            <th class="ant-table-cell" scope="col"
                                                                style="text-align: center; width: 18%;">อีเมล</th>
                                                            <th class="ant-table-cell" scope="col"
                                                                style="text-align: center; width: 15%;">ชื่อจริง</th>
                                                            <th class="ant-table-cell" scope="col"
                                                                style="text-align: center; width: 15%;">นามสกุล</th>
                                                            <th class="ant-table-cell" scope="col"
                                                                style="text-align: center; width: 10%;">
                                                                <div class="filter-dropdown-container">
                                                                    <span role="button" tabindex="0" class="ant-dropdown-trigger ant-table-filter-trigger <?= !empty($filter_type) ? 'active' : '' ?>">
                                                                        ประเภท
                                                                        <span role="img" aria-label="filter" class="anticon anticon-filter">
                                                                            <svg viewBox="64 64 896 896" focusable="false" data-icon="filter" width="1em" height="1em" fill="currentColor" aria-hidden="true">
                                                                                <path d="M880.1 154H143.9c-24.5 0-39.8 26.7-27.5 48L349 597.4V838c0 17.7 14.2 32 31.8 32h262.4c17.6 0 31.8-14.3 31.8-32V597.4L907.7 202c12.2-21.3-3.1-48-27.6-48zM603.4 798H420.6V642h182.9v156zm9.6-236.6l-9.5 16.6h-183l-9.5-16.6L212.7 226h598.6L613 561.4z"></path>
                                                                            </svg>
                                                                        </span>
                                                                    </span>
                                                                    <div class="ant-dropdown css-ee1yud ant-dropdown-placement-bottomRight">
                                                                        <div class="ant-table-filter-dropdown">
                                                                            <div class="ant-select-dropdown-options-list">
                                                                                <?php foreach ($userTypeTranslation as $value => $label): ?>
                                                                                    <div class="ant-select-item <?= ($filter_type == $value) ? 'ant-select-item-option-selected' : '' ?>" data-value="<?= htmlspecialchars($value) ?>">
                                                                                        <?= htmlspecialchars($label) ?>
                                                                                    </div>
                                                                                <?php endforeach; ?>
                                                                            </div>
                                                                            <div class="ant-dropdown-footer">
                                                                                <button class="btn-link clear-type-filter" type="button">ล้าง</button>
                                                                                <button class="btn-primary apply-type-filter" type="button">ตกลง</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </th>
                                                            <th class="ant-table-cell" scope="col"
                                                                style="text-align: center; width: 10%;">วันที่ส่ง</th>
                                                            <th class="ant-table-cell" scope="col"
                                                                style="text-align: center; width: 8%;">เอกสาร</th>
                                                            <th class="ant-table-cell" scope="col"
                                                                style="text-align: center; width: 10%;">อนุมัติ</th>
                                                            <th class="ant-table-cell" scope="col"
                                                                style="text-align: center; width: 10%;">เข้าสู่ระบบล่าสุด</th>

                                                        </tr>
                                                    </thead>
                                                    <tbody class="ant-table-tbody">
                                                        <?php if (!empty($users)): ?>
                                                            <?php foreach ($users as $user): ?>
                                                                <tr
                                                                    data-user-id="<?php echo htmlspecialchars($user['user_id']); ?>">
                                                                    <td class="ant-table-cell">
                                                                        <?php echo htmlspecialchars($user['username']); ?>
                                                                    </td>
                                                                    <td class="ant-table-cell">
                                                                        <?php echo htmlspecialchars($user['email']); ?>
                                                                    </td>
                                                                    <td class="ant-table-cell">
                                                                        <?php echo htmlspecialchars($user['first_name']); ?>
                                                                    </td>
                                                                    <td class="ant-table-cell">
                                                                        <?php echo htmlspecialchars($user['last_name']); ?>
                                                                    </td>
                                                                    <td class="ant-table-cell">
                                                                        <span class="user-type-<?= htmlspecialchars($user['user_type']); ?>">
                                                                            <?php
                                                                            echo htmlspecialchars($userTypeTranslation[$user['user_type']] ?? ucfirst($user['user_type']));
                                                                            ?>
                                                                        </span>
                                                                    </td>
                                                                    <td class="ant-table-cell">
                                                                        <?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($user['registration_date']))); ?>
                                                                    </td>
                                                                    <td class="ant-table-cell">
                                                                        <button type="button" class="ant-switch toggle-approved"
                                                                            data-user-id="<?= htmlspecialchars($user['user_id']); ?>"
                                                                            data-is-approved="<?= $user['is_approved'] ? 'true' : 'false'; ?>"
                                                                            <?= ($user['user_id'] == $_SESSION['user_id'] || $user['user_type'] == 'admin') ? 'disabled' : ''; ?>
                                                                            style="min-width: 70px;">
                                                                            <div class="ant-switch-handle"></div>
                                                                            <span class="ant-switch-inner">
                                                                                <span class="ant-switch-inner-checked">อนุมัติ</span>
                                                                                <span class="ant-switch-inner-unchecked">ไม่อนุมัติ</span>
                                                                            </span>
                                                                        </button>
                                                                    </td>
                                                                    <td class="ant-table-cell">
                                                                        <?php echo $user['last_login'] ? htmlspecialchars(date('Y-m-d H:i', strtotime($user['last_login']))) : 'ยังไม่เคย'; ?>
                                                                    </td>
                                                                    <td class="ant-table-cell">
                                                                        <button type="button" class="action-circle-btn edit-btn"
                                                                            onclick="location.href='edit_user.php?id=<?php echo htmlspecialchars($user['user_id']); ?>'"
                                                                            title="แก้ไขข้อมูลผู้ใช้">
                                                                            <i class="fas fa-edit"></i>
                                                                        </button>
                                                                        <button type="button" class="action-circle-btn delete-btn"
                                                                            onclick="confirmDelete(<?php echo htmlspecialchars($user['user_id']); ?>)"
                                                                            title="ลบข้อมูลผู้ใช้"
                                                                            <?php if ($user['user_id'] == $_SESSION['user_id'] || $user['user_type'] == 'admin'): ?>
                                                                            disabled style="opacity: 0.5; cursor: not-allowed;"
                                                                            <?php endif; ?>>
                                                                            <i class="fas fa-trash-alt"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; // End of foreach for users 
                                                            ?>
                                                        <?php else: ?>
                                                            <tr class="ant-table-placeholder" style="display: table-row;">
                                                                <td class="ant-table-cell" colspan="9">
                                                                    <div class="css-ee1yud ant-empty ant-empty-normal">
                                                                        <div class="ant-empty-image">
                                                                            <svg width="64" height="41" viewBox="0 0 64 41"
                                                                                xmlns="http://www.w3.org/2000/svg">
                                                                                <title>Simple Empty</title>
                                                                                <g transform="translate(0 1)" fill="none"
                                                                                    fill-rule="evenodd">
                                                                                    <ellipse fill="#f5f5f5" cx="32" cy="33"
                                                                                        rx="32" ry="7"></ellipse>
                                                                                    <g fill-rule="nonzero" stroke="#d9d9d9">
                                                                                        <path
                                                                                            d="M55 12.76L44.854 1.258C44.367.474 43.656 0 42.907 0H21.093c-.749 0-1.46.474-1.947 1.257L9 12.761V22h46v-9.24z">
                                                                                        </path>
                                                                                        <path
                                                                                            d="M41.613 15.931c0-1.605.994-2.93 2.227-2.931H55v18.137C55 33.26 53.68 35 52.05 35h-40.1C10.32 35 9 33.259 9 31.137V13h11.16c1.233 0 2.227 1.323 2.227 2.928v.022c0 1.605 1.005 2.901 2.237 2.901h14.752c1.232 0 2.237-1.308 2.237-2.913v-.007z"
                                                                                            fill="#fafafa"></path>
                                                                                    </g>
                                                                                </g>
                                                                            </svg>
                                                                        </div>
                                                                        <div class="ant-empty-description">ยังไม่มีเอกสารขอยืนยันตัวตน</div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endif; // End of if (!empty($users)) 
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
        </div>
        <script src="plugins/jquery/jquery.min.js"></script>
        <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
        <script src="dist/js/adminlte.min.js"></script>
        <script>
            $(function() {
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