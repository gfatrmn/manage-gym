<!DOCTYPE html>
<html class="dark" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>IRON ELITE | Aktivasi Member</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;600;700&family=JetBrains+Mono:wght@400;500;700&family=Hanken+Grotesk:wght@400;600&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
</head>
<body class="bg-[#131313] text-[#e2e2e2] min-h-screen">
    <main class="w-full max-w-6xl mx-auto px-4 py-8 lg:py-12 grid gap-6 lg:grid-cols-[minmax(320px,420px)_1fr] items-start">
        <div class="w-full p-8 bg-[#1f1f1f] border border-[#353535] rounded-xl">
            <div class="flex justify-center mb-4">
                <img src="{{ asset('images/arena-fitness-logo.jpg') }}" alt="Arena Fitness" style="width: 132px; height: auto; max-width: 100%;">
            </div>
            <h2 class="text-sm uppercase text-[#ebbbb4] mb-8 text-center" style="font-family: 'JetBrains Mono', monospace;">Aktivasi Akun Member</h2>

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-900/50 border border-red-500 text-red-200 rounded-lg">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('member.activate.store') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs uppercase text-[#ebbbb4] mb-2" style="font-family: 'JetBrains Mono', monospace;">Email Member</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-[#353535] border border-[#4a4a4a] rounded-lg p-3 text-white focus:border-red-500 focus:outline-none" placeholder="email@example.com">
                </div>
                <div class="mb-4">
                    <label class="block text-xs uppercase text-[#ebbbb4] mb-2" style="font-family: 'JetBrains Mono', monospace;">Password Baru</label>
                    <input type="password" name="password" required minlength="6" class="w-full bg-[#353535] border border-[#4a4a4a] rounded-lg p-3 text-white focus:border-red-500 focus:outline-none" placeholder="Minimal 6 karakter">
                </div>
                <div class="mb-8">
                    <label class="block text-xs uppercase text-[#ebbbb4] mb-2" style="font-family: 'JetBrains Mono', monospace;">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required minlength="6" class="w-full bg-[#353535] border border-[#4a4a4a] rounded-lg p-3 text-white focus:border-red-500 focus:outline-none" placeholder="Ulangi password">
                </div>
                <button type="submit" class="w-full bg-red-600 text-black font-semibold uppercase py-3 rounded-lg hover:bg-white transition-colors duration-300" style="font-family: 'JetBrains Mono', monospace;">
                    Simpan Password
                </button>
            </form>

            <div class="mt-5 text-center">
                <a href="{{ route('member.login') }}" class="text-xs uppercase text-[#ebbbb4] hover:text-[#ffb4a8]" style="font-family: 'JetBrains Mono', monospace;">
                    Kembali ke Login
                </a>
            </div>
        </div>

        <div class="w-full p-6 bg-[#1f1f1f] border border-[#353535] rounded-xl">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between mb-5">
                <div>
                    <p class="text-xs uppercase text-[#ebbbb4]" style="font-family: 'JetBrains Mono', monospace;">List Member</p>
                    <h1 class="text-2xl font-bold text-white" style="font-family: 'Oswald', sans-serif;">Member Terdaftar</h1>
                </div>
                <form method="GET" action="{{ route('member.activate.show') }}" class="flex gap-2">
                    <input type="text" name="q" value="{{ $memberSearch ?? '' }}" class="w-full sm:w-64 bg-[#353535] border border-[#4a4a4a] rounded-lg p-3 text-sm text-white focus:border-red-500 focus:outline-none" placeholder="Cari member">
                    <button type="submit" class="bg-red-600 text-black font-semibold uppercase px-4 rounded-lg hover:bg-white transition-colors duration-300" style="font-family: 'JetBrains Mono', monospace;">Cari</button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-[#353535] text-left text-xs uppercase text-[#ebbbb4]" style="font-family: 'JetBrains Mono', monospace;">
                            <th class="py-3 pr-4">Nama</th>
                            <th class="py-3 pr-4">Email</th>
                            <th class="py-3 pr-4">HP</th>
                            <th class="py-3 pr-4">Expired</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#2f2f2f]">
                        @forelse (($activationMembers ?? collect()) as $member)
                            <tr>
                                <td class="py-3 pr-4 font-semibold text-white">{{ $member->full_name }}</td>
                                <td class="py-3 pr-4 text-[#c9c9c9]">{{ $member->email ?: ($member->user?->email ?: '-') }}</td>
                                <td class="py-3 pr-4 text-[#c9c9c9]">{{ $member->phone ?: '-' }}</td>
                                <td class="py-3 pr-4 text-[#c9c9c9]">{{ $member->expires_at ? $member->expires_at->format('d M Y') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-[#a9a9a9]">Tidak ada member ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (isset($activationMembers) && method_exists($activationMembers, 'links'))
                <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between text-xs text-[#a9a9a9]" style="font-family: 'JetBrains Mono', monospace;">
                    <span>
                        Menampilkan {{ $activationMembers->firstItem() ?: 0 }} - {{ $activationMembers->lastItem() ?: 0 }}
                        dari {{ $activationMembers->total() }} member
                    </span>
                    <div class="[&_a]:text-[#ebbbb4] [&_span]:text-[#777]">
                        {{ $activationMembers->links() }}
                    </div>
                </div>
            @endif
        </div>
    </main>
</body>
</html>
