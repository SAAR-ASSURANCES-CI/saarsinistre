<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - SAAR Assurances</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
                    Réinitialisation de mot de passe
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
                    <strong>Instructions :</strong> Entrez le numéro de téléphone que vous avez utilisé lors de votre déclaration de sinistre. 
                    Un code de vérification à 6 chiffres vous sera envoyé par SMS.
                </p>
            </div>

            <!-- Formulaire de demande -->
            <form class="mt-8 space-y-6" action="{{ route('password.reset.send.assure') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="telephone" class="block text-sm font-medium text-gray-700 mb-2">
                            Numéro de téléphone
                        </label>
                        <input id="telephone" name="telephone" type="tel" autocomplete="tel" required
                            value="{{ old('telephone') }}"
                            class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-saar-blue focus:border-saar-blue focus:z-10 sm:text-sm"
                            placeholder="Ex: 0701234567 ou +225701234567">
                        <p class="mt-1 text-xs text-gray-500">
                            Format: 0701234567, 225701234567 ou +225701234567
                        </p>
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-saar-blue to-blue-600 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-saar-blue transition-all duration-200">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-blue-300 group-hover:text-blue-400"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                        Envoyer le code de vérification
                    </button>
                </div>

                <!-- Lien de retour -->
                <div class="text-center">
                    <a href="{{ route('login.assure') }}" 
                       class="text-saar-blue hover:text-blue-700 text-sm font-medium">
                        ← Retour à la connexion
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
