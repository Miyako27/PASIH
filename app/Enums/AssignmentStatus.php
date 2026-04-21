<?php

namespace App\Enums;

enum AssignmentStatus: string
{
    case Assigned = 'assigned';
    case InProgress = 'in_progress';
    case PendingKadivApproval = 'pending_kadiv_approval';
    case PendingKakanwilApproval = 'pending_kakanwil_approval';
    case RevisionByPic = 'revision_by_pic';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Assigned => 'Belum ada Penanggung Jawab',
            self::InProgress => 'Dalam Analisis',
            self::PendingKadivApproval => 'Menunggu Persetujuan Kadiv',
            self::PendingKakanwilApproval => 'Menunggu Persetujuan Kakanwil',
            self::RevisionByPic => 'Revisi oleh Penanggung Jawab',
            self::Completed => 'Selesai Analisis',
        };
    }
}
