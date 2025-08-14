<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Erreur') • Saar Sinistre</title>
    @vite(['resources/css/app.css'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        html, body { height: 100%; }
        body { font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, 'Apple Color Emoji', 'Segoe UI Emoji'; }
    </style>
    <meta name="color-scheme" content="light dark">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-full bg-gradient-to-br from-slate-50 via-white to-slate-100 dark:from-slate-900 dark:via-slate-950 dark:to-black">
    <div class="relative isolate">
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -top-24 -left-24 h-72 w-72 rounded-full bg-indigo-500/10 blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 h-72 w-72 rounded-full bg-fuchsia-500/10 blur-3xl"></div>
        </div>

        <main class="relative z-10 flex items-center justify-center px-4 py-16 sm:px-6 lg:px-8">
            <div class="w-full max-w-2xl">
                <div class="mx-auto overflow-hidden rounded-2xl border border-slate-200/60 bg-white/90 shadow-lg backdrop-blur dark:border-slate-800/60 dark:bg-slate-900/80">
                    <div class="px-6 py-8 sm:px-10 sm:py-12">
                        <div class="mb-6 flex items-center justify-between">
                            <div class="inline-flex items-center gap-2">
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-sm ring-1 ring-indigo-500/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6l4 2" />
                                        <circle cx="12" cy="12" r="9" stroke-width="1.5" />
                                    </svg>
                                </span>
                                <span class="text-sm font-semibold tracking-wide text-slate-700 dark:text-slate-200">Saar Sinistre</span>
                            </div>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-700">@yield('badge', 'Erreur')</span>
                        </div>

                        <div class="grid items-center gap-6 sm:grid-cols-5 sm:gap-8">
                            <div class="sm:col-span-2">
                                <div class="mx-auto flex h-40 w-40 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500/10 to-fuchsia-500/10 ring-1 ring-slate-200 dark:ring-slate-800">
                                    @yield('illustration')
                                </div>
                            </div>
                            <div class="sm:col-span-3">
                                <p class="mb-2 text-xs uppercase tracking-widest text-indigo-600 dark:text-indigo-400">Code @yield('code', '—')</p>
                                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white sm:text-3xl">@yield('heading', 'Une erreur est survenue')</h1>
                                <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-slate-300">@yield('message', "Désolé, quelque chose s'est mal passé.")</p>

                                <div class="mt-6 flex flex-wrap items-center gap-3">
                                    @yield('actions')
                                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm ring-1 ring-black/5 hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l9-9 9 9"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 21V9h6v12"/></svg>
                                        Accueil
                                    </a>
                                    <button type="button" onclick="history.back()" class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 ring-1 ring-inset ring-slate-200 hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-200 dark:ring-slate-700 dark:hover:bg-slate-700/80">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"/></svg>
                                        Retour
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between border-t border-slate-200/70 bg-slate-50/60 px-6 py-4 text-xs text-slate-500 dark:border-slate-800/70 dark:bg-slate-900/60 dark:text-slate-400 sm:px-10">
                        <span>Besoin d'aide ? <a class="font-medium text-indigo-600 hover:underline dark:text-indigo-400" href="mailto:support@example.com">Contactez le support</a></span>
                        <span>&copy; {{ date('Y') }} Saar Sinistre</span>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>


