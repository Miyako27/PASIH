<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case OperatorPemda = 'operator_pemda';
    case OperatorKanwil = 'operator_kanwil';
    case KetuaTimAnalisis = 'ketua_tim_analisis';
    case Kakanwil = 'kakanwil';
    case KepalaDivisiP3H = 'kepala_divisi_p3h';
    case AnalisHukum = 'analis_hukum';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::OperatorPemda => 'Operator Biro/Bagian Hukum Pemda',
            self::OperatorKanwil => 'Operator Kanwil Kemenkum',
            self::KetuaTimAnalisis => 'Ketua Tim Analisis',
            self::Kakanwil => 'Kakanwil',
            self::KepalaDivisiP3H => 'Kepala Divisi P3H',
            self::AnalisHukum => 'Analis Hukum Kemenkum',
        };
    }
}
