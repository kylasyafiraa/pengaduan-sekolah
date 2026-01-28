USE pengaduan_sekolah;

ALTER TABLE pengaduan
  ADD COLUMN gambar VARCHAR(255) NULL AFTER isi;

ALTER TABLE balasan
  ADD COLUMN gambar VARCHAR(255) NULL AFTER isi_balasan;
