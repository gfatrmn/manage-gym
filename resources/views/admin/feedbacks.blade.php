@extends('admin.layout')

@section('content')
<div class="topbar-card p-4 mb-4">
    <div class="section-label">Admin</div>
    <h1 class="display-6 fw-bold mt-2 mb-0">Kritik &amp; Saran Member</h1>
    <div class="small text-white-50 mt-2"><span id="feedbackUnreadCount">{{ $unreadCount }}</span> pesan belum dibaca</div>
</div>

<div class="panel-card p-4">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Waktu</th><th>Nama</th><th>Subjek</th><th>Ringkasan Pesan</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @forelse ($feedbacks as $item)
                <tr>
                    <td>{{ $item->created_at?->format('d M Y H:i') }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->subject }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($item->message, 70) }}</td>
                    <td><span class="badge text-bg-{{ $item->read_at ? 'success' : 'warning' }}" data-feedback-status="{{ $item->id }}">{{ $item->read_at ? 'Dibaca' : 'Baru' }}</span></td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-2 flex-wrap">
                        <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#feedbackModal{{ $item->id }}">
                            Buka Pesan
                        </button>
                        <form method="POST" action="{{ route('admin.feedbacks.destroy', $item) }}" onsubmit="return confirm('Hapus kritik dan saran dari ' + @js($item->name) + '?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger rounded-pill px-3">Hapus</button>
                        </form>
                        </div>
                    </td>
                </tr>

                @empty
                <tr><td colspan="6" class="text-center text-secondary py-4">Belum ada pesan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $feedbacks->links() }}</div>
</div>

@foreach ($feedbacks as $item)
<div class="modal fade" id="feedbackModal{{ $item->id }}" tabindex="-1" aria-labelledby="feedbackModalLabel{{ $item->id }}" aria-hidden="true" data-feedback-modal data-feedback-id="{{ $item->id }}" data-read-url="{{ route('admin.feedbacks.read', $item) }}" data-is-unread="{{ $item->read_at ? '0' : '1' }}">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4" style="background:#141414;color:#fff;">
            <div class="modal-header border-0 pb-0">
                <div>
                    <div class="small text-white-50">{{ $item->created_at?->format('d M Y H:i') }}</div>
                    <h5 class="modal-title fw-bold mt-1" id="feedbackModalLabel{{ $item->id }}">{{ $item->subject }}</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body pt-3">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge text-bg-dark border border-secondary-subtle">{{ $item->name }}</span>
                    <span class="badge text-bg-{{ $item->read_at ? 'success' : 'warning' }}" data-feedback-status="{{ $item->id }}">{{ $item->read_at ? 'Dibaca' : 'Baru' }}</span>
                </div>
                <div class="p-3 rounded-4" style="background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.1); white-space:pre-wrap; line-height:1.7;">{{ $item->message }}</div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-danger rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrfToken = @js(csrf_token());
        const unreadCount = document.getElementById('feedbackUnreadCount');

        function markStatusAsRead(feedbackId) {
            document.querySelectorAll('[data-feedback-status="' + feedbackId + '"]').forEach(function (badge) {
                badge.classList.remove('text-bg-warning');
                badge.classList.add('text-bg-success');
                badge.textContent = 'Dibaca';
            });
        }

        function reduceUnreadCount() {
            if (! unreadCount) return;

            const nextCount = Math.max(0, (parseInt(unreadCount.textContent, 10) || 0) - 1);
            unreadCount.textContent = nextCount;
        }

        document.querySelectorAll('[data-feedback-modal]').forEach(function (modal) {
            modal.addEventListener('shown.bs.modal', function () {
                if (modal.dataset.isUnread !== '1') return;

                const feedbackId = modal.dataset.feedbackId;
                modal.dataset.isUnread = '0';

                fetch(modal.dataset.readUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(function (response) {
                    if (! response.ok) {
                        throw new Error('Gagal menandai pesan.');
                    }

                    markStatusAsRead(feedbackId);
                    reduceUnreadCount();
                }).catch(function () {
                    modal.dataset.isUnread = '1';
                });
            });
        });
    });
</script>
@endsection
