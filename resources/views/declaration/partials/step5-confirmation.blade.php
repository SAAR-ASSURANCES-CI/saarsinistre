<div id="step-5" class="step-content hidden p-8">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl mb-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 mb-2">Vérification et Envoi</h3>
        <p class="text-gray-600">Vérifiez vos informations avant l'envoi final</p>
    </div>
        <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-6 mb-6 border border-gray-200">
        <h4 class="font-bold text-gray-900 mb-6 text-lg">📋 Récapitulatif de votre déclaration</h4>
                <div class="mb-6">
            <h5 class="font-semibold text-gray-800 mb-3 text-sm uppercase tracking-wide">Vos informations</h5>
            <div class="grid md:grid-cols-2 gap-4 text-sm">
                <p><strong>Nom:</strong> <span id="recap-nom" class="text-gray-700"></span></p>
                <p><strong>Email:</strong> <span id="recap-email" class="text-gray-700"></span></p>
                <p><strong>Téléphone:</strong> <span id="recap-telephone" class="text-gray-700"></span></p>
                <p><strong>N° Police:</strong> <span id="recap-police" class="text-gray-700"></span></p>
            </div>
        </div>
        <div class="mb-6 border-t border-gray-300 pt-6">
            <h5 class="font-semibold text-gray-800 mb-3 text-sm uppercase tracking-wide">Véhicule impliqué</h5>
            <div class="grid md:grid-cols-2 gap-4 text-sm">
                <p><strong>Marque:</strong> <span id="recap-marque" class="text-gray-700"></span></p>
                <p><strong>Modèle:</strong> <span id="recap-modele" class="text-gray-700"></span></p>
                <p><strong>Année:</strong> <span id="recap-annee" class="text-gray-700"></span></p>
                <p><strong>Couleur:</strong> <span id="recap-couleur" class="text-gray-700"></span></p>
                <p><strong>Immatriculation:</strong> <span id="recap-immatriculation" class="text-gray-700"></span></p>
                <p><strong>Type:</strong> <span id="recap-type" class="text-gray-700"></span></p>
                <p><strong>N° Châssis:</strong> <span id="recap-chassis" class="text-gray-700"></span></p>
            </div>
        </div>
        <div class="border-t border-gray-300 pt-6">
            <h5 class="font-semibold text-gray-800 mb-3 text-sm uppercase tracking-wide">Détails du sinistre</h5>
            <div class="grid md:grid-cols-2 gap-4 text-sm">
                <p><strong>Date sinistre:</strong> <span id="recap-date" class="text-gray-700"></span></p>
                <p><strong>Lieu:</strong> <span id="recap-lieu" class="text-gray-700"></span></p>
                <p><strong>Conducteur:</strong> <span id="recap-conducteur" class="text-gray-700"></span></p>
            </div>
        </div>
    </div>
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-saar-blue p-6 mb-8 rounded-r-xl">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-saar-blue" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h4 class="text-lg font-semibold text-saar-blue mb-2">Information importante</h4>
                <p class="text-sm text-blue-700 leading-relaxed mb-3">
                    En soumettant ce formulaire, vous confirmez que toutes les informations fournies sont exactes et complètes.
                    Vous recevrez un email de confirmation avec votre numéro de sinistre dans les minutes qui suivent.
                </p>
                <ul class="list-disc pl-5 space-y-2 text-sm text-blue-800">
                    <li>1) Je certifie que les informations suivantes sont sincères et fiables</li>
                    <li>2) Je m'engage à communiquer toutes autres informations à l'assureur au besoin pour la prise en charge du sinistre</li>
                    <li>3) J'accepte qu'en cas de fausses déclarations, le sinistre peut faire l'objet d'une non prise en charge et même de poursuites judiciaires pour tentative d'escroquerie à l'assurance</li>
                    <li>4) J'atteste avoir pris connaissance des conditions générales automobiles ainsi que des articles 13, 18 et 42 du Code CIMA</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="bg-white border-2 border-gray-200 rounded-xl p-6">
        <div class="flex items-start">
            <input type="checkbox" id="accept-conditions" required class="h-5 w-5 text-saar-blue focus:ring-blue-500 border-gray-300 rounded mt-1">
            <label for="accept-conditions" class="ml-3 text-sm text-gray-700 leading-relaxed">
                <strong>J'accepte les conditions générales</strong> et je certifie sur l'honneur que toutes les informations fournies dans cette déclaration sont exactes et complètes. Je comprends que toute fausse déclaration peut entraîner la nullité de mon contrat d'assurance.
            </label>
        </div>
    </div>
</div>