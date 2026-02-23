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
            self::Assigned => 'Belum ada PIC',
            self::InProgress => 'Dalam Analisis',
            self::PendingKadivApproval => 'Menunggu ACC Kadiv',
            self::PendingKakanwilApproval => 'Menunggu ACC Kakanwil',
            self::RevisionByPic => 'Revisi oleh PIC',
            self::Completed => 'Selesai Analisis',
        };
    }
}
