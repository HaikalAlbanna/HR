-- Migration SQL for triggers (MySQL)
-- Run manually after migrations: source this file in the DB

DELIMITER $$

CREATE TRIGGER `trg_karyawan_after_insert` AFTER INSERT ON `karyawan` FOR EACH ROW
BEGIN
  INSERT INTO `tlog` (`tanggal`, `jam`, `keterangan`)
  VALUES (CURDATE(), CURTIME(), CONCAT('INSERT karyawan id=', NEW.id, ', nama=', NEW.nama, ', tgl_lahir=', COALESCE(NEW.tgl_lahir, ''), ', gaji=', NEW.gaji));
END$$

CREATE TRIGGER `trg_karyawan_after_update` AFTER UPDATE ON `karyawan` FOR EACH ROW
BEGIN
  INSERT INTO `tlog` (`tanggal`, `jam`, `keterangan`)
  VALUES (
    CURDATE(),
    CURTIME(),
    CONCAT(
      'UPDATE karyawan id=', NEW.id,
      ', nama: ', OLD.nama, ' -> ', NEW.nama,
      ', tgl_lahir: ', COALESCE(OLD.tgl_lahir, ''), ' -> ', COALESCE(NEW.tgl_lahir, ''),
      ', gaji: ', OLD.gaji, ' -> ', NEW.gaji
    )
  );
END$$

CREATE TRIGGER `trg_karyawan_after_delete` AFTER DELETE ON `karyawan` FOR EACH ROW
BEGIN
  INSERT INTO `tlog` (`tanggal`, `jam`, `keterangan`)
  VALUES (CURDATE(), CURTIME(), CONCAT('DELETE karyawan id=', OLD.id, ', nama=', OLD.nama, ', tgl_lahir=', COALESCE(OLD.tgl_lahir, ''), ', gaji=', OLD.gaji));
END$$

DELIMITER ;
