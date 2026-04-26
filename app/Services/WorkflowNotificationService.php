<?php

namespace App\Services;

use App\Enums\AssignmentStatus;
use App\Enums\SubmissionStatus;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use App\Notifications\WorkflowMailNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Illuminate\Support\Str;

class WorkflowNotificationService
{
    public function notifyNewSubmission(Submission $submission, User $actor): void
    {
        $submission->loadMissing('submitter');
        $nomorSurat = $this->submissionNumber($submission);

        $recipients = $this->usersByRoles(['operator_kanwil'], [$actor->id], [$actor->email]);

        $this->sendToUsers(
            $recipients,
            new WorkflowMailNotification(
                "Permohonan Baru: {$nomorSurat}",
                'Permohonan baru telah diajukan.',
                "{$actor->name} mengajukan permohonan {$nomorSurat}. Silakan lakukan peninjauan.",
                route('submissions.show', $submission->id)
            )
        );
    }

    public function notifySubmissionStatusUpdated(Submission $submission, User $actor, string $status, ?string $note = null): void
    {
        $submission->loadMissing('submitter');
        $recipient = $submission->submitter;
        $nomorSurat = $this->submissionNumber($submission);
        $statusLabel = SubmissionStatus::tryFrom($status)?->label() ?? ucfirst(str_replace('_', ' ', $status));

        $body = "Status permohonan {$nomorSurat} berubah menjadi: {$statusLabel}.";
        if (filled($note)) {
            $body .= ' Catatan: '.trim((string) $note).'.';
        }
        $body .= " Diperbarui oleh {$actor->name}.";

        $this->sendToUser(
            $recipient,
            new WorkflowMailNotification(
                "Status Permohonan Diperbarui: {$nomorSurat}",
                'Ada pembaruan status permohonan Anda.',
                $body,
                route('submissions.show', $submission->id)
            )
        );
    }

    public function notifySubmissionDispositioned(Submission $submission, User $actor, User $toUser, ?string $note = null): void
    {
        $nomorSurat = $this->submissionNumber($submission);
        $body = "Permohonan {$nomorSurat} didisposisikan kepada Anda oleh {$actor->name}.";
        if (filled($note)) {
            $body .= ' Catatan disposisi: '.trim((string) $note).'.';
        }

        $this->sendToUser(
            $toUser,
            new WorkflowMailNotification(
                "Disposisi Baru: {$nomorSurat}",
                'Anda menerima disposisi baru di PASIH.',
                $body,
                route('submissions.show', $submission->id)
            )
        );
    }

    public function notifyAssignmentCreated(Assignment $assignment, User $actor): void
    {
        $assignment->loadMissing('submission');
        $nomorSurat = $this->assignmentSubmissionNumber($assignment);
        $recipients = $this->usersByRoles(['ketua_tim_analisis'], [$actor->id], [$actor->email]);

        $this->sendToUsers(
            $recipients,
            new WorkflowMailNotification(
                "Penugasan Baru: {$nomorSurat}",
                'Ada penugasan baru yang perlu ditindaklanjuti.',
                "{$actor->name} membuat penugasan baru untuk permohonan {$nomorSurat}.",
                route('assignments.show', $assignment->id)
            )
        );
    }

    public function notifyAssignmentPicAssigned(Assignment $assignment, User $actor, User $analyst, ?string $deadlineAt = null): void
    {
        $assignment->loadMissing('submission');
        $nomorSurat = $this->assignmentSubmissionNumber($assignment);
        $body = "{$actor->name} menetapkan Anda sebagai Penanggung Jawab untuk analisis {$nomorSurat}.";

        if (filled($deadlineAt)) {
            $body .= " Tenggat: {$deadlineAt}.";
        }

        $this->sendToUser(
            $analyst,
            new WorkflowMailNotification(
                "Penanggung Jawab Ditetapkan: {$nomorSurat}",
                'Anda mendapatkan penugasan analisis baru.',
                $body,
                route('assignments.show', $assignment->id)
            )
        );
    }

    public function notifySubmitterReplyLetterAvailable(Assignment $assignment, User $actor): void
    {
        $assignment->loadMissing('submission.submitter');
        $submission = $assignment->submission;

        if (! $submission) {
            return;
        }

        $submitter = $submission->submitter;
        $nomorSurat = $this->submissionNumber($submission);

        $this->sendToUser(
            $submitter,
            new WorkflowMailNotification(
                "Surat Balasan Tersedia: {$nomorSurat}",
                'Surat balasan untuk permohonan Anda sudah tersedia.',
                "{$actor->name} telah mengunggah surat balasan untuk permohonan {$nomorSurat}. Silakan cek detail permohonan Anda.",
                route('submissions.show', $submission->id),
                'Lihat Detail'
            )
        );
    }

