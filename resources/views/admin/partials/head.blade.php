<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title> {{ $title ?? 'SAAR Assurance' }}</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#0d6efd">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>
<script src="{{ asset('js/tailwindConfig.js') }}"></script>
<script>
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
      navigator.serviceWorker.register('/sw.js');
    });
  }
</script>
<script src="/js/pwa.js"></script>
<style>
    .menu-item.active {
        background: rgba(255, 255, 255, 0.15);
        border-bottom-color: white !important;
    }
</style>
