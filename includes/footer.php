  <footer id="footer" class="footer footer-premium">

    <div class="container">
  
      <div class="row gy-5 footer-main">
        
        <!-- COMPANY INFO -->
        <div class="col-lg-4 col-md-6">
          <div class="footer-brand">
            <img src="assets/img/juslogo.png" class="footer-logo" alt="PT JSMP Logo">
            <h4 class="footer-company">PT. Jaka Satria Mandala Putra</h4>
            <p class="footer-desc">
              Penyedia layanan keamanan profesional untuk perusahaan, kawasan industri, perumahan, dan event.
              Terlatih, bersertifikat, dan siap 24 jam.
            </p>
  
            <ul class="footer-contact">
              <li><i class="bi bi-geo-alt"></i> <?php echo get_setting('contact_address', 'Jonggol, Bogor'); ?></li>
              <li><i class="bi bi-telephone"></i> <?php echo get_setting('contact_phone', '+62 815-4693-9999'); ?></li>
              <li><i class="bi bi-envelope"></i> <?php echo get_setting('contact_email', 'ptjakasatriamandalaputra@gmail.com'); ?></li>
            </ul>
          </div>
        </div>
  
        <!-- QUICK LINKS -->
        <div class="col-lg-2 col-md-3">
          <h5 class="footer-title">Navigasi</h5>
          <ul class="footer-links">
            <li><a href="index.php#hero">Beranda</a></li>
            <li><a href="index.php#about">Tentang Kami</a></li>
            <li><a href="index.php#services">Layanan</a></li>
            <li><a href="index.php#contact">Kontak</a></li>
          </ul>
        </div>
  
        <!-- SERVICES -->
        <div class="col-lg-2 col-md-3">
          <h5 class="footer-title">Layanan Kami</h5>
          <ul class="footer-links">
            <li><a href="#">Satpam / Security</a></li>
            <li><a href="#">Keamanan Event</a></li>
            <li><a href="#">Patroli Kawasan</a></li>
            <li><a href="#">CCTV & Monitoring</a></li>
          </ul>
        </div>
  
        <!-- SOCIAL MEDIA -->
        <div class="col-lg-4 col-md-12">
          <h5 class="footer-title">Ikuti Kami</h5>
          <p class="footer-desc">Terhubung dengan kami melalui media sosial resmi.</p>
          
          <div class="footer-socials">
            <a href="#"><i class="bi bi-linkedin"></i></a>
            <a href="#"><i class="bi bi-instagram"></i></a>
            <a href="#"><i class="bi bi-facebook"></i></a>
            <a href="#"><i class="bi bi-youtube"></i></a>
          </div>
        </div>
  
      </div>
  
      <!-- COPYRIGHT -->
      <div class="footer-bottom text-center">
        <p>© 2025 <strong>PT. Jaka Satria Mandala Putra</strong> — All Rights Reserved.</p>
      </div>
  
    </div>
  </footer>
  

  <!-- FLOATING WHATSAPP -->
  <a href="https://wa.me/<?php echo str_replace(['+', '-', ' '], '', get_setting('contact_phone', '6281546939999')); ?>?text=Halo, saya ingin konsultasi layanan security JSMP dan request survey lokasi." 
     class="floating-wa shadow-lg" 
     target="_blank"
     aria-label="Contact on WhatsApp">
    <div class="wa-text">Butuh Security Sekarang?</div>
    <i class="bi bi-whatsapp"></i>
  </a>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/waypoints/noframework.waypoints.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  
  <!-- CMS & Lead Handler -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const form = document.getElementById('contactFormJSMP');
      if (!form) return;
  
      form.addEventListener('submit', function (e) {
        e.preventDefault(); 
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        
        // Loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

        const formData = new FormData(form);
  
        fetch('includes/process_lead.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            alert(data.message);
            // Buka WhatsApp di tab baru sesuai URL dari backend
            window.open(data.wa_url, '_blank');
            form.reset();
          } else {
            alert('Gagal: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Terjadi kesalahan koneksi.');
        })
        .finally(() => {
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalBtnText;
        });
      });
    });
  </script>
  
  <script>
    // Hero Image Slider (Main Section)
    new Swiper('.hero-image-slider', {
      loop: true,
      speed: 1000,
      effect: 'fade',
      fadeEffect: { crossFade: true },
      autoplay: {
        delay: 3500,
        disableOnInteraction: false,
      },
      pagination: {
        el: '.hero-image-slider .swiper-pagination',
        clickable: true,
      },
    });
  </script>
  <script>
    new Swiper('.about-slider', {
      loop: true,
      speed: 600,
      autoplay: {
        delay: 3000,
        disableOnInteraction: false
      },
      pagination: {
        el: '.about-slider .swiper-pagination',
        clickable: true
      },
      navigation: {
        nextEl: '.about-slider .swiper-button-next',
        prevEl: '.about-slider .swiper-button-prev'
      },
      effect: 'fade',
      fadeEffect: { crossFade: true },
    });
  </script>
  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>