    public function notifyAssignmentSubmittedForKadivReview(Assignment $assignment, User $actor): void
    {
        $assignment->loadMissing('submission');
        $nomorSurat = $this->assignmentSubmissionNumber($assignment);
        $recipients = $this->usersByRoles(['kepala_divisi_p3h'], [$actor->id], [$actor->email]);

        $this->sendToUsers(
            $recipients,
            new WorkflowMailNotification(
                "Menunggu Persetujuan Kadiv: {$nomorSurat}",
                'Hasil analisis siap ditinjau.',
                "{$actor->name} mengunggah hasil analisis {$nomorSurat} dan menunggu persetujuan Kadiv.",
                route('assignments.show', $assignment->id)
            )
        );
    }

    public function notifyAssignmentForwardedToKakanwil(Assignment $assignment, User $actor): void
    {
        $assignment->loadMissing('submission');
        $nomorSurat = $this->assignmentSubmissionNumber($assignment);
        $recipients = $this->usersByRoles(['kakanwil'], [$actor->id], [$actor->email]);

        $this->sendToUsers(
            $recipients,
            new WorkflowMailNotification(
                "Menunggu Persetujuan Kakanwil: {$nomorSurat}",
                'Hasil analisis siap ditinjau oleh Kakanwil.',
                "{$actor->name} menyetujui tahap Kadiv untuk {$nomorSurat}.",
                route('assignments.show', $assignment->id)
            )
        );
    }

    public function notifyAssignmentReturnedForRevision(Assignment $assignment, User $actor, ?string $revisionNote = null): void
    {
        $assignment->loadMissing('latestPicUpdate.analyst', 'submission');
        $analyst = $assignment->analyst;
        $nomorSurat = $this->assignmentSubmissionNumber($assignment);
        $body = "Analisis {$nomorSurat} dikembalikan untuk revisi oleh {$actor->name}.";

        if (filled($revisionNote)) {
            $body .= ' Catatan revisi: '.trim((string) $revisionNote).'.';
        }

        $this->sendToUser(
            $analyst,
            new WorkflowMailNotification(
                "Permintaan Revisi Analisis: {$nomorSurat}",
                'Ada revisi yang perlu Anda tindak lanjuti.',
                $body,
                route('assignments.show', $assignment->id)
            )
        );
    }

    public function notifyAssignmentCompleted(Assignment $assignment, User $actor): void
    {
        $assignment->loadMissing('submission.submitter');
        $submission = $assignment->submission;

        if (! $submission) {
            return;
        }

        $recipient = $submission->submitter;
        $nomorSurat = $this->submissionNumber($submission);
        $statusLabel = AssignmentStatus::Completed->label();

        $this->sendToUser(
            $recipient,
            new WorkflowMailNotification(
                "Analisis Selesai: {$nomorSurat}",
                'Permohonan Anda telah selesai dianalisis.',
                "Status analisis {$nomorSurat} saat ini: {$statusLabel}. Diperbarui oleh {$actor->name}.",
                route('submissions.show', $submission->id)
            )
        );
    }

    /**
     * @param  array<int, string>  $roles
     * @param  array<int, int>  $excludeUserIds
     * @param  array<int, string|null>  $excludeEmails
     * @return Collection<int, User>
     */
    private function usersByRoles(array $roles, array $excludeUserIds = [], array $excludeEmails = []): Collection
    {
        $normalizedExcludeEmails = collect($excludeEmails)
            ->filter(fn ($email) => filled($email))
            ->map(fn ($email) => Str::lower(trim((string) $email)))
            ->unique()
            ->values()
            ->all();

        $query = User::query()
            ->whereIn('role', $roles)
            ->whereNotNull('email')
            ->where('email', '!=', '');

        if ($excludeUserIds !== []) {
            $query->whereNotIn('id', $excludeUserIds);
        }

        $users = $query->get();

        if ($normalizedExcludeEmails === []) {
            return $users;
        }

        return $users
            ->reject(function (User $user) use ($normalizedExcludeEmails): bool {
                $userEmail = Str::lower(trim((string) $user->email));

                return in_array($userEmail, $normalizedExcludeEmails, true);
            })
            ->values();
    }

    private function sendToUser(?User $recipient, WorkflowMailNotification $notification): void
    {
        if (! $recipient || blank($recipient->email)) {
            return;
        }

        $recipient->notify($notification);
    }

    /**
     * @param  Collection<int, User>  $recipients
     */
    private function sendToUsers(Collection $recipients, WorkflowMailNotification $notification): void
    {
        if ($recipients->isEmpty()) {
            return;
        }

        NotificationFacade::send(
            $recipients
                ->filter(fn (User $user) => filled($user->email))
                ->unique('id')
                ->values(),
            $notification
        );
    }

    private function submissionNumber(Submission $submission): string
    {
        return trim((string) ($submission->nomor_surat ?: '#'.$submission->id));
    }

    private function assignmentSubmissionNumber(Assignment $assignment): string
    {
        $nomorSurat = $assignment->submission?->nomor_surat;

        return trim((string) ($nomorSurat ?: '#'.$assignment->submission_id));
    }
}
