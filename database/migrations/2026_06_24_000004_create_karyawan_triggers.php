<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_karyawan_after_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_karyawan_after_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_karyawan_after_delete');

        DB::unprepared("
            CREATE TRIGGER trg_karyawan_after_insert
            AFTER INSERT ON karyawan
            FOR EACH ROW
            BEGIN
                INSERT INTO tlog (tanggal, jam, keterangan)
                VALUES (
                    CURDATE(),
                    CURTIME(),
                    CONCAT('INSERT karyawan id=', NEW.id, ', nama=', NEW.nama, ', tgl_lahir=', COALESCE(NEW.tgl_lahir, ''), ', gaji=', NEW.gaji)
                );
            END
        ");

        DB::unprepared("
            CREATE TRIGGER trg_karyawan_after_update
            AFTER UPDATE ON karyawan
            FOR EACH ROW
            BEGIN
                INSERT INTO tlog (tanggal, jam, keterangan)
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
            END
        ");

        DB::unprepared("
            CREATE TRIGGER trg_karyawan_after_delete
            AFTER DELETE ON karyawan
            FOR EACH ROW
            BEGIN
                INSERT INTO tlog (tanggal, jam, keterangan)
                VALUES (
                    CURDATE(),
                    CURTIME(),
                    CONCAT('DELETE karyawan id=', OLD.id, ', nama=', OLD.nama, ', tgl_lahir=', COALESCE(OLD.tgl_lahir, ''), ', gaji=', OLD.gaji)
                );
            END
        ");
    }

    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_karyawan_after_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_karyawan_after_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_karyawan_after_delete');
    }
};
