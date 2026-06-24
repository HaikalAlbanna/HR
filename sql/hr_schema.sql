-- HR database and tables + triggers
CREATE DATABASE IF NOT EXISTS `hr` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `hr`;

CREATE TABLE IF NOT EXISTS `karyawan` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(255) NOT NULL,
  `tgl_lahir` DATE NULL,
  `gaji` DECIMAL(15,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `tlog` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `karyawan_id` INT NULL,
  `action` VARCHAR(16) NOT NULL,
  `changed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `extra` TEXT NULL,
  FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Triggers to log insert, update, delete on karyawan
DELIMITER $$

CREATE TRIGGER `trg_karyawan_after_insert` AFTER INSERT ON `karyawan` FOR EACH ROW
BEGIN
  INSERT INTO `tlog` (`karyawan_id`,`action`,`extra`)
  VALUES (NEW.id, 'insert', CONCAT('nama=',COALESCE(NEW.nama,''),'|tgl_lahir=',COALESCE(NEW.tgl_lahir,''),'|gaji=',COALESCE(NEW.gaji,'')));
END$$

CREATE TRIGGER `trg_karyawan_after_update` AFTER UPDATE ON `karyawan` FOR EACH ROW
BEGIN
  INSERT INTO `tlog` (`karyawan_id`,`action`,`extra`)
  VALUES (NEW.id, 'update', CONCAT('old_nama=',COALESCE(OLD.nama,''),'|new_nama=',COALESCE(NEW.nama,''),'|old_tgl=',COALESCE(OLD.tgl_lahir,''),'|new_tgl=',COALESCE(NEW.tgl_lahir,''),'|old_gaji=',COALESCE(OLD.gaji,''),'|new_gaji=',COALESCE(NEW.gaji,'')));
END$$

CREATE TRIGGER `trg_karyawan_after_delete` AFTER DELETE ON `karyawan` FOR EACH ROW
BEGIN
  INSERT INTO `tlog` (`karyawan_id`,`action`,`extra`)
  VALUES (OLD.id, 'delete', CONCAT('nama=',COALESCE(OLD.nama,''),'|tgl_lahir=',COALESCE(OLD.tgl_lahir,''),'|gaji=',COALESCE(OLD.gaji,'')));
END$$

DELIMITER ;

-- End of schema
