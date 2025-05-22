<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Fungsi untuk memformat angka ke format Rupiah
        function formatRupiah(angka) {
            const numberString = angka.toString();
            const sisa = numberString.length % 3;
            let rupiah = numberString.substr(0, sisa);
            const ribuan = numberString.substr(sisa).match(/\d{3}/g);

            if (ribuan) {
                const separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            return 'Rp ' + rupiah;
        }

        // Fungsi untuk mengonversi format Rupiah ke angka
        function convertToAngka(rupiah) {
            return parseFloat(rupiah.replace(/[^0-9]/g, ''));
        }

        // Terapkan format Rupiah pada input yang relevan
        const inputNominal = document.querySelector('input[name="nominal_transaksi"]');
        const inputAdminDalam = document.querySelector('input[name="admin_dalam"]');
        const inputAdminLuar = document.querySelector('input[name="admin_luar"]');
        const inputAdminBank = document.querySelector('input[name="admin_bank"]');

        // Format input nominal transaksi
        if (inputNominal) {
            inputNominal.addEventListener("input", function() {
                const angka = convertToAngka(this.value);
                this.value = formatRupiah(angka);
            });
        }

        // Format input admin dalam
        if (inputAdminDalam) {
            inputAdminDalam.addEventListener("input", function() {
                const angka = convertToAngka(this.value);
                this.value = formatRupiah(angka);
            });
        }

        // Format input admin luar
        if (inputAdminLuar) {
            inputAdminLuar.addEventListener("input", function() {
                const angka = convertToAngka(this.value);
                this.value = formatRupiah(angka);
            });
        }

        // Format input admin bank
        if (inputAdminBank) {
            inputAdminBank.addEventListener("input", function() {
                const angka = convertToAngka(this.value);
                this.value = formatRupiah(angka);
            });
        }

        // Fungsi untuk menampilkan input sesuai jenis transaksi
        function tampilkanInputTransaksi(jenis) {
            // Sembunyikan semua input terlebih dahulu
            document.querySelectorAll(".modal-body .mb-3").forEach((div) => {
                div.style.display = "none";
            });

            // Tampilkan input yang relevan berdasarkan jenis transaksi
            document.querySelector(".transaksi-nominal").style.display = "block";
            document.querySelector(".transaksi-sumber-dana").style.display = "block";
            document.querySelector(".transaksi-penerima-dana").style.display = "block";
            document.querySelector(".transaksi-admin-luar").style.display = "block";
            document.querySelector(".transaksi-keterangan").style.display = "block";

            if (jenis === "transfer") {
                document.querySelector(".transaksi-admin-bank").style.display = "block";
                document.querySelector(".Transaksi_tipe").style.display = "block";
                document.querySelector(".transaksi-admin-dalam").style.display = "block";
            } else if (jenis === "tarik_tunai") {
                document.querySelector(".transaksi-admin-dalam").style.display = "block";
            } else if (jenis === "jasa_transfer") {
                document.querySelector(".transaksi-admin-dalam").style.display = "block";
            } else if (jenis === "mode_pulsa") {
                document.querySelector(".transaksi-nomor-tujuan").style.display = "block";
            }
        }

        // Event listener untuk tombol pilih transaksi
        document.querySelectorAll(".pilih-transaksi").forEach((button) => {
            button.addEventListener("click", function() {
                const jenis = this.getAttribute("data-jenis");

                // Set judul modal sesuai jenis transaksi
                document.getElementById("modalJenisTransaksi").textContent = this.textContent;

                // Set nilai input jenis transaksi (hidden)
                document.getElementById("inputJenisTransaksi").value = jenis;

                // Tampilkan input yang relevan
                tampilkanInputTransaksi(jenis);
            });
        });

        // Konversi nilai sebelum mengirim form
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener("submit", function(e) {
                // Konversi nilai input ke angka sebelum submit
                if (inputNominal) {
                    inputNominal.value = convertToAngka(inputNominal.value);
                }
                if (inputAdminDalam) {
                    inputAdminDalam.value = convertToAngka(inputAdminDalam.value);
                }
                if (inputAdminLuar) {
                    inputAdminLuar.value = convertToAngka(inputAdminLuar.value);
                }
                if (inputAdminBank) {
                    inputAdminBank.value = convertToAngka(inputAdminBank.value);
                }
            });
        }
    });


    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("form-transaksi");
        const daftarPembelian = document.getElementById("daftar-pembelian");

        form.addEventListener("submit", function(e) {
            if (daftarPembelian.children.length === 0) {
                e.preventDefault(); // cegah pengiriman form

                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Daftar pembelian masih kosong!',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });

                return false;
            }
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.transition = 'opacity 1s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 1000);
            });
        }, 5000);
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
