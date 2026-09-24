<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\ErrorHandler;

/** Best-effort notifications via PHP mail(). Leads are always stored before this runs. */
final class Mailer
{
    public static function send(string $to, string $subject, string $text, ?string $replyTo = null): bool
    {
        if ($to === '' || filter_var($to, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }
        // Strip CR/LF from anything that goes into a header.
        $subject = (string) preg_replace('/[\r\n]+/', ' ', $subject);
        $from = (string) preg_replace('/[\r\n]+/', '', (string) config('app.mail_from'));
        $headers = [
            'From: ' . config('business.name') . ' Website <' . $from . '>',
            'Content-Type: text/plain; charset=UTF-8',
            'X-Mailer: DRE',
        ];
        if ($replyTo !== null && filter_var($replyTo, FILTER_VALIDATE_EMAIL) !== false) {
            $headers[] = 'Reply-To: ' . $replyTo;
        }
        try {
            return mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $text, implode("\r\n", $headers));
        } catch (\Throwable $e) {
            ErrorHandler::report($e);
            return false;
        }
    }

    public static function notifyLead(int $id, array $lead): void
    {
        $to = setting('notify_email') ?: (string) config('app.mail_to');
        if ($to === '') {
            return;
        }
        $lines = [
            'New website enquiry #' . $id,
            '',
            'Name: ' . $lead['name'],
            'Phone: ' . $lead['phone'],
            'Service: ' . ($lead['service_label'] ?? '-'),
            'Location: ' . $lead['location'],
            'Vehicle: ' . ($lead['vehicle_type'] ?: '-'),
            'Preferred contact: ' . $lead['preferred_contact'],
            '',
            'Message:',
            (string) ($lead['message'] ?: '-'),
            '',
            'Open in admin: ' . url('/admin/leads/' . $id . '/'),
        ];
        self::send($to, 'New recovery enquiry: ' . $lead['name'] . ' (' . $lead['location'] . ')', implode("\n", $lines), $lead['email'] ?? null);
    }
}
