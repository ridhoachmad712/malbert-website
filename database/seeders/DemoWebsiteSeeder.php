<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\SchoolProfile;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class DemoWebsiteSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            throw new RuntimeException('Data contoh hanya boleh dimuat di lingkungan lokal atau pengujian.');
        }

        $admin = User::where('role', 'admin')->first();

        if (! $admin) {
            throw new RuntimeException('Buat akun admin terlebih dahulu sebelum memuat data contoh.');
        }

        foreach (['hero', 'literasi', 'kebun', 'karya', 'kegiatan'] as $image) {
            Storage::disk('public')->put("demo/{$image}.svg", file_get_contents(resource_path("demo/{$image}.svg")));
        }

        $school = SchoolProfile::current();

        if ($school->name === 'Nama sekolah belum diisi') {
            $school->update([
                'name' => 'SDI M Albert (Data Contoh)',
                'tagline' => 'Belajar dengan rasa ingin tahu, bertumbuh bersama (data contoh).',
                'description' => 'Ini adalah profil fiktif untuk melihat rancangan website. Sekolah dalam contoh ini mendampingi peserta didik melalui pembelajaran yang hangat, kegiatan membaca, eksplorasi lingkungan, dan karya kreatif. Ganti seluruh narasi ini dengan informasi resmi sebelum website dipublikasikan.',
                'history' => 'Cerita berdirinya sekolah, perkembangan layanan, dan tonggak penting dapat ditulis di bagian ini. Teks yang sedang tampil hanyalah contoh pengisian.',
                'vision' => 'Menjadi ruang belajar yang aman, terbuka, dan mendorong setiap anak untuk berkembang sesuai potensinya. (Contoh, bukan visi resmi.)',
                'mission' => "Membangun kebiasaan membaca dan bertanya.\nMengajak anak belajar melalui pengalaman langsung.\nMenumbuhkan sikap saling menghargai dan bertanggung jawab.\nMelibatkan keluarga dalam perjalanan belajar.\n(Seluruh butir merupakan contoh.)",
                'programs' => "Pojok baca dan berbagi cerita.\nProyek kebun sekolah.\nPekan karya dan seni.\nKegiatan kolaborasi bersama keluarga.\n(Data program contoh.)",
                'facilities' => "Ruang belajar dan sudut baca.\nArea bermain dan kebun belajar.\nRuang kegiatan bersama.\n(Data fasilitas contoh; sesuaikan dengan kondisi sekolah.)",
                'address' => 'Alamat sekolah belum diisi — data contoh',
                'hero_path' => 'demo/hero.svg',
            ]);
        }

        if (! $school->principal_name && ! $school->principal_greeting) {
            $school->update([
                'principal_name' => 'Alya Pramesti (Contoh)',
                'principal_greeting' => 'Ini sambutan contoh, bukan pernyataan kepala sekolah yang sebenarnya. Selamat datang di ruang informasi sekolah. Kami berharap setiap anak merasa aman untuk belajar, bertanya, dan berkarya. Ganti nama serta teks ini dengan sambutan resmi sebelum website dipublikasikan.',
            ]);
        }

        if (! $school->admission_intro && ! $school->admission_requirements && ! $school->admission_steps && ! $school->admission_schedule) {
            $school->update([
                'admission_intro' => 'Contoh informasi penerimaan murid. Jadwal dan ketentuan resmi belum ditetapkan sekolah.',
                'admission_requirements' => 'Contoh: siapkan dokumen identitas calon murid dan orang tua. Daftar persyaratan resmi harus diperiksa kembali pada pengumuman sekolah.',
                'admission_steps' => 'Contoh: baca pengumuman resmi, lengkapi dokumen, lalu hubungi panitia penerimaan melalui kontak sekolah.',
                'admission_schedule' => 'Belum ada jadwal resmi. Tanggal dalam contoh ini tidak boleh dipakai untuk mendaftar.',
            ]);
        }

        $teachers = [
            ['Alya Pramesti (Contoh)', 'contoh-alya-pramesti', 'Kepala sekolah', 'Kepemimpinan sekolah', 'Profil fiktif. Mendampingi tim sekolah dalam membangun budaya belajar yang ramah, tertib, dan terbuka terhadap gagasan baru.'],
            ['Nadia Putri (Contoh)', 'contoh-nadia-putri', 'Guru kelas', 'Pembelajaran kelas awal', 'Profil fiktif. Menyukai kegiatan bercerita, permainan bahasa, dan pengamatan sederhana untuk membantu anak percaya diri saat belajar.'],
            ['Rafi Mahendra (Contoh)', 'contoh-rafi-mahendra', 'Guru kelas', 'Pembelajaran kelas atas', 'Profil fiktif. Mengajak anak memecahkan masalah bersama dan menghubungkan pelajaran dengan kehidupan sehari-hari.'],
            ['Siti Rahma (Contoh)', 'contoh-siti-rahma', 'Guru pendidikan agama', 'Pembinaan karakter', 'Profil fiktif. Memadukan percakapan, refleksi, dan teladan sehari-hari dalam pendampingan peserta didik.'],
            ['Dimas Ardiansyah (Contoh)', 'contoh-dimas-ardiansyah', 'Guru olahraga', 'Kegiatan gerak dan kesehatan', 'Profil fiktif. Mengembangkan kegiatan gerak yang inklusif dan menyenangkan bagi seluruh peserta didik.'],
            ['Mira Lestari (Contoh)', 'contoh-mira-lestari', 'Guru seni', 'Seni dan kreativitas', 'Profil fiktif. Mendampingi anak bereksperimen dengan warna, bentuk, musik, dan kerja kelompok.'],
        ];

        foreach ($teachers as $index => [$name, $slug, $position, $assignment, $bio]) {
            Teacher::firstOrCreate(['slug' => $slug], [
                'name' => $name,
                'position' => $position,
                'assignment' => $assignment,
                'bio' => $bio,
                'publication_approved' => true,
                'is_published' => true,
                'sort_order' => $index + 1,
            ]);
        }

        $posts = [
            ['news', 'pekan-literasi', 'Pekan literasi mengajak anak berbagi cerita', 'Kegiatan membaca bersama menjadi ruang untuk mendengar, bertanya, dan menemukan buku baru.', 'Setiap kelas dalam contoh kegiatan ini menyiapkan sudut baca dan memilih cerita untuk dibagikan. Anak-anak bergiliran menceritakan tokoh favorit, sementara guru membantu mereka bertanya dan menyampaikan pendapat. Ini adalah berita fiktif untuk pratinjau.', 'literasi', true, 1],
            ['news', 'belajar-di-kebun', 'Belajar mengenal tanaman di kebun sekolah', 'Pengamatan sederhana membantu anak memahami perubahan di sekitar mereka.', 'Peserta didik dalam contoh ini mengamati bentuk daun, mencatat pertumbuhan tanaman, dan mendiskusikan cara merawat lingkungan. Kegiatan dan narasi ini bersifat fiktif.', 'kebun', false, 4],
            ['news', 'pameran-karya', 'Karya siswa mewarnai ruang belajar', 'Ragam hasil belajar ditampilkan sebagai bentuk apresiasi terhadap proses anak.', 'Pada pekan karya contoh, anak-anak menampilkan gambar, tulisan pendek, dan hasil proyek kelompok. Ini bukan laporan kegiatan nyata.', 'karya', false, 8],
            ['news', 'kolaborasi-keluarga', 'Keluarga dan sekolah belajar bersama', 'Pertemuan contoh ini mempertemukan orang tua dan guru untuk berbagi praktik pendampingan belajar.', 'Sekolah dan keluarga dalam cerita fiktif ini berdiskusi mengenai rutinitas membaca di rumah serta cara memberi ruang bagi anak untuk bercerita.', 'kegiatan', false, 12],
            ['announcement', 'pertemuan-orang-tua', 'Jadwal pertemuan orang tua', 'Contoh pengumuman untuk memperlihatkan tata letak informasi penting.', 'Ini adalah pengumuman fiktif. Tanggal, waktu, tempat, dan peserta pertemuan belum ditetapkan. Ganti dengan pemberitahuan resmi sekolah.', null, false, 2],
            ['announcement', 'pengumpulan-buku', 'Pengumpulan buku bacaan', 'Ajakan contoh untuk memperkaya sudut baca kelas.', 'Pengumuman ini hanyalah data contoh. Jika sekolah memiliki program serupa, cantumkan jenis buku, kondisi, cara penyerahan, dan kontak resmi yang benar.', null, false, 6],
            ['announcement', 'layanan-administrasi', 'Informasi layanan administrasi', 'Lihat bagaimana informasi layanan tampil di halaman pengumuman.', 'Jam dan cara layanan administrasi belum diisi karena data resmi belum diberikan. Ganti tulisan contoh ini sebelum membagikan website.', null, false, 10],
            ['gallery', 'galeri-literasi', 'Suasana pekan literasi', 'Galeri ilustrasi untuk pratinjau kegiatan membaca.', 'Gambar di bawah adalah ilustrasi, bukan dokumentasi peserta didik atau kegiatan nyata.', 'literasi', false, 3],
            ['gallery', 'galeri-kebun', 'Eksplorasi kebun belajar', 'Galeri ilustrasi untuk pratinjau kegiatan lingkungan.', 'Gambar di bawah adalah ilustrasi, bukan dokumentasi kegiatan nyata.', 'kebun', false, 7],
            ['gallery', 'galeri-karya', 'Pekan karya kreatif', 'Galeri ilustrasi untuk pratinjau karya dan seni.', 'Gambar di bawah adalah ilustrasi, bukan dokumentasi kegiatan nyata.', 'karya', false, 11],
            ['program', 'program-literasi', 'Program literasi kelas', 'Contoh halaman program; rincian ini belum diverifikasi sekolah.', 'Contoh: kelas menyediakan waktu membaca bersama dan berbagi cerita. Ganti tujuan, jadwal, penanggung jawab, dan dokumentasi dengan data resmi sekolah sebelum dipublikasikan.', 'literasi', false, 2],
            ['facility', 'fasilitas-sudut-baca', 'Sudut baca', 'Contoh halaman fasilitas; keberadaannya perlu dikonfirmasi sekolah.', 'Ini ilustrasi fasilitas, bukan pernyataan bahwa sekolah memiliki sarana ini. Ganti dengan nama fasilitas, kondisi, lokasi, dan foto sebenarnya setelah diperiksa.', 'literasi', false, 2],
            ['event', 'agenda-pertemuan-keluarga', 'Pertemuan keluarga dan sekolah', 'Contoh agenda; tanggal dan tempat belum ditetapkan secara resmi.', 'Agenda ini fiktif untuk menunjukkan tampilan jadwal. Jangan gunakan sebagai undangan atau pengumuman resmi.', null, false, 2],
            ['achievement', 'prestasi-karya-anak', 'Apresiasi karya peserta didik', 'Contoh prestasi; bukan capaian nyata sekolah atau murid.', 'Ini contoh tampilan untuk data prestasi. Sebelum menerbitkan prestasi nyata, periksa nama kegiatan, penyelenggara, tanggal, kategori, dan izin publikasi murid.', 'karya', false, 2],
            ['faq', 'faq-pendaftaran', 'Bagaimana memperoleh informasi pendaftaran?', 'Contoh pertanyaan umum.', 'Ini jawaban contoh. Lihat halaman penerimaan murid dan hubungi sekolah melalui kontak resmi untuk informasi yang berlaku.', null, false, 2],
            ['faq', 'faq-jadwal', 'Di mana jadwal kegiatan sekolah diumumkan?', 'Contoh pertanyaan umum.', 'Ini jawaban contoh. Jadwal yang sudah disahkan sekolah dapat dilihat pada halaman Agenda dan Pengumuman.', null, false, 2],
        ];

        foreach ($posts as [$type, $slug, $title, $summary, $body, $image, $featured, $daysAgo]) {
            Post::firstOrCreate(['slug' => "contoh-{$slug}"], [
                'user_id' => $admin->id,
                'type' => $type,
                'title' => "Contoh: {$title}",
                'summary' => $summary,
                'body' => $body,
                'cover_path' => $image ? "demo/{$image}.svg" : null,
                'cover_alt' => $image ? 'Ilustrasi data contoh, bukan foto kegiatan nyata' : null,
                'gallery_paths' => $type === 'gallery' ? ["demo/{$image}.svg", 'demo/kegiatan.svg'] : null,
                'status' => 'published',
                'is_featured' => $featured,
                'published_at' => now()->subDays($daysAgo),
                'event_date' => $type === 'event' ? now()->addDays(30)->toDateString() : null,
            ]);
        }
    }
}
