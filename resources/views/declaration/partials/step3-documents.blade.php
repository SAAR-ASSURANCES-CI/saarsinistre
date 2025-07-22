<div id="step-3" class="step-content hidden p-8">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-green-500 to-saar-green rounded-2xl mb-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 mb-2">Documents Requis</h3>
        <p class="text-gray-600">Téléchargez les documents nécessaires pour votre dossier</p>
    </div>
    <div class="space-y-8">
        <!-- Documents obligatoires -->
        <div>
            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <span class="w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs mr-2">!</span>
                Documents Obligatoires
            </h4>
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Carte grise recto -->
                <div class="upload-zone" data-field="carte_grise_recto">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Carte grise (Recto) *
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-saar-blue hover:bg-blue-50 transition-all duration-300 cursor-pointer">
                        <input type="file" accept=".pdf,.jpg,.jpeg,.png" required class="hidden">
                        <div class="upload-content">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span class="text-sm text-saar-blue font-semibold">Cliquer pour télécharger</span>
                            <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (max 5MB)</p>
                        </div>
                        <div class="upload-success hidden">
                            <svg class="mx-auto h-12 w-12 text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <p class="text-sm text-green-600 font-semibold">Fichier téléchargé ✓</p>
                        </div>
                    </div>
                </div>
                <!-- Carte grise verso -->
                <div class="upload-zone" data-field="carte_grise_verso">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Carte grise (Verso) *
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-saar-blue hover:bg-blue-50 transition-all duration-300 cursor-pointer">
                        <input type="file" accept=".pdf,.jpg,.jpeg,.png" required class="hidden">
                        <div class="upload-content">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span class="text-sm text-saar-blue font-semibold">Cliquer pour télécharger</span>
                            <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (max 5MB)</p>
                        </div>
                        <div class="upload-success hidden">
                            <svg class="mx-auto h-12 w-12 text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <p class="text-sm text-green-600 font-semibold">Fichier téléchargé ✓</p>
                        </div>
                    </div>
                </div>
                <!-- Visite technique recto -->
                <div class="upload-zone" data-field="visite_technique_recto">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Visite technique (Recto) *</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-saar-blue hover:bg-blue-50 transition-all duration-300 cursor-pointer">
                        <input type="file" accept=".pdf,.jpg,.jpeg,.png" required class="hidden">
                        <div class="upload-content">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span class="text-sm text-saar-blue font-semibold">Cliquer pour télécharger</span>
                            <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (max 5MB)</p>
                        </div>
                        <div class="upload-success hidden">
                            <svg class="mx-auto h-12 w-12 text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <p class="text-sm text-green-600 font-semibold">Fichier téléchargé ✓</p>
                        </div>
                    </div>
                </div>
                <!-- Visite technique verso -->
                <div class="upload-zone" data-field="visite_technique_verso">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Visite technique (Verso) *</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-saar-blue hover:bg-blue-50 transition-all duration-300 cursor-pointer">
                        <input type="file" accept=".pdf,.jpg,.jpeg,.png" required class="hidden">
                        <div class="upload-content">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span class="text-sm text-saar-blue font-semibold">Cliquer pour télécharger</span>
                            <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (max 5MB)</p>
                        </div>
                        <div class="upload-success hidden">
                            <svg class="mx-auto h-12 w-12 text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <p class="text-sm text-green-600 font-semibold">Fichier téléchargé ✓</p>
                        </div>
                    </div>
                </div>
                <!-- Attestation d'assurance -->
                <div class="upload-zone" data-field="attestation_assurance">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Attestation d'assurance *</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-saar-blue hover:bg-blue-50 transition-all duration-300 cursor-pointer">
                        <input type="file" accept=".pdf,.jpg,.jpeg,.png" required class="hidden">
                        <div class="upload-content">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span class="text-sm text-saar-blue font-semibold">Cliquer pour télécharger</span>
                            <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (max 5MB)</p>
                        </div>
                        <div class="upload-success hidden">
                            <svg class="mx-auto h-12 w-12 text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <p class="text-sm text-green-600 font-semibold">Fichier téléchargé ✓</p>
                        </div>
                    </div>
                </div>
                <!-- Permis de conduire -->
                <div class="upload-zone" data-field="permis_conduire">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Permis de conduire *</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-saar-blue hover:bg-blue-50 transition-all duration-300 cursor-pointer">
                        <input type="file" accept=".pdf,.jpg,.jpeg,.png" required class="hidden">
                        <div class="upload-content">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span class="text-sm text-saar-blue font-semibold">Cliquer pour télécharger</span>
                            <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (max 5MB)</p>
                        </div>
                        <div class="upload-success hidden">
                            <svg class="mx-auto h-12 w-12 text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <p class="text-sm text-green-600 font-semibold">Fichier téléchargé ✓</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Photos du véhicule -->
        <div>
            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <span class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs mr-2">📷</span>
                Photos du Véhicule
            </h4>
            <div class="upload-zone" data-field="photos_vehicule">
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-saar-blue hover:bg-blue-50 transition-all duration-300 cursor-pointer">
                    <input type="file" accept=".jpg,.jpeg,.png" multiple class="hidden">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h36a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h2m2 0h.01m2 0h.01m2 0h.01m2 0h.01M10 12l8 8 8-8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="upload-content">
                        <span class="text-lg text-saar-blue font-semibold">Télécharger les photos</span>
                        <p class="text-sm text-gray-500 mt-2">JPG, PNG - Plusieurs fichiers acceptés</p>
                        <p class="text-xs text-gray-400 mt-1">Recommandé: photos des dommages sous différents angles</p>
                    </div>
                    <div class="upload-success hidden">
                        <p class="text-sm text-green-600 font-semibold"><span class="photo-count">0</span> photo(s) téléchargée(s) ✓</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 