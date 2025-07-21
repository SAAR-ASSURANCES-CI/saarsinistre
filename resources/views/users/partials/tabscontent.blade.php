<div class="tab-content">
    <!-- Onglet Gestionnaires -->
    <div class="hidden p-4 rounded-lg bg-gray-50" id="gestionnaires" role="tabpanel" aria-labelledby="gestionnaires-tab">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <div id="gestionnaires-container">
                    @include('users.partials.gestionnaires-table', ['gestionnaires' => $gestionnaires])
                </div>
            </div>
        </div>
    </div>

    <!-- Onglet Assurés -->
    <div class="hidden p-4 rounded-lg bg-gray-50" id="assures" role="tabpanel" aria-labelledby="assures-tab">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <div id="assures-container">
                    @include('users.partials.assures-table', ['assures' => $assures])
                </div>
            </div>
        </div>
    </div>
</div>
