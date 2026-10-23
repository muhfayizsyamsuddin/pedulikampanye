# PeduliKampanye AI

PeduliKampanye AI adalah chatbot berbasis kecerdasan buatan yang membantu organisasi sosial menyusun draf kampanye donasi, membuat FAQ untuk calon donatur, serta memeriksa kelengkapan dan transparansi informasi kampanye.

Project ini dikembangkan untuk memenuhi tugas **Day 18 — OpenRouter and Chatbots** dari Maxy Academy.

## Fitur

- Membuat draf kampanye donasi yang terstruktur.
- Menyusun FAQ untuk calon donatur.
- Membuat checklist transparansi kampanye.
- Menandai informasi yang belum tersedia dengan `[Perlu dikonfirmasi]`.
- Memilih model AI melalui OpenRouter.
- Menyimpan riwayat percakapan dan pesan ke MySQL.
- Membuka kembali percakapan yang tersimpan.
- Menghapus percakapan melalui modal konfirmasi.
- Menampilkan respons AI dengan format Markdown.
- Antarmuka web responsif dengan area chat dan sidebar yang dapat digulir secara terpisah.

## Teknologi

- PHP 8.3+
- Laravel 13
- Blade
- Tailwind CSS
- JavaScript
- MySQL
- OpenRouter API
- Marked
- DOMPurify

## Model AI

Model yang tersedia dikonfigurasi melalui `config/services.php`:

- NVIDIA Nemotron 3.5 Lightning
- NVIDIA Nemotron 3.5 Super VL
- Cohere North Mini Code

Ketersediaan model dan keberhasilan permintaan bergantung pada provider OpenRouter serta batas penggunaan model gratis.

## Cara Menggunakan

1. Buka aplikasi PeduliKampanye AI.
2. Pilih model AI melalui bagian Pengaturan AI.
3. Masukkan instruksi pada kolom percakapan.
4. Gunakan chatbot untuk membuat draf kampanye, menyusun FAQ, atau memeriksa transparansi.
5. Buka riwayat untuk melihat kembali percakapan sebelumnya.
6. Gunakan tombol hapus dan modal konfirmasi untuk menghapus percakapan yang tidak diperlukan.
7. Periksa kembali seluruh informasi yang dihasilkan AI sebelum digunakan atau dipublikasikan.

## Batasan

- PeduliKampanye AI merupakan prototipe asisten penyusunan komunikasi kampanye, bukan platform penggalangan dana.
- Hasil AI dapat mengandung kesalahan dan perlu diverifikasi oleh pengguna.
- Informasi terkait nominal donasi, mekanisme pembayaran, penerima manfaat, dan kebijakan organisasi tidak boleh dianggap sebagai fakta tanpa konfirmasi.
- Model gratis dapat mengalami pembatasan penggunaan atau gangguan ketersediaan.
- Aplikasi saat ini berfokus pada percakapan AI dan penyimpanan riwayat chat; belum mencakup autentikasi pengguna dan pengelolaan kampanye secara terstruktur.


## Kontributor

Muh. Fayiz Syamsuddin

## Konteks Tugas

Maxy Academy — Day 18: OpenRouter and Chatbots.