<?php

namespace App\Helpers;

use App\Models\GymMember;
use Illuminate\Support\Collection;

class WhatsAppHelper
{
    public static function normalizePhone(?string $phone): ?string
    {
        if (! $phone) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);

        if (! $digits) {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62' . $digits;
        }

        return str_starts_with($digits, '62') ? $digits : null;
    }

    public static function buildUrl(?string $phone, string $message): ?string
    {
        $normalizedPhone = self::normalizePhone($phone);

        if (! $normalizedPhone) {
            return null;
        }

        return 'https://wa.me/' . $normalizedPhone . '?text=' . rawurlencode($message);
    }

    public static function announcementMessage(string $title, string $body): string
    {
        return trim("ARENA GYM\n\nPengumuman Admin\n{$title}\n\n{$body}");
    }

    public static function reminderMessage(GymMember $member): string
    {
        $expiryText = $member->expires_at?->format('d M Y') ?? '-';

        return trim("ARENA GYM\n\nHalo {$member->full_name}, masa aktif membership Anda akan segera berakhir pada {$expiryText}. Silakan datang ke kasir untuk melakukan perpanjangan membership.");
    }

    public static function buildDispatches(Collection $members, string $message): array
    {
        $dispatches = $members->map(function (GymMember $member) use ($message) {
            $url = self::buildUrl($member->phone, $message);

            return [
                'member_id' => $member->id,
                'name' => $member->full_name,
                'phone' => $member->phone,
                'normalized_phone' => self::normalizePhone($member->phone),
                'url' => $url,
                'can_send' => filled($url),
            ];
        })->values();

        return [
            'recipients' => $dispatches->all(),
            'sendable_count' => $dispatches->where('can_send', true)->count(),
            'missing_phone_count' => $dispatches->where('can_send', false)->count(),
        ];
    }
}
