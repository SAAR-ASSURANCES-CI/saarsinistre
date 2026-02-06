<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreExpertiseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Les routes sont déjà protégées par le middleware 'auth' + 'role',
        // on peut donc autoriser ici.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'lieu_expertise' => ['required', 'string', 'max:255'],
            'contact_client' => ['nullable', 'string', 'max:20'],
            'vehicule_expertise' => ['nullable', 'string', 'max:255'],

            // Tableau d'opérations
            'operations' => ['required', 'array', 'min:1'],
            'operations.*.libelle' => ['required', 'string', 'max:255'],
            'operations.*.echange' => ['sometimes', 'boolean'],
            'operations.*.reparation' => ['sometimes', 'boolean'],
            'operations.*.controle' => ['sometimes', 'boolean'],
            'operations.*.peinture' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Validation supplémentaire : au moins une case (ECH/REP/CTL/P) doit être cochée par ligne.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $operations = $this->input('operations', []);

            if (!is_array($operations)) {
                return;
            }

            foreach ($operations as $index => $operation) {
                $echange = !empty($operation['echange']);
                $reparation = !empty($operation['reparation']);
                $controle = !empty($operation['controle']);
                $peinture = !empty($operation['peinture']);

                if (!$echange && !$reparation && !$controle && !$peinture) {
                    $validator->errors()->add(
                        "operations.$index",
                        'Au moins une case (ECH, REP, CTL ou P) doit être cochée pour chaque opération.'
                    );
                }
            }
        });
    }
}
