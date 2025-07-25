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
    @media (max-width: 640px) {
        .stats-cards {
            grid-template-columns: repeat(2, 1fr);
        }

        .sinistre-actions {
            flex-direction: column;
            gap: 0.5rem;
        }

        .sinistre-actions button {
            width: 100%;
        }
    }

    @media (max-width: 768px) {
        .search-filters {
            flex-direction: column;
            gap: 1rem;
        }

        .search-filters > div {
            width: 100%;
            max-width: 100%;
        }
    }

    /* Animation pour la modal */
    @keyframes slideIn {
        from {
            transform: translateY(20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .animate-modal {
        animation: slideIn 0.3s ease-out;
    }

    .menu-item.active {
        background: rgba(255, 255, 255, 0.15);
        border-bottom-color: white !important;
    }

    .preview-modal img {
        max-width: 100%;
        max-height: 80vh;
        object-fit: contain;
    }

    .preview-modal {
        transition: all 0.3s ease;
    }

    .preview-modal-buttons {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        padding: 1rem;
        border-top: 1px solid #e5e7eb;
    }
</style>
