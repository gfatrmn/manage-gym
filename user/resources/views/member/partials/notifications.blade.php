@php
    $memberAnnouncements = $memberAnnouncements ?? ($announcements ?? collect());
    $hasMembershipWarning = filled($membershipWarning ?? null);
    $channelUrl = $channelUrl ?? config('services.whatsapp.channel_url');
    $shouldShowChannelPrompt = ($showWhatsAppChannelPrompt ?? false) && filled($channelUrl);
    $notificationCount = $memberAnnouncements->count() + ($hasMembershipWarning ? 1 : 0) + ($shouldShowChannelPrompt ? 1 : 0);
    $notificationSignature = collect([
        $memberAnnouncements->pluck('id')->implode('-'),
        $hasMembershipWarning ? 'membership-' . ($membershipWarning['days_left'] ?? 'warning') : null,
        $shouldShowChannelPrompt ? 'whatsapp-channel' : null,
    ])->filter()->implode('|');
@endphp

<style>
    .member-notif-toast {
        position: fixed;
        top: 96px;
        right: 24px;
        width: min(380px, calc(100vw - 32px));
        border: 1px solid rgba(255,255,255,.14);
        background: linear-gradient(180deg, rgba(30,30,30,.97), rgba(16,16,16,.97));
        box-shadow: 0 20px 50px rgba(0,0,0,.45);
        z-index: 80;
        opacity: 0;
        transform: translateY(-10px) scale(.98);
        pointer-events: none;
        transition: all .28s ease;
    }
    .member-notif-toast.show {
        opacity: 1;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }
</style>

<div class="relative" data-member-notifications data-notification-count="{{ $notificationCount }}" data-notification-key="{{ $notificationSignature }}">
    <a id="memberNotifToggle" href="{{ route('messages') }}" class="relative inline-flex items-center justify-center w-10 h-10 border border-[#353535] text-[#ebbbb4] hover:border-[#ff5540] hover:text-[#ff5540] transition-colors" aria-label="Buka pemberitahuan member">
        <span class="material-symbols-outlined text-[20px]">notifications</span>
        @if($notificationCount > 0)
            <span data-member-notif-badge class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-[#ff5540] text-black text-[10px] leading-[18px] font-bold text-center">{{ min($notificationCount, 9) }}</span>
        @endif
    </a>
</div>

@if($notificationCount > 0)
    <div id="memberNotifToast" class="member-notif-toast p-3">
        <div class="flex items-start justify-between gap-3 mb-2">
            <p class="font-['JetBrains_Mono'] text-xs text-[#ebbbb4] uppercase">Notifikasi Member</p>
            <button id="memberNotifToastClose" type="button" class="text-[#ebbbb4] hover:text-white text-sm" aria-label="Tutup notifikasi">&times;</button>
        </div>
        @if($hasMembershipWarning)
            <a href="{{ route('membership') }}" class="block p-3 border border-[#ff5540] bg-[#ff5540]/15 hover:bg-[#ff5540]/20 transition-colors">
                <p class="text-sm text-white font-semibold">{{ $membershipWarning['title'] }}</p>
                <p class="text-xs text-[#ebbbb4] mt-1">{{ $membershipWarning['message'] }}</p>
            </a>
        @elseif($memberAnnouncements->count() > 0)
            @php $latestNotice = $memberAnnouncements->first(); @endphp
            <a href="{{ route('messages') }}#notice-{{ $latestNotice->id }}" class="block p-3 border border-[#353535] bg-[#1b1b1b] hover:border-[#ff5540] hover:bg-[#ff5540]/10 transition-colors">
                <p class="text-sm text-white font-semibold">{{ $latestNotice->title }}</p>
                <p class="text-xs text-[#ebbbb4] mt-1">{{ \Illuminate\Support\Str::limit(preg_replace('/^\[TARGET_MEMBER_ID:\d+\]\s*/', '', $latestNotice->body), 110) }}</p>
            </a>
        @elseif($shouldShowChannelPrompt)
            <div class="p-3 border border-[#ff5540]/40 bg-[#ff5540]/10">
                <p class="text-sm text-white font-semibold">Gabung Saluran WhatsApp</p>
                <p class="text-xs text-[#ebbbb4] mt-1">Dapatkan informasi umum Arena Fitness lewat saluran resmi.</p>
                <a href="{{ $channelUrl }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex items-center gap-2 font-['JetBrains_Mono'] text-[10px] uppercase tracking-[0.18em] text-[#ff5540]">
                    Buka Saluran
                    <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                </a>
            </div>
        @endif
    </div>
@endif

<script>
    (() => {
        const root = document.querySelector('[data-member-notifications]');
        if (!root || root.dataset.initialized === 'true') {
            return;
        }

        root.dataset.initialized = 'true';

        const toggle = root.querySelector('#memberNotifToggle');
        const badge = root.querySelector('[data-member-notif-badge]');
        const toast = document.getElementById('memberNotifToast');
        const close = document.getElementById('memberNotifToastClose');
        const notificationKey = root.dataset.notificationKey || 'empty';
        const notificationCount = Number(root.dataset.notificationCount || 0);
        const storageKey = `arena-member-notification-seen:${notificationKey}`;
        const isSeen = () => notificationCount <= 0 || localStorage.getItem(storageKey) === '1';

        const acknowledge = () => {
            if (notificationCount > 0) {
                localStorage.setItem(storageKey, '1');
            }
            badge?.remove();
            if (toast) {
                toast.classList.remove('show');
                toast.dataset.dismissed = 'true';
            }
        };

        if (isSeen()) {
            badge?.remove();
            if (toast) {
                toast.remove();
            }
        }

        if (toggle) {
            toggle.addEventListener('click', (event) => {
                if (!isSeen()) {
                    event.preventDefault();
                    acknowledge();
                    return;
                }
            });
        }

        if (toast) {
            setTimeout(() => {
                if (!isSeen() && toast.dataset.dismissed !== 'true') {
                    toast.classList.add('show');
                }
            }, 450);
            close?.addEventListener('click', acknowledge);
        }
    })();
</script>
