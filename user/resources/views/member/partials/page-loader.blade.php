<style>
    .page-loader {
        position: fixed;
        inset: 0;
        z-index: 3000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.25rem;
        background: radial-gradient(circle at 50% 42%, rgba(255, 85, 64, 0.2), transparent 24rem), rgba(5, 5, 6, 0.78);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        opacity: 0;
        pointer-events: none;
        transition: opacity .22s ease;
    }
    .page-loader.is-visible { opacity: 1; pointer-events: auto; }
    .page-loader-card {
        position: relative;
        overflow: hidden;
        width: min(420px, 100%);
        padding: 1.5rem;
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 1.25rem;
        background: radial-gradient(circle at 16% 12%, rgba(255, 85, 64, .24), transparent 9rem), linear-gradient(180deg, rgba(255,255,255,.08), rgba(255,255,255,.035)), rgba(18,18,20,.9);
        box-shadow: 0 28px 70px rgba(0,0,0,.52);
    }
    .page-loader-brand { display: flex; align-items: center; gap: 1rem; color: #fff; font-weight: 800; position: relative; z-index: 1; }
    .page-loader-mark { position: relative; width: 3.8rem; height: 3.8rem; display: inline-flex; align-items: center; justify-content: center; border-radius: 1.15rem; background: linear-gradient(135deg, #ff5540, #8f130b); box-shadow: 0 18px 34px rgba(255,85,64,.28); isolation: isolate; }
    .page-loader-mark::after { content: ''; position: absolute; inset: -.45rem; border: 1px solid rgba(255,85,64,.32); border-radius: 1.45rem; animation: page-loader-pulse 1.45s ease-in-out infinite; z-index: -1; }
    .page-loader-logo { display: block; width: 3.1rem; height: 3.1rem; object-fit: contain; border-radius: .9rem; background: rgba(255,255,255,.04); }
    .page-loader-title { font-size: 1.08rem; line-height: 1.15; }
    .page-loader-subtitle { margin-top: .2rem; color: rgba(255,255,255,.62); font-size: .86rem; font-weight: 600; }
    .page-loader-wordmark { display: inline-flex; align-items: center; gap: .45rem; margin-bottom: .15rem; color: rgba(255,255,255,.58); font-size: .7rem; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; }
    .page-loader-wordmark::before { content: ''; width: 1.4rem; height: 2px; background: #ff5540; }
    .page-loader-bar { position: relative; overflow: hidden; height: .5rem; margin-top: 1.15rem; border-radius: 999px; background: rgba(255,255,255,.1); z-index: 1; }
    .page-loader-bar::after { content: ''; position: absolute; inset: 0 auto 0 0; width: 42%; background: linear-gradient(90deg, #ffb4a8, #ff5540, #8f130b); animation: page-loader-slide 1s ease-in-out infinite; }
    @keyframes page-loader-slide { 0% { transform: translateX(-110%); } 55% { transform: translateX(95%); } 100% { transform: translateX(250%); } }
    @keyframes page-loader-pulse { 0%, 100% { transform: scale(.96); opacity: .55; } 50% { transform: scale(1.08); opacity: 1; } }
</style>

<div class="page-loader is-visible" id="pageLoader" aria-live="polite" aria-label="Memuat halaman">
    <div class="page-loader-card">
        <div class="page-loader-brand">
            <span class="page-loader-mark"><img src="{{ asset('images/arena-fitness-logo.jpg') }}" alt="Arena Fitness" class="page-loader-logo"></span>
            <div>
                <div class="page-loader-wordmark">Loading</div>
                <div class="page-loader-title">Arena Fitness</div>
                <div class="page-loader-subtitle" id="pageLoaderText">Menyiapkan halaman member...</div>
            </div>
        </div>
        <div class="page-loader-bar" aria-hidden="true"></div>
    </div>
</div>

<script>
    (() => {
        const pageLoader = document.getElementById('pageLoader');
        const pageLoaderText = document.getElementById('pageLoaderText');
        const hidePageLoader = () => pageLoader?.classList.remove('is-visible');
        const showPageLoader = (message = 'Memuat halaman...') => {
            if (pageLoaderText) pageLoaderText.textContent = message;
            pageLoader?.classList.add('is-visible');
        };
        window.addEventListener('load', () => window.setTimeout(hidePageLoader, 380));
        window.addEventListener('pageshow', hidePageLoader);
        window.addEventListener('beforeunload', () => showPageLoader('Memuat halaman...'));
        window.addEventListener('pagehide', () => showPageLoader('Memuat halaman...'));
    })();
</script>
