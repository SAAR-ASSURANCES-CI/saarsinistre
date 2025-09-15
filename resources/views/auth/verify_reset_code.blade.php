<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification du code - SAAR Assurances</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'saar-red': '#FF0000',
                        'saar-blue': '#1E40AF',
                        'saar-green': '#059669',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gradient-to-br from-red-50 via-white to-green-50 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo et titre -->
            <div class="text-center">
                <div class="mx-auto w-32 h-32 rounded-2xl flex items-center justify-center mb-4">
                    <img src="{{ asset('logo.png') }}" alt="SAAR Assurances" class="w-32 h-32 object-contain">
                </div>
                <h2 class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-saar-red to-saar-green">
                    SAAR ASSURANCES
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Vérification du code
                </p>
            </div>

            <!-- Messages de succès/erreur -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Instructions -->
            <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg">
                <p class="text-sm">
                    <strong>Vérification :</strong> Entrez le code à 6 chiffres que vous avez reçu par SMS. 
                    Ce code expire dans 10 minutes.
                </p>
            </div>

            <!-- Formulaire de vérification -->
            <form class="mt-8 space-y-6" action="{{ route('password.reset.verify.post.assure') }}" method="POST">
                @csrf
                
                <!-- Champ caché pour le téléphone -->
                <input type="hidden" name="telephone" value="{{ session('telephone') }}">
                
                <div class="space-y-4">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                            Code de vérification
                        </label>
                        <input id="code" name="code" type="text" maxlength="6" pattern="[0-9]{6}" required
                            class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-saar-blue focus:border-saar-blue focus:z-10 sm:text-sm text-center text-2xl font-mono tracking-widest"
                            placeholder="000000"
                            autocomplete="one-time-code">
                        <p class="mt-1 text-xs text-gray-500 text-center">
                            Entrez le code à 6 chiffres reçu par SMS
                        </p>
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-saar-green to-green-600 hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-saar-green transition-all duration-200">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-green-300 group-hover:text-green-400"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                        Vérifier le code
                    </button>
                </div>

                <!-- Lien de retour -->
                <div class="text-center">
                    <a href="{{ route('password.reset.request.assure') }}" 
                       class="text-saar-blue hover:text-blue-700 text-sm font-medium">
                        ← Demander un nouveau code
                    </a>
                </div>
            </form>

            <!-- Script pour améliorer l'expérience utilisateur -->
            <script>
                document.getElementById('code').addEventListener('input', function(e) {

                    this.value = this.value.replace(/[^0-9]/g, '');
                        
                    if (this.value.length > 6) {
                        this.value = this.value.slice(0, 6);
                    }

                });

                // Focus automatique sur le champ
                document.getElementById('code').focus();
            </script>
        </div>
    </div>
</body>

</html>
