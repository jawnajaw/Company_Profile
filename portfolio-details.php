<?php 
require_once 'includes/header.php'; 
?>

<main class="main">

  <!-- Page Title -->
  <div class="page-title" data-aos="fade">
    <div class="container d-lg-flex justify-content-between align-items-center">
      <h1 class="mb-2 mb-lg-0">Portofolio Detail</h1>
      <nav class="breadcrumbs">
        <ol>
          <li><a href="index.php">Beranda</a></li>
          <li class="current">Portofolio Detail</li>
        </ol>
      </nav>
    </div>
  </div><!-- End Page Title -->

  <!-- Portfolio Details Section -->
  <section id="portfolio-details" class="portfolio-details section">

    <div class="container" data-aos="fade-up" data-aos-delay="100">

      <div class="project-hero">
        <div class="hero-content" data-aos="fade-up">
          <div class="project-category">Layanan Pengamanan Terpadu</div>
          <h1 class="project-title">Pengamanan Kawasan Industri & Komersial</h1>
          <p class="project-subtitle">Dokumentasi pengamanan dan prosedur operasional standar (SOP) yang kami terapkan di berbagai lokasi klien strategis kami.</p>
        </div>

        <div class="project-meta-grid" data-aos="fade-up" data-aos-delay="200">
          <div class="meta-column">
            <div class="meta-label">Klien</div>
            <div class="meta-value">Berbagai Perusahaan Nasional</div>
          </div>
          <div class="meta-column">
            <div class="meta-label">Durasi Kontrak</div>
            <div class="meta-value">Tahunan (Long-term)</div>
          </div>
          <div class="meta-column">
            <div class="meta-label">Lokasi</div>
            <div class="meta-value">Jawa Barat & Sekitarnya</div>
          </div>
          <div class="meta-column">
            <div class="meta-label">Layanan</div>
            <div class="meta-value">Security Guard, Patrol, Monitoring</div>
          </div>
        </div>
      </div>

      <div class="visual-showcase" data-aos="zoom-in" data-aos-delay="100">
        <div class="main-visual">
          <div class="portfolio-details-slider swiper init-swiper">
            <script type="application/json" class="swiper-config">
              {
                "loop": true,
                "speed": 600,
                "autoplay": {
                  "delay": 4000
                },
                "effect": "creative",
                "creativeEffect": {
                  "prev": { "shadow": false, "translate": ["-120%", 0, -500] },
                  "next": { "shadow": false, "translate": ["120%", 0, -500] }
                },
                "slidesPerView": 1,
                "navigation": {
                  "nextEl": ".swiper-button-next",
                  "prevEl": ".swiper-button-prev"
                }
              }
            </script>
            <div class="swiper-wrapper">
              <div class="swiper-slide">
                <img src="assets/img/portfolio/portfolio-5.webp" alt="JSMP Security" class="img-fluid" loading="lazy">
              </div>
              <div class="swiper-slide">
                <img src="assets/img/portfolio/portfolio-7.webp" alt="JSMP Security" class="img-fluid" loading="lazy">
              </div>
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
          </div>
        </div>
      </div>

      <div class="content-section">
        <div class="row">
          <div class="col-lg-8 offset-lg-2">
            <div class="project-overview" data-aos="fade-up">
              <h2>Gambaran Proyek</h2>
              <p class="overview-text">PT. Jaka Satria Mandala Putra berkomitmen memberikan pelayanan terbaik dalam menjaga aset vital klien melalui penempatan personil yang disiplin dan terlatih.</p>
            </div>
          </div>
        </div>
      </div>

    </div>

  </section><!-- /Portfolio Details Section -->

</main>

<?php require_once 'includes/footer.php'; ?>
