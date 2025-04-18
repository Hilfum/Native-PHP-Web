USE berkas;

-- Membuat tabel Roles
CREATE TABLE Roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role VARCHAR(255) NOT NULL
);

-- Membuat tabel Users
CREATE TABLE Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    id_role INT,
    FOREIGN KEY (id_role) REFERENCES Roles(id)
);

-- Membuat tabel Dokumen
CREATE TABLE Dokumen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_dokumen VARCHAR(255) NOT NULL,
    penerima INT,
    nama VARCHAR(255) NOT NULL,
    nop VARCHAR(255) NOT NULL,
    kelurahan_objek_pajak VARCHAR(255) NOT NULL,
    kecamatan_objek_pajak VARCHAR(255) NOT NULL,
    alamat_wajib_pajak TEXT NOT NULL,
    alamat_objek_pajak TEXT NOT NULL,
    tipe_berkas ENUM('BPHTB', 'Mutasi', 'OP Baru') NOT NULL,
    status ENUM('pending', 'sampai','selesai') NOT NULL,
    FOREIGN KEY (penerima) REFERENCES Users(id)
);

CREATE TABLE HistoriStatus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dokumen_id INT NOT NULL,
    status_lama ENUM('pending', 'sampai', 'selesai') NOT NULL,
    status_baru ENUM('pending', 'sampai', 'selesai') NOT NULL,
    perubahan_oleh INT NOT NULL, -- User yang mengubah status
    waktu_perubahan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (dokumen_id) REFERENCES Dokumen(id),
    FOREIGN KEY (perubahan_oleh) REFERENCES Users(id)
);

ALTER TABLE dokumen 
ADD COLUMN current_handler VARCHAR(50),
ADD COLUMN completion_date TIMESTAMP NULL;