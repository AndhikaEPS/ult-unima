// Toggle menu mobile
document.addEventListener('DOMContentLoaded', function () {
  var burger = document.getElementById('hamburger');
  var navLinks = document.getElementById('navLinks');
  if (burger) {
    burger.addEventListener('click', function () {
      navLinks.classList.toggle('open');
    });
  }

  // Preview nama file pada upload box
  var fileInput = document.getElementById('file_dokumen');
  var uploadBox = document.getElementById('uploadBox');
  if (fileInput && uploadBox) {
    fileInput.addEventListener('change', function () {
      if (fileInput.files.length > 0) {
        uploadBox.classList.add('has-file');
        uploadBox.querySelector('.upload-text').textContent = '✓ ' + fileInput.files[0].name;
      } else {
        uploadBox.classList.remove('has-file');
      }
    });
  }

  // Isi ulang dropdown sub-layanan saat kategori dipilih
  var kategoriSelect = document.getElementById('kategori_id');
  var subLayananSelect = document.getElementById('sub_layanan_id');
  if (kategoriSelect && subLayananSelect) {
    kategoriSelect.addEventListener('change', function () {
      var kategoriId = kategoriSelect.value;
      subLayananSelect.innerHTML = '<option value="">Memuat...</option>';
      if (!kategoriId) {
        subLayananSelect.innerHTML = '<option value="">-- Pilih kategori terlebih dahulu --</option>';
        return;
      }
      fetch('api/sub_layanan.php?kategori_id=' + encodeURIComponent(kategoriId))
        .then(function (res) { return res.json(); })
        .then(function (data) {
          subLayananSelect.innerHTML = '<option value="">-- Pilih layanan --</option>';
          data.forEach(function (item) {
            var opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = item.nama_layanan;
            subLayananSelect.appendChild(opt);
          });
        })
        .catch(function () {
          subLayananSelect.innerHTML = '<option value="">Gagal memuat layanan</option>';
        });
    });
  }

  // Live update statistik beranda (auto refresh tiap 10 detik)
  var elSedang = document.getElementById('stat-sedang-dilayani');
  var elTotal = document.getElementById('stat-total-menunggu');
  if (elSedang || elTotal) {
    function refreshStats() {
      fetch('api/stats.php')
        .then(function (res) { return res.json(); })
        .then(function (data) {
          if (elSedang) elSedang.textContent = data.sedang_dilayani || '-';
          if (elTotal) elTotal.textContent = data.total_menunggu ?? 0;
        })
        .catch(function () {});
    }
    refreshStats();
    setInterval(refreshStats, 10000);
  }

  // Live update papan antrian (monitor)
  var monitorBox = document.getElementById('monitorContent');
  if (monitorBox) {
    function refreshMonitor() {
      fetch('api/monitor.php')
        .then(function (res) { return res.json(); })
        .then(function (data) {
          document.getElementById('monitor-current-no').textContent = data.sedang_dilayani || '---';
          var listEl = document.getElementById('monitor-list');
          listEl.innerHTML = '';
          data.antrian_berikutnya.forEach(function (item) {
            var div = document.createElement('div');
            div.className = 'item';
            div.textContent = item;
            listEl.appendChild(div);
          });
        })
        .catch(function () {});
    }
    refreshMonitor();
    setInterval(refreshMonitor, 5000);
  }
});
