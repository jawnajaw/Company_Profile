<?php 
require_once 'includes/header.php'; 

// Fetch All Dynamic Data
$hero = get_hero_settings();
$stats = get_all_stats();
$advantages = get_all_advantages();
$work_steps = get_all_work_steps();
$packages = get_all_packages();
$services = get_all_services();
$projects_stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC LIMIT 6");
$team_members = get_team_members();

// SEO & Site Settings
$site_title = get_setting('site_title');
$meta_desc = get_setting('meta_description');
$meta_keys = get_setting('meta_keywords');
?>

<main class="main">

  <!-- Dynamic Hero Section -->
  <?php 
    $hero_slides = $pdo->query("SELECT * FROM hero_slides ORDER BY id DESC")->fetchAll();
  ?>
  <section id="hero" class="hero section">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-6">
          <div class="hero-content">
            <div class="d-flex align-items-center gap-2 mb-3">
              <?php if(isset($hero['badge_1']) && $hero['badge_1']): ?><span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill"><?php echo htmlspecialchars($hero['badge_1']); ?></span><?php endif; ?>
              <?php if(isset($hero['badge_2']) && $hero['badge_2']): ?><span class="badge bg-secondary text-white fw-bold px-3 py-2 rounded-pill"><?php echo htmlspecialchars($hero['badge_2']); ?></span><?php endif; ?>
            </div>
            <h1 class="display-3 fw-bold mb-4"><?php echo $hero['headline'] ?? 'Butuh Security Sekarang?'; ?></h1>
            <p class="lead text-theme-muted mb-5 fs-5">
              <?php echo htmlspecialchars($hero['subheadline'] ?? ''); ?>
            </p>
            <div class="d-flex flex-wrap gap-3">
              <a href="<?php echo htmlspecialchars($hero['cta_link'] ?? '#contact'); ?>" class="btn-elite-primary btn-lg shadow-lg px-4"><?php echo htmlspecialchars($hero['cta_text'] ?? 'Hubungi Kami'); ?> <i class="bi bi-shield-fill ms-2"></i></a>
              <a href="#simulasi" class="btn-elite-outline btn-lg px-4">Simulasi Kebutuhan</a>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="hero-image-stack position-relative">
            <div class="main-image-wrap glass-elite p-2 shadow-lg floating">
              <div class="swiper hero-image-slider rounded-4 overflow-hidden">
                <div class="swiper-wrapper">
                  <?php if(empty($hero_slides)): ?>
                    <div class="swiper-slide">
                      <img src="<?php echo $hero['image'] ?? 'assets/img/bg/srengseng.jpeg'; ?>" class="img-fluid rounded-4 w-100" alt="JSMP Elite Guard" loading="lazy">
                    </div>
                  <?php else: ?>
                    <?php foreach($hero_slides as $slide): ?>
                    <div class="swiper-slide">
                      <img src="<?php echo htmlspecialchars($slide['image_path']); ?>" class="img-fluid rounded-4 w-100" alt="JSMP Elite Guard" loading="lazy" style="aspect-ratio: 4/3; object-fit: cover;">
                    </div>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </div>
                <?php if(!empty($hero_slides)): ?><div class="swiper-pagination"></div><?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Dynamic Achievement Counters -->
  <section id="stats" class="stats section py-5 border-bottom border-secondary border-opacity-10">
    <div class="container">
      <div class="row g-4 text-center">
        <?php foreach($stats as $stat): ?>
        <div class="col-6 col-lg-3">
          <div class="stat-item p-3">
            <h2 class="display-4 fw-bold text-theme-heading mb-0"><?php echo htmlspecialchars($stat['count_value']); ?></h2>
            <p class="text-gold fw-semibold mb-0"><?php echo htmlspecialchars($stat['label']); ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Dynamic Why Choose Us Section -->
  <section id="why-jsmp" class="why-jsmp section py-5">
    <div class="container text-center mb-5">
      <h5 class="text-gold fw-bold letter-spacing-3 mb-2">KEUNGGULAN KAMI</h5>
      <h2 class="display-5 fw-bold">Pilihan Utama Sektor Korporat</h2>
    </div>
    <div class="container">
      <div class="row g-4">
        <?php foreach($advantages as $adv): ?>
        <div class="col-lg-4 col-md-6">
          <div class="glass-elite p-4 h-100 border-start border-warning border-4 text-center">
            <?php if(isset($adv['image']) && $adv['image']): ?>
                <img src="<?php echo htmlspecialchars($adv['image']); ?>" alt="<?php echo htmlspecialchars($adv['title']); ?>" style="height: 60px;" class="mb-3 grayscale" loading="lazy">
            <?php else: ?>
                <i class="bi <?php echo htmlspecialchars($adv['icon'] ?? 'bi-shield-check'); ?> text-gold display-4 mb-3 d-block"></i>
            <?php endif; ?>
            <h5 class="fw-bold mb-2"><?php echo htmlspecialchars($adv['title']); ?></h5>
            <p class="text-muted small mb-0"><?php echo htmlspecialchars($adv['description']); ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Dynamic How We Work Section -->
  <section id="cara-kerja" class="cara-kerja section py-5 bg-light-theme">
    <div class="container text-center mb-5">
      <h5 class="text-gold fw-bold letter-spacing-3 mb-2">PROFESIONALISME</h5>
      <h2 class="display-5 fw-bold">Langkah Menuju Keamanan Maksimal</h2>
    </div>
    <div class="container">
      <div class="row g-4 justify-content-center">
        <?php foreach($work_steps as $step): ?>
        <div class="col-lg-3 col-md-6">
          <div class="process-step text-center">
            <div class="step-number"><?php echo htmlspecialchars($step['step_number']); ?></div>
            <h5 class="fw-bold mt-3"><?php echo htmlspecialchars($step['title']); ?></h5>
            <p class="text-muted small"><?php echo htmlspecialchars($step['description']); ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Dynamic Paket Penawaran Section -->
  <section id="paket" class="section py-5">
    <div class="container text-center mb-5">
      <h5 class="text-gold fw-bold letter-spacing-3 mb-2">PENAWARAN KHUSUS</h5>
      <h2 class="display-5 fw-bold">Pilih Paket Sesuai Kebutuhan Anda</h2>
    </div>
    <div class="container">
      <div class="row g-4 justify-content-center">
        <?php foreach($packages as $pkg): ?>
        <div class="col-lg-4">
          <div class="glass-elite p-5 package-card h-100 <?php echo $pkg['is_featured'] ? 'featured' : ''; ?>">
            <?php if(isset($pkg['badge_label']) && $pkg['badge_label']): ?>
                <div class="featured-label"><?php echo htmlspecialchars($pkg['badge_label']); ?></div>
            <?php endif; ?>
            <h3 class="fw-bold"><?php echo htmlspecialchars($pkg['name']); ?></h3>
            <p class="text-gold fw-bold small"><?php echo htmlspecialchars($pkg['subtitle']); ?></p>
            <hr class="border-secondary opacity-25">
            <ul class="list-unstyled mb-5">
              <?php 
              $features = explode('|', $pkg['features']);
              foreach($features as $feat): 
              ?>
                <li class="mb-3"><i class="bi bi-check-circle-fill text-gold me-2"></i> <?php echo htmlspecialchars(trim($feat)); ?></li>
              <?php endforeach; ?>
            </ul>
            <a href="#contact" class="<?php echo $pkg['is_featured'] ? 'btn-elite-primary' : 'btn-elite-outline'; ?> w-100 justify-content-center"><?php echo htmlspecialchars($pkg['cta_text']); ?></a>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Simulasi Kebutuhan Section -->
  <?php 
  $sim_stmt = $pdo->query("SELECT * FROM simulation_options ORDER BY location_name ASC");
  $sim_options = $sim_stmt->fetchAll();
  ?>
  <section id="simulasi" class="section py-5">
    <div class="container">
      <div class="glass-elite p-5 shadow-lg">
        <div class="row align-items-center">
          <div class="col-lg-6 mb-4 mb-lg-0">
            <h2 class="fw-bold mb-3">Berapa Personel Security yang Anda Butuhkan?</h2>
            <p class="text-muted mb-4">Gunakan simulator cepat kami untuk mendapatkan rekomendasi awal jumlah tim pengamanan aset Anda.</p>
            
            <div class="mb-4">
                <label class="form-label fw-bold">Jenis Lokasi:</label>
                <select class="form-select form-control" id="simulasi-tipe">
                    <option value="" disabled selected>Pilih Lokasi</option>
                    <?php foreach($sim_options as $opt): ?>
                    <option value="<?php echo $opt['multiplier']; ?>"><?php echo htmlspecialchars($opt['location_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="form-label fw-bold">Jumlah Titik Masuk/Gate:</label>
                <input type="number" class="form-control" id="simulasi-gate" value="1" min="1">
            </div>
 
            <button class="btn-elite-primary w-100" onclick="hitungSimulasi()">Hitung Rekomendasi</button>
          </div>
          <div class="col-lg-6">
            <div class="p-4 rounded-4 border border-warning border-2 bg-warning bg-opacity-10 text-center" id="hasil-simulasi" style="display:none;">
                <h5 class="fw-bold mb-2">Rekomendasi Kami:</h5>
                <div class="display-1 fw-bold text-gold" id="rekomendasi-angka">4-6</div>
                <p class="fw-bold mb-4">Personel Terlatih Gada Pratama</p>
                <hr>
                <p class="small text-muted mb-4">*Ini adalah estimasi awal. Kami sarankan survey lokasi untuk hasil yang akurat.</p>
                <a href="#contact" class="btn btn-dark w-100 py-3 rounded-pill">Dapatkan Penawaran Resmi</a>
            </div>
            <div id="simulasi-placeholder" class="text-center p-5 opacity-50">
                <i class="bi bi-calculator fs-1 text-gold mb-3 d-block"></i>
                <p>Isi data di samping untuk melihat rekomendasi kebutuhan personel Anda.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
 
  <!-- Dynamic Case Study Section -->
  <?php 
    $projects_stmt = $pdo->query("SELECT * FROM projects ORDER BY id DESC");
    $all_projects = $projects_stmt->fetchAll();
    
    // Fetch all gallery images at once for efficiency
    $gallery_stmt = $pdo->query("SELECT * FROM project_images");
    $gallery_images = [];
    while($img = $gallery_stmt->fetch()) {
        $gallery_images[$img['project_id']][] = $img['image_path'];
    }
  ?>
  <section id="proyek" class="section py-5 light-background">
    <div class="container text-center mb-5">
      <h5 class="text-gold fw-bold letter-spacing-3 mb-2">BUKTI NYATA</h5>
      <h2 class="display-5 fw-bold">Case Study & Keberhasilan Proyek</h2>
      <p class="text-muted mx-auto" style="max-width: 700px;">Kami tidak hanya menjual janji, tapi memberikan hasil nyata melalui strategi pengamanan yang terukur dan personel yang berdedikasi tinggi.</p>
    </div>
    <div class="container">
      <div class="row g-4">
        <?php foreach ($all_projects as $row): ?>
        <div class="col-lg-4 col-md-6">
          <div class="case-study-card h-100 shadow-sm border-0 bg-theme-surface overflow-hidden">
            <div class="position-relative">
              <img src="<?php echo htmlspecialchars($row['image']); ?>" class="img-fluid w-100" style="height: 240px; object-fit: cover;" alt="<?php echo htmlspecialchars($row['title']); ?>" loading="lazy">
              <span class="badge-premium-category"><?php echo htmlspecialchars($row['category']); ?></span>
              
              <?php if(isset($gallery_images[$row['id']])): ?>
              <button class="btn-gallery-trigger" onclick='openPublicGallery(<?php echo json_encode($gallery_images[$row['id']]); ?>, "<?php echo addslashes($row['title']); ?>")'>
                <i class="bi bi-images"></i> <span>View Gallery (<?php echo count($gallery_images[$row['id']]); ?>)</span>
              </button>
              <?php endif; ?>
            </div>
            <div class="p-4">
              <h5 class="fw-bold mb-3"><?php echo htmlspecialchars($row['title']); ?></h5>
              <div class="d-flex gap-2 mb-3">
                 <div class="flex-grow-1 p-2 bg-light dark-bg-subtle rounded text-center">
                    <div class="fw-bold text-gold small"><?php echo htmlspecialchars($row['incident_rate']); ?></div>
                    <div class="x-small text-muted">Incident Rate</div>
                 </div>
                 <div class="flex-grow-1 p-2 bg-light dark-bg-subtle rounded text-center">
                    <div class="fw-bold text-gold small"><?php echo htmlspecialchars($row['patrol_status']); ?></div>
                    <div class="x-small text-muted">Status</div>
                 </div>
              </div>
              <p class="small text-muted mb-2"><strong>Masalah:</strong> <?php echo htmlspecialchars($row['problem']); ?></p>
              <p class="small text-muted mb-3"><strong>Solusi:</strong> <?php echo htmlspecialchars($row['solution']); ?></p>
              <hr class="opacity-10">
              <div class="text-success fw-bold small"><i class="bi bi-check2-circle me-1"></i> <?php echo htmlspecialchars($row['result']); ?></div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Public Gallery Modal -->
  <div class="modal fade" id="publicGalleryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content bg-dark border-0 rounded-4 overflow-hidden">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title text-white fw-bold" id="publicGalleryTitle">Project Gallery</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div id="publicGalleryContent" class="row g-3">
             <!-- Images will be injected here -->
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
  function openPublicGallery(images, title) {
      const modal = new bootstrap.Modal(document.getElementById('publicGalleryModal'));
      document.getElementById('publicGalleryTitle').innerText = title + ' - Photo Documentation';
      const container = document.getElementById('publicGalleryContent');
      container.innerHTML = '';
      
      images.forEach(path => {
          container.innerHTML += `
              <div class="col-lg-4 col-md-6">
                  <div class="rounded-4 overflow-hidden border border-secondary shadow-lg">
                      <img src="${path}" class="w-100 h-100 object-fit-cover" style="min-height: 250px;" loading="lazy">
                  </div>
              </div>
          `;
      });
      
      modal.show();
  }
  </script>

  <!-- Dynamic Services Section -->
  <section id="featured-services" class="featured-services section py-5 light-background">
    <div class="container text-center mb-5">
      <h5 class="text-gold fw-bold letter-spacing-3 mb-2">SOLUSI TERPADU</h5>
      <h2 class="display-5 fw-bold">Layanan Utama Kami</h2>
      <p class="text-muted mx-auto" style="max-width: 700px;">Kami menyediakan berbagai layanan pengamanan profesional yang disesuaikan dengan kebutuhan spesifik sektor bisnis Anda.</p>
    </div>
    <div class="container">
      <div class="row g-4">
        <?php foreach($services as $svc): ?>
        <div class="col-lg-4">
          <div class="glass-elite p-4 text-center">
            <div class="text-gold fs-1 mb-3"><i class="bi <?php echo htmlspecialchars($svc['icon'] ?? 'bi-shield-check'); ?>"></i></div>
            <h4 class="fw-bold mb-3"><?php echo htmlspecialchars($svc['title']); ?></h4>
            <p class="text-muted"><?php echo htmlspecialchars($svc['description']); ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Dynamic About Section -->
  <?php 
    $about = $pdo->query("SELECT * FROM about_section WHERE id=1")->fetch();
    $about_images = $pdo->query("SELECT * FROM about_images ORDER BY sort_order ASC, id DESC")->fetchAll();
  ?>
  <section id="about" class="about section py-5">
    <div class="container text-center mb-5">
      <h5 class="text-gold fw-bold letter-spacing-3 mb-2">PROFIL PERUSAHAAN</h5>
      <h2 class="display-5 fw-bold">Mengenal PT JSMP Lebih Dekat</h2>
    </div>
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-6">
          <?php if(!empty($about_images)): ?>
            <div id="aboutCarousel" class="carousel slide carousel-fade shadow-lg rounded-4 overflow-hidden" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php foreach($about_images as $index => $img): ?>
                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>" data-bs-interval="4000">
                        <img src="<?php echo $img['image_path']; ?>" class="d-block w-100" alt="JSMP Activity" style="height: 600px; object-fit: cover;">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php if(count($about_images) > 1): ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#aboutCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#aboutCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
                <?php endif; ?>
            </div>
          <?php else: ?>
            <img src="assets/img/about/aza3.jpeg" class="img-fluid rounded-3 shadow-sm" alt="Tentang JSMP" loading="lazy">
          <?php endif; ?>
        </div>
        <div class="col-lg-6">
          <h2 class="fw-bold mb-4"><?php echo htmlspecialchars($about['title']); ?></h2>
          <p class="text-muted mb-4"><?php echo nl2br(htmlspecialchars($about['description'])); ?></p>
          <div class="row g-4">
            <div class="col-6">
              <h4 class="text-gold fw-bold mb-0"><?php echo htmlspecialchars($about['stat1_value']); ?></h4>
              <p class="small text-muted"><?php echo htmlspecialchars($about['stat1_label']); ?></p>
            </div>
            <div class="col-6">
              <h4 class="text-gold fw-bold mb-0"><?php echo htmlspecialchars($about['stat2_value']); ?></h4>
              <p class="small text-muted"><?php echo htmlspecialchars($about['stat2_label']); ?></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Dynamic Team Section -->
  <section id="team" class="team section py-5 light-background">
    <div class="container text-center mb-5">
      <h5 class="text-gold fw-bold letter-spacing-3 mb-2">MANAJEMEN</h5>
      <h2 class="display-5 fw-bold">Struktur Komando</h2>
      <p class="text-muted">Kepemimpinan yang Berorientasi pada Disiplin & Integritas</p>
    </div>
    <div class="container">
      <div class="row gy-5 justify-content-center">
        <?php foreach ($team_members as $member): ?>
        <div class="col-lg-3 col-md-6">
          <div class="member-card-elite">
            <div class="member-image-wrapper">
              <span class="member-rank"><?php echo htmlspecialchars($member['rank']); ?></span>
              <div class="member-image-inner">
                <img src="<?php echo htmlspecialchars($member['image']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($member['name']); ?>" loading="lazy">
              </div>
            </div>
            <div class="member-content">
              <h4 class="member-name"><?php echo htmlspecialchars($member['name']); ?></h4>
              <span class="member-role"><?php echo htmlspecialchars($member['role']); ?></span>
              <p class="member-bio"><?php echo htmlspecialchars($member['bio']); ?></p>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Contact Section -->
  <section id="contact" class="contact section py-5">
    <div class="container">
      <div class="row justify-content-center text-center mb-5">
        <div class="col-lg-8">
          <h5 class="text-gold fw-bold letter-spacing-3 mb-2">HUBUNGI KAMI</h5>
          <h2 class="display-5 fw-bold">Siap Mengamankan Aset Anda</h2>
          <p class="text-muted">Layanan operasional dan konsultasi tersedia 24/7 di seluruh wilayah cakupan kami.</p>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-lg-4">
          <div class="contact-info-clean p-4 glass-elite h-100">
            <div class="d-flex mb-4">
              <i class="bi bi-geo-alt text-gold fs-3 me-3"></i>
              <div>
                <h5 class="fw-bold mb-1">Alamat Kantor</h5>
                <?php $maps_url = get_setting('contact_maps'); ?>
                <a href="<?php echo $maps_url ?: '#'; ?>" target="_blank" class="text-decoration-none small text-muted mb-0 d-block"><?php echo get_setting('contact_address'); ?></a>
              </div>
            </div>
            <div class="d-flex mb-4">
              <i class="bi bi-whatsapp text-gold fs-3 me-3"></i>
              <div>
                <h5 class="fw-bold mb-1">WhatsApp</h5>
                <?php 
                  $phone_raw = get_setting('contact_phone');
                  $phone_clean = preg_replace('/[^0-9]/', '', $phone_raw);
                  if (substr($phone_clean, 0, 1) === '0') $phone_clean = '62' . substr($phone_clean, 1);
                ?>
                <a href="https://wa.me/<?php echo $phone_clean; ?>" target="_blank" class="text-decoration-none small text-muted mb-0 d-block"><?php echo $phone_raw; ?></a>
              </div>
            </div>
            <div class="d-flex mb-4">
              <i class="bi bi-envelope text-gold fs-3 me-3"></i>
              <div>
                <h5 class="fw-bold mb-1">Email</h5>
                <a href="mailto:<?php echo get_setting('contact_email'); ?>" class="text-decoration-none small text-muted mb-0 d-block"><?php echo get_setting('contact_email'); ?></a>
              </div>
            </div>
            <div class="d-flex mb-0">
              <i class="bi bi-clock text-gold fs-3 me-3"></i>
              <div>
                <h5 class="fw-bold mb-1">Jam Operasional</h5>
                <p class="small text-muted mb-0"><?php echo get_setting('contact_hours'); ?></p>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-8">
          <div class="glass-elite p-4">
            <form id="contactFormJSMP" class="php-email-form">
              <div class="row g-3">
                <div class="col-md-6">
                  <input type="text" name="name" class="form-control" placeholder="Nama Lengkap" required>
                </div>
                <div class="col-md-6">
                  <input type="email" name="email" class="form-control" placeholder="Email Anda" required>
                </div>
                <div class="col-md-6">
                  <input type="text" name="phone" class="form-control" placeholder="Nomor WhatsApp" required>
                </div>
                <div class="col-md-6">
                  <select name="service" class="form-select" required>
                    <option value="" disabled selected>Pilih Layanan</option>
                    <?php foreach($services as $svc): ?>
                    <option value="<?php echo htmlspecialchars($svc['title']); ?>"><?php echo htmlspecialchars($svc['title']); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-12">
                  <textarea name="message" class="form-control" rows="5" placeholder="Ceritakan detail kebutuhan pengamanan Anda..." required></textarea>
                </div>
                <div class="col-md-12 text-center">
                  <button type="submit" class="btn-elite-primary w-100 py-3">Kirim Pesan Sekarang</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

</main>

<script>
function hitungSimulasi() {
    const multiplier = parseFloat(document.getElementById('simulasi-tipe').value);
    if(isNaN(multiplier)) { alert('Silakan pilih jenis lokasi!'); return; }

    const gate = parseInt(document.getElementById('simulasi-gate').value) || 1;
    
    // Logic: Base calculation (2-4 per gate) scaled by multiplier
    let baseMin = 2 * gate;
    let baseMax = 4 * gate;
    
    let min = Math.floor(baseMin * multiplier);
    let max = Math.ceil(baseMax * multiplier);

    document.getElementById('rekomendasi-angka').innerText = `${min}-${max}`;
    document.getElementById('hasil-simulasi').style.display = 'block';
    document.getElementById('simulasi-placeholder').style.display = 'none';
    if(window.innerWidth < 992) { document.getElementById('hasil-simulasi').scrollIntoView({ behavior: 'smooth' }); }
}
</script>

<?php require_once 'includes/footer.php'; ?>
