<?php
/**
 * KSSM SPM Prinsip Perakaunan syllabus seeder. Tingkatan 4 (9 bab —
 * asas perakaunan, dokumen, lejar, imbangan duga, penyata kewangan
 * milikan tunggal) + Tingkatan 5 (7 bab — analisis penyata, rekod tak
 * lengkap, kawalan dalaman, perkongsian, syarikat berhad, kelab, kos)
 * — 16 bab total, dengan starter formula / nisbah / persamaan bank.
 *
 * Idempotent: bab matched by (subject_id, name, form_level); legacy
 * same-named NULL-form bab are adopted.
 *
 *   php public_html/cron/seed_perakaunan_kssm.php
 */
declare(strict_types=1);

require_once __DIR__ . '/../inc/db.php';

function perakaunan_kssm_catalog(): array
{
    return [
        // ============= TINGKATAN 4 =============
        ['form' => 4, 'name' => 'Pengenalan kepada Perakaunan', 'subtopics' => [
            'Sejarah dan Perkembangan Perakaunan',
            'Tujuan dan Kepentingan Perakaunan',
            'Bidang Kerjaya dalam Perakaunan',
            'Etika dan Tanggungjawab Akauntan',
            'Jenis Perniagaan dan Aktivitinya',
        ], 'skills' => [
            ['Menjelaskan konsep dan kepentingan perakaunan', 'easy'],
            ['Mengenal pasti bidang kerjaya dalam perakaunan', 'easy'],
            ['Menerangkan tanggungjawab akauntan', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Klasifikasi Akaun dan Persamaan Perakaunan', 'subtopics' => [
            'Klasifikasi Akaun (Aset, Liabiliti, Ekuiti, Hasil, Belanja)',
            'Persamaan Perakaunan',
            'Kesan Urus Niaga ke atas Persamaan',
            'Akaun Kontra',
        ], 'skills' => [
            ['Mengklasifikasikan akaun', 'medium'],
            ['Menggunakan persamaan perakaunan', 'medium'],
            ['Menganalisis kesan urus niaga ke atas akaun', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Dokumen Perniagaan Sebagai Sumber Maklumat', 'subtopics' => [
            'Dokumen Sumber Dalaman (Memo, Resit, Baucar)',
            'Dokumen Sumber Luaran (Invois, Nota Debit, Nota Kredit, Penyata Akaun)',
            'Fungsi Setiap Dokumen',
            'Aliran Dokumen dalam Urus Niaga',
        ], 'skills' => [
            ['Mengenal pasti jenis dokumen perniagaan', 'easy'],
            ['Menerangkan fungsi setiap dokumen', 'medium'],
            ['Mentafsir maklumat dalam dokumen', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Buku Catatan Pertama', 'subtopics' => [
            'Jurnal Am', 'Jurnal Jualan', 'Jurnal Belian',
            'Jurnal Pulangan Jualan', 'Jurnal Pulangan Belian',
            'Buku Tunai dan Buku Tunai Runcit',
        ], 'skills' => [
            ['Mencatat urus niaga dalam jurnal khas', 'medium'],
            ['Menyediakan buku tunai dua lajur dan tiga lajur', 'hard'],
            ['Menyediakan buku tunai runcit (sistem panjar)', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Lejar', 'subtopics' => [
            'Konsep Lejar', 'Lejar Am dan Lejar Subsidiari',
            'Pengeposan dari Jurnal ke Lejar',
            'Mengimbangkan Akaun',
        ], 'skills' => [
            ['Mengepos catatan ke lejar', 'medium'],
            ['Mengimbangkan akaun lejar', 'medium'],
            ['Membezakan lejar am dan lejar subsidiari', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Imbangan Duga', 'subtopics' => [
            'Tujuan Imbangan Duga',
            'Format dan Penyediaan Imbangan Duga',
            'Kesilapan yang Tidak Dapat Dikesan',
            'Kesilapan yang Dapat Dikesan',
        ], 'skills' => [
            ['Menyediakan imbangan duga', 'medium'],
            ['Mengenal pasti jenis kesilapan', 'medium'],
            ['Menganalisis had imbangan duga', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Penyata Kewangan Milikan Tunggal tanpa Pelarasan', 'subtopics' => [
            'Penyata Pendapatan (Akaun Untung Rugi)',
            'Penyata Kedudukan Kewangan',
            'Format Penyata Kewangan',
            'Pengiraan Untung Kasar dan Untung Bersih',
        ], 'skills' => [
            ['Menyediakan penyata pendapatan', 'medium'],
            ['Menyediakan penyata kedudukan kewangan', 'medium'],
            ['Mengira untung kasar dan untung bersih', 'medium'],
        ]],
        ['form' => 4, 'name' => 'Pelarasan Pada Tarikh Imbangan dan Penyediaan Penyata Kewangan Milikan Tunggal', 'subtopics' => [
            'Belanja Terakru dan Terdahulu',
            'Hasil Terakru dan Terdahulu',
            'Susut Nilai Aset Bukan Semasa',
            'Hutang Lapuk dan Peruntukan Hutang Ragu',
            'Penyediaan Penyata Kewangan dengan Pelarasan',
        ], 'skills' => [
            ['Membuat catatan pelarasan akhir tahun', 'hard'],
            ['Mengira susut nilai (garis lurus, baki berkurangan)', 'hard'],
            ['Menyediakan penyata kewangan dengan pelarasan', 'hard'],
        ]],
        ['form' => 4, 'name' => 'Pembetulan Kesilapan', 'subtopics' => [
            'Jenis Kesilapan',
            'Akaun Penggantungan',
            'Pembetulan dan Catatan Jurnal',
            'Kesan kepada Untung dan Penyata Kewangan',
        ], 'skills' => [
            ['Mengenal pasti dan membetulkan kesilapan', 'medium'],
            ['Menyediakan akaun penggantungan', 'hard'],
            ['Menganalisis kesan kesilapan ke atas penyata kewangan', 'medium'],
        ]],

        // ============= TINGKATAN 5 =============
        ['form' => 5, 'name' => 'Analisis dan Tafsiran Penyata Kewangan untuk Membuat Keputusan', 'subtopics' => [
            'Tujuan Analisis Penyata Kewangan',
            'Nisbah Keberuntungan',
            'Nisbah Kecekapan',
            'Nisbah Kecairan / Mudah Tunai',
            'Mentafsir Keputusan Nisbah',
        ], 'skills' => [
            ['Mengira nisbah kewangan utama', 'medium'],
            ['Mentafsir nisbah untuk membuat keputusan', 'hard'],
            ['Membanding prestasi antara tempoh', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Rekod Tak Lengkap', 'subtopics' => [
            'Konsep Rekod Tak Lengkap',
            'Pengiraan Untung dengan Kaedah Perbandingan Modal',
            'Pengiraan Item daripada Maklumat Tidak Lengkap',
            'Penyediaan Penyata Kewangan dari Rekod Tak Lengkap',
        ], 'skills' => [
            ['Mengira untung kasar/bersih dari rekod tak lengkap', 'hard'],
            ['Menyediakan penyata kewangan dari maklumat separa', 'hard'],
            ['Mengenal pasti maklumat yang hilang', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Perakaunan untuk Kawalan Dalaman', 'subtopics' => [
            'Kawalan Tunai dan Prosedur Asas',
            'Penyata Penyesuaian Bank',
            'Belanjawan Tunai',
            'Akaun Kawalan Penghutang dan Pemiutang',
        ], 'skills' => [
            ['Menyediakan penyata penyesuaian bank', 'hard'],
            ['Menyediakan belanjawan tunai', 'hard'],
            ['Menyediakan akaun kawalan', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Perakaunan untuk Perkongsian', 'subtopics' => [
            'Ciri-ciri Perniagaan Perkongsian',
            'Akta Perkongsian 1961 dan Perjanjian Perkongsian',
            'Akaun Pengasingan Untung Rugi',
            'Akaun Semasa dan Modal Pekongsi',
            'Penyata Kewangan Perkongsian',
            'Pembubaran Perkongsian',
        ], 'skills' => [
            ['Menyediakan akaun pengasingan untung rugi', 'hard'],
            ['Menyediakan akaun semasa pekongsi', 'medium'],
            ['Membuat catatan pembubaran perkongsian', 'hard'],
        ]],
        ['form' => 5, 'name' => 'Perakaunan untuk Syarikat Berhad Menurut Syer', 'subtopics' => [
            'Ciri-ciri Syarikat Berhad',
            'Modal Syer (Syer Biasa, Syer Keutamaan)',
            'Terbitan Syer dan Debentur',
            'Penyata Pendapatan Syarikat',
            'Penyata Kedudukan Kewangan Syarikat',
            'Dividen dan Rizab',
        ], 'skills' => [
            ['Mencatat terbitan syer dan debentur', 'hard'],
            ['Menyediakan penyata kewangan syarikat berhad', 'hard'],
            ['Mengira dividen dan peruntukan rizab', 'medium'],
        ]],
        ['form' => 5, 'name' => 'Perakaunan untuk Kelab dan Persatuan', 'subtopics' => [
            'Ciri Pertubuhan Bukan Untung',
            'Akaun Penerimaan dan Pembayaran',
            'Akaun Yuran Ahli',
            'Akaun Pendapatan dan Perbelanjaan',
            'Penyata Kedudukan Kewangan Kelab',
        ], 'skills' => [
            ['Menyediakan akaun penerimaan dan pembayaran', 'medium'],
            ['Menyediakan akaun yuran ahli', 'medium'],
            ['Menyediakan akaun pendapatan dan perbelanjaan', 'hard'],
        ]],
        ['form' => 5, 'name' => 'Perakaunan Kos', 'subtopics' => [
            'Jenis Kos (Kos Tetap, Kos Berubah, Kos Bercampur)',
            'Kerja dalam Proses',
            'Akaun Pengeluaran',
            'Analisis Titik Pulang Modal (BEP)',
            'Margin Caruman',
        ], 'skills' => [
            ['Mengklasifikasikan kos pengeluaran', 'medium'],
            ['Menyediakan akaun pengeluaran', 'hard'],
            ['Mengira titik pulang modal dan margin caruman', 'hard'],
        ]],
    ];
}

/** Starter formula / ratio / persamaan bank. */
function perakaunan_kssm_formulas(): array
{
    return [
        // topic name, formula_name, formula, description, category
        ['Klasifikasi Akaun dan Persamaan Perakaunan', 'Persamaan Perakaunan Asas', 'Aset = Liabiliti + Ekuiti Pemilik', 'Persamaan asas yang menjadi tulang belakang semua catatan perakaunan.', 'persamaan'],
        ['Klasifikasi Akaun dan Persamaan Perakaunan', 'Persamaan Perakaunan (Lanjutan)', 'Aset = Liabiliti + Modal + Hasil − Belanja − Ambilan', 'Persamaan diperluas dengan unsur hasil, belanja dan ambilan pemilik.', 'persamaan'],
        ['Penyata Kewangan Milikan Tunggal tanpa Pelarasan', 'Kos Jualan', 'Kos Jualan = Stok Awal + Belian Bersih − Stok Akhir', 'Belian Bersih = Belian + Angkutan Masuk − Pulangan Belian.', 'penyata_pendapatan'],
        ['Penyata Kewangan Milikan Tunggal tanpa Pelarasan', 'Untung Kasar', 'Untung Kasar = Jualan Bersih − Kos Jualan', 'Jualan Bersih = Jualan − Pulangan Jualan.', 'penyata_pendapatan'],
        ['Penyata Kewangan Milikan Tunggal tanpa Pelarasan', 'Untung Bersih', 'Untung Bersih = Untung Kasar + Hasil Lain − Jumlah Belanja', 'Untung sebelum pelarasan akhir tahun.', 'penyata_pendapatan'],
        ['Pelarasan Pada Tarikh Imbangan dan Penyediaan Penyata Kewangan Milikan Tunggal', 'Susut Nilai — Garis Lurus', 'Susut Nilai = (Kos − Nilai Sisa) ÷ Jangka Hayat', 'Susut nilai tetap setiap tahun sepanjang hayat aset.', 'susut_nilai'],
        ['Pelarasan Pada Tarikh Imbangan dan Penyediaan Penyata Kewangan Milikan Tunggal', 'Susut Nilai — Baki Berkurangan', 'Susut Nilai = Nilai Buku × Kadar %', 'Susut nilai berdasarkan baki nilai buku setiap tahun.', 'susut_nilai'],
        ['Pelarasan Pada Tarikh Imbangan dan Penyediaan Penyata Kewangan Milikan Tunggal', 'Peruntukan Hutang Ragu', 'PHR = Penghutang Bersih × Kadar %', 'Anggaran hutang yang mungkin tidak dapat dikutip.', 'pelarasan'],
        ['Analisis dan Tafsiran Penyata Kewangan untuk Membuat Keputusan', 'Margin Untung Kasar', 'Margin UK = (Untung Kasar ÷ Jualan Bersih) × 100%', 'Peratus untung kasar daripada jualan.', 'nisbah_untung'],
        ['Analisis dan Tafsiran Penyata Kewangan untuk Membuat Keputusan', 'Margin Untung Bersih', 'Margin UB = (Untung Bersih ÷ Jualan Bersih) × 100%', 'Peratus untung bersih daripada jualan.', 'nisbah_untung'],
        ['Analisis dan Tafsiran Penyata Kewangan untuk Membuat Keputusan', 'Pulangan atas Modal Pekerjaan (ROCE)', 'ROCE = (Untung Bersih ÷ Modal Pekerjaan) × 100%', 'Kecekapan penggunaan modal untuk menjana untung.', 'nisbah_untung'],
        ['Analisis dan Tafsiran Penyata Kewangan untuk Membuat Keputusan', 'Pusing Ganti Inventori', 'Pusing Ganti = Kos Jualan ÷ Stok Purata', 'Berapa kali stok diganti dalam tempoh.', 'nisbah_kecekapan'],
        ['Analisis dan Tafsiran Penyata Kewangan untuk Membuat Keputusan', 'Tempoh Kutipan Penghutang', 'Tempoh = (Penghutang ÷ Jualan Kredit) × 365', 'Berapa hari purata untuk mengutip hutang.', 'nisbah_kecekapan'],
        ['Analisis dan Tafsiran Penyata Kewangan untuk Membuat Keputusan', 'Tempoh Bayaran Pemiutang', 'Tempoh = (Pemiutang ÷ Belian Kredit) × 365', 'Berapa hari purata diambil untuk membayar pemiutang.', 'nisbah_kecekapan'],
        ['Analisis dan Tafsiran Penyata Kewangan untuk Membuat Keputusan', 'Nisbah Semasa', 'Nisbah Semasa = Aset Semasa ÷ Liabiliti Semasa', 'Mengukur kemampuan jangka pendek menampung liabiliti.', 'nisbah_kecairan'],
        ['Analisis dan Tafsiran Penyata Kewangan untuk Membuat Keputusan', 'Nisbah Cepat (Acid Test)', 'Nisbah Cepat = (Aset Semasa − Stok) ÷ Liabiliti Semasa', 'Kecairan tanpa mengambil kira stok yang sukar dijual cepat.', 'nisbah_kecairan'],
        ['Rekod Tak Lengkap', 'Untung Bersih dengan Kaedah Perbandingan Modal', 'Untung Bersih = Modal Akhir + Ambilan − Modal Tambahan − Modal Awal', 'Pengiraan untung tanpa catatan dwicatatan lengkap.', 'rekod_tak_lengkap'],
        ['Rekod Tak Lengkap', 'Modal Awal', 'Modal Awal = Aset Awal − Liabiliti Awal', 'Kira modal pada permulaan tempoh perakaunan.', 'rekod_tak_lengkap'],
        ['Perakaunan untuk Perkongsian', 'Faedah atas Modal Pekongsi', 'Faedah Modal = Modal × Kadar Faedah', 'Diperuntukkan dalam akaun pengasingan untung rugi.', 'perkongsian'],
        ['Perakaunan untuk Perkongsian', 'Faedah atas Ambilan', 'Faedah Ambilan = Ambilan × Kadar × (Bulan ÷ 12)', 'Dicaj atas ambilan pekongsi sepanjang tahun.', 'perkongsian'],
        ['Perakaunan untuk Syarikat Berhad Menurut Syer', 'Dividen Syer Keutamaan', 'Dividen Keutamaan = Nilai Tara × Bilangan Syer × Kadar Dividen', 'Dividen tetap untuk pemegang syer keutamaan.', 'syarikat'],
        ['Perakaunan untuk Syarikat Berhad Menurut Syer', 'Dividen Syer Biasa', 'Dividen Biasa = Bilangan Syer × Dividen Setiap Syer', 'Diisytihar oleh lembaga pengarah selepas dividen keutamaan.', 'syarikat'],
        ['Perakaunan Kos', 'Titik Pulang Modal (Unit)', 'TPM (Unit) = Kos Tetap ÷ (Harga Jual Seunit − Kos Berubah Seunit)', 'Jumlah unit yang perlu dijual untuk menampung kos tetap.', 'kos'],
        ['Perakaunan Kos', 'Titik Pulang Modal (RM)', 'TPM (RM) = Kos Tetap ÷ Nisbah Margin Caruman', 'Nilai jualan dalam RM untuk mencapai pulang modal.', 'kos'],
        ['Perakaunan Kos', 'Margin Caruman Seunit', 'Margin Caruman = Harga Jual Seunit − Kos Berubah Seunit', 'Sumbangan setiap unit kepada kos tetap dan untung.', 'kos'],
        ['Perakaunan Kos', 'Nisbah Margin Caruman', 'Nisbah MC = (Margin Caruman ÷ Harga Jual) × 100%', 'Peratus jualan yang menyumbang ke arah kos tetap.', 'kos'],
    ];
}

function perakaunan_kssm_run(): array
{
    $subject = db_one("SELECT id FROM subjects WHERE slug = 'perakaunan' LIMIT 1");
    if (!$subject) {
        return ['status' => 'no_subject', 'message' => 'Prinsip Perakaunan subject not found. Run install / seed_subjects first.'];
    }
    $sid = (int) $subject['id'];

    $formulasTable = (bool) db_one("SELECT 1 AS x FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'perakaunan_formulas'");

    $catalog = perakaunan_kssm_catalog();
    $topicsCreated = $topicsKept = $topicsAdopted = 0;
    $subCreated = $subKept = 0;
    $skillsCreated = $skillsKept = 0;
    $formulasCreated = $formulasKept = 0;

    $topicIds = [];

    foreach ($catalog as $i => $t) {
        $name = $t['name'];
        $form = (int) $t['form'];
        $sortOrder = ($form === 4 ? 0 : 100) + $i;

        $existing = db_one(
            'SELECT id FROM topics WHERE subject_id = ? AND name = ? AND form_level <=> ? LIMIT 1',
            [$sid, $name, $form]
        );
        if ($existing) {
            $tid = (int) $existing['id'];
            $topicsKept++;
        } else {
            $legacy = db_one(
                'SELECT id FROM topics WHERE subject_id = ? AND name = ? AND form_level IS NULL LIMIT 1',
                [$sid, $name]
            );
            if ($legacy) {
                $tid = (int) $legacy['id'];
                db_exec('UPDATE topics SET form_level = ? WHERE id = ?', [$form, $tid]);
                $topicsAdopted++;
            } else {
                $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($name . ' f' . $form));
                $tid  = db_exec(
                    'INSERT INTO topics (subject_id, form_level, name, slug, sort_order) VALUES (?, ?, ?, ?, ?)',
                    [$sid, $form, $name, $slug, $sortOrder]
                );
                $topicsCreated++;
            }
        }
        if (!isset($topicIds[$name])) {
            $topicIds[$name] = $tid;
        }

        foreach ($t['subtopics'] as $idx => $subName) {
            $sub = db_one('SELECT id FROM subtopics WHERE topic_id = ? AND name = ? LIMIT 1', [$tid, $subName]);
            if ($sub) {
                $subKept++;
            } else {
                db_exec('INSERT INTO subtopics (topic_id, name, sort_order) VALUES (?, ?, ?)', [$tid, $subName, $idx + 1]);
                $subCreated++;
            }
        }

        foreach ($t['skills'] as [$skillName, $diff]) {
            $existing = db_one('SELECT id FROM skills WHERE topic_id = ? AND name = ? LIMIT 1', [$tid, $skillName]);
            if ($existing) {
                $skillsKept++;
            } else {
                db_exec('INSERT INTO skills (topic_id, name, difficulty) VALUES (?, ?, ?)', [$tid, $skillName, $diff]);
                $skillsCreated++;
            }
        }
    }

    if ($formulasTable) {
        foreach (perakaunan_kssm_formulas() as $idx => [$topicName, $fname, $formula, $desc, $category]) {
            $tid = $topicIds[$topicName] ?? null;
            $existing = db_one('SELECT id FROM perakaunan_formulas WHERE formula_name = ? LIMIT 1', [$fname]);
            if ($existing) {
                $formulasKept++;
            } else {
                db_exec(
                    'INSERT INTO perakaunan_formulas (topic_id, topic_label, formula_name, formula, description, category, sort_order)
                     VALUES (?,?,?,?,?,?,?)',
                    [$tid, $topicName, $fname, $formula, $desc, $category, $idx + 1]
                );
                $formulasCreated++;
            }
        }
    }

    return [
        'status'             => 'ok',
        'topics_created'     => $topicsCreated,
        'topics_kept'        => $topicsKept,
        'topics_adopted'     => $topicsAdopted,
        'subtopics_created'  => $subCreated,
        'subtopics_kept'     => $subKept,
        'skills_created'     => $skillsCreated,
        'skills_kept'        => $skillsKept,
        'formulas_created'   => $formulasCreated,
        'formulas_kept'      => $formulasKept,
        'catalog_topics'     => count($catalog),
        'formulas_table'     => $formulasTable,
    ];
}

if (PHP_SAPI === 'cli') {
    $r = perakaunan_kssm_run();
    if (($r['status'] ?? '') !== 'ok') {
        fwrite(STDERR, ($r['message'] ?? 'unknown error') . "\n");
        exit(1);
    }
    echo "KSSM Prinsip Perakaunan seeded —\n";
    echo "  Bab:       created {$r['topics_created']}, adopted {$r['topics_adopted']}, kept {$r['topics_kept']} (catalog {$r['catalog_topics']})\n";
    echo "  Subtopik:  created {$r['subtopics_created']}, kept {$r['subtopics_kept']}\n";
    echo "  Kemahiran: created {$r['skills_created']}, kept {$r['skills_kept']}\n";
    if ($r['formulas_table']) {
        echo "  Formula:   created {$r['formulas_created']}, kept {$r['formulas_kept']}\n";
    } else {
        echo "  Formula:   table missing — run database migrations to enable.\n";
    }
}
