<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau mot de passe - SAAR Assurances</title>
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
                    Nouveau mot de passe
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
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                <p class="text-sm">
                    <strong>Code vérifié !</strong> Vous pouvez maintenant définir votre nouveau mot de passe. 
                    Assurez-vous qu'il soit sécurisé et facile à retenir.
                </p>
            </div>

            <!-- Formulaire de nouveau mot de passe -->
            <form class="mt-8 space-y-6" action="{{ route('password.reset.update.assure') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Nouveau mot de passe
                        </label>
                        <input id="password" name="password" type="password" required
                            class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-saar-blue focus:border-saar-blue focus:z-10 sm:text-sm"
                            placeholder="Votre nouveau mot de passe">
                        <p class="mt-1 text-xs text-gray-500">
                            Le mot de passe doit contenir au moins 8 caractères
                        </p>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirmer le mot de passe
                        </label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                            class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-saar-blue focus:border-saar-blue focus:z-10 sm:text-sm"
                            placeholder="Confirmez votre nouveau mot de passe">
                    </div>
                </div>

                <!-- Indicateur de force du mot de passe -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Force du mot de passe
                    </label>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div id="password-strength" class="h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                    <div id="password-feedback" class="text-xs text-gray-500"></div>
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
                        Définir le nouveau mot de passe
                    </button>
                </div>
            </form>

            <!-- Script pour vérifier la force du mot de passe -->
            <script>
                const passwordInput = document.getElementById('password');
                const passwordStrength = document.getElementById('password-strength');
                const passwordFeedback = document.getElementById('password-feedback');

                passwordInput.addEventListener('input', function() {
                    const password = this.value;
                    let strength = 0;
                    let feedback = '';

                    if (password.length >= 8) strength += 25;
                    if (password.match(/[a-z]/)) strength += 25;
                    if (password.match(/[A-Z]/)) strength += 25;
                    if (password.match(/[0-9]/)) strength += 25;

                    passwordStrength.style.width = strength + '%';

                    if (strength <= 25) {
                        passwordStrength.className = 'h-2 rounded-full transition-all duration-300 bg-red-500';
                        feedback = 'Très faible';
                    } else if (strength <= 50) {
                        passwordStrength.className = 'h-2 rounded-full transition-all duration-300 bg-orange-500';
                        feedback = 'Faible';
                    } else if (strength <= 75) {
                        passwordStrength.className = 'h-2 rounded-full transition-all duration-300 bg-yellow-500';
                        feedback = 'Moyen';
                    } else {
                        passwordStrength.className = 'h-2 rounded-full transition-all duration-300 bg-green-500';
                        feedback = 'Fort';
                    }

                    passwordFeedback.textContent = feedback;
                });
            </script>
        </div>
    </div>
</body>

</html>
