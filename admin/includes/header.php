<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include_once '../includes/db_connect.php';
include_once '../includes/functions.php';
$settings = get_settings();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Dashboard | PT JSMP Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="PT Jaka Satria Mandala Putra Admin Panel" name="description">
    <meta content="JSMP" name="author">
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- Cropper.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">

    <!-- plugin css -->
    <link href="assets/libs/jsvectormap/css/jsvectormap.min.css" rel="stylesheet" type="text/css">

    <!-- Bootstrap Css -->
    <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css">
    <!-- Icons Css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css">
    <!-- App Css-->
    <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css">
    <!-- Premium Admin Custom UI -->
    <link href="assets/css/premium-admin.css" rel="stylesheet" type="text/css">
</head>

<body>
    <!-- Begin page -->
    <div id="layout-wrapper">
        <header id="page-topbar" class="isvertical-topbar">
            <div class="navbar-header">
                <div class="d-flex">
                    <!-- LOGO -->
                    <div class="navbar-brand-box">
                        <a href="dashboard.php" class="logo logo-dark text-center">
                            <span class="logo-sm">
                                <span class="fw-bold text-primary">JSMP</span>
                            </span>
                            <span class="logo-lg">
                                <span class="fw-bold text-primary" style="font-size: 1.2rem;">PT JSMP</span>
                            </span>
                        </a>
                    </div>

                    <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect vertical-menu-btn">
                        <i class="bx bx-menu align-middle"></i>
                    </button>

                    <div class="page-title-box align-self-center d-none d-md-block">
                        <h4 class="page-title mb-0">Hi, <?php echo $_SESSION['admin_name'] ?? 'Admin'; ?>!</h4>
                    </div>
                </div>

                <div class="d-flex">
                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn header-item user text-start d-flex align-items-center" id="page-header-user-dropdown-v" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="rounded-circle header-profile-user" src="assets/images/users/avatar-3.jpg" alt="Header Avatar">
                            <span class="d-none d-xl-inline-block ms-2 fw-medium font-size-15"><?php echo $_SESSION['admin_name'] ?? 'Administrator'; ?></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end pt-0">
                            <div class="p-3 border-bottom">
                                <h6 class="mb-0"><?php echo $_SESSION['admin_name'] ?? 'Admin'; ?></h6>
                                <p class="mb-0 font-size-11 text-muted">admin@ptjsmp.com</p>
                            </div>
                            <a class="dropdown-item" href="logout.php"><i class="mdi mdi-logout text-muted font-size-16 align-middle me-2"></i> <span class="align-middle">Logout</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </header>
