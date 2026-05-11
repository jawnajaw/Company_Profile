<?php
require_once 'functions.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?php echo get_setting('site_title', 'JSMP Guard'); ?></title>
  <meta name="description" content="<?php echo get_setting('meta_description', ''); ?>">
  <meta name="keywords" content="<?php echo get_setting('meta_keywords', ''); ?>">

  <!-- Favicons -->
  <link href="assets/img/logy.png" rel="icon">
  <link href="assets/img/juslogo.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: CoreBiz
  * Template URL: https://bootstrapmade.com/corebiz-bootstrap-business-template/
  * Updated: Aug 30 2025 with Bootstrap v5.3.8
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body class="index-page">

  <header id="header" class="header sticky-top">

  <!-- TOPBAR -->
  <div class="topbar d-flex align-items-center jsmp-topbar">
    <div class="container d-flex justify-content-center justify-content-md-between align-items-center">
  
      <!-- Info singkat perusahaan (kiri) -->
      <div class="jsmp-top-info d-none d-md-flex">
        <span class="jsmp-top-tagline">
          <i class="bi bi-shield-check me-1"></i> Jasa Keamanan &amp; Pengamanan Profesional
        </span>
        <span class="jsmp-top-location d-none d-lg-inline ms-3">
          <i class="bi bi-geo-alt me-1"></i> <?php echo get_setting('contact_address'); ?>
        </span>
        <a href="admin/login.php" class="ms-3 text-gold text-decoration-none small d-none d-lg-inline"><i class="bi bi-person-lock me-1"></i> Login Admin</a>
      </div>
  
      <!-- Kontak (kanan) -->
      <div class="jsmp-top-contact fw-semibold text-nowrap">
        <span class="d-none d-sm-inline">Hubungi Kami:&nbsp;</span>
        <?php $phone = get_setting('contact_phone', '6281546939999'); ?>
        <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $phone); ?>"><?php echo $phone; ?></a>
      </div>
  
    </div>
  </div>
  
  <!-- /TOPBAR -->

  <!-- BRANDING + MENU -->
  <div class="branding d-flex align-items-center jsmp-branding">
    <div class="container position-relative d-flex align-items-center justify-content-between">
  
      <!-- LOGO + NAMA PERUSAHAAN -->
      <a href="index.php" class="logo d-flex align-items-center text-decoration-none">
        <img src="assets/img/logy.png" alt="JSMP Logo" class="jsmp-logo-img me-3">
        <div class="jsmp-logo-text d-none d-sm-block">
          <h1 class="jsmp-logo-company mb-0" style="font-size: 1.15rem; font-weight: 800; letter-spacing: 0.01em;"><?php echo strtoupper(get_setting('site_title', 'JAKA SATRIA MANDALA PUTRA')); ?></h1>
          <span class="jsmp-logo-slogan text-gold fw-bold" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.12em;">Profesionalisme & Keamanan Terjamin</span>
        </div>
      </a>
  
      <!-- NAV MENU -->
      <nav id="navmenu" class="navmenu d-flex align-items-center">
        <ul class="me-xl-4 mb-0">
          <li><a href="index.php#hero" class="active">Beranda</a></li>
          <li><a href="index.php#proyek">Proyek</a></li>
          <li><a href="index.php#paket">Penawaran</a></li>
          <li><a href="index.php#cara-kerja">Alur Kerja</a></li>
          <li><a href="index.php#contact">Kontak</a></li>
        </ul>
      
        <!-- HEADER ACTIONS -->
        <div class="header-actions d-flex align-items-center ms-lg-3">
          <a href="#contact" class="btn-urgent-header d-none d-lg-flex align-items-center">
            <span>Request Survey</span>
            <i class="bi bi-geo-alt-fill ms-2"></i>
          </a>
          <button id="theme-toggle" class="theme-btn-minimal ms-3" aria-label="Toggle Theme" style="background:none; border:none; color:var(--default-color); font-size: 1.2rem; cursor:pointer;">
            <i class="bi bi-moon-fill" id="theme-icon"></i>
          </button>
        </div>
        <i class="mobile-nav-toggle d-xl-none bi bi-list ms-3"></i>
      </nav>
      
  
    </div>
  </div>
  
  <!-- /BRANDING + MENU -->

  </header>

  <script src="assets/js/theme-control.js"></script>
