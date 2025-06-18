<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sinistre;
use App\Models\User;
use Carbon\Carbon;

class SinistreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les utilisateurs gestionnaires
        $gestionnaires = User::whereIn('role', ['gestionnaire', 'admin'])->pluck('id')->toArray();

        // Données fictives réalistes pour les sinistres
        $sinistresData = [
            [
                'nom_assure' => 'Kouame Yao Marcel',
                'email_assure' => 'kouame.yao@email.ci',
                'telephone_assure' => '+225 07 12 34 56 78',
                'lieu_sinistre' => 'Carrefour Solibra, Yopougon',
                'circonstances' => 'Collision avec un véhicule qui a grillé le feu rouge au carrefour',
                'montant_estime' => 850000,
                'statut' => 'en_attente'
            ],
            [
                'nom_assure' => 'Fatou Diallo',
                'email_assure' => 'fatou.diallo@gmail.com',
                'telephone_assure' => '+225 05 87 65 43 21',
                'lieu_sinistre' => 'Boulevard Lagunaire, Cocody',
                'circonstances' => 'Accident avec un taxi lors d\'un dépassement dangereux',
                'montant_estime' => 1200000,
                'statut' => 'en_cours'
            ],
            [
                'nom_assure' => 'Adama Traore',
                'email_assure' => 'adama.traore@yahoo.fr',
                'telephone_assure' => '+225 01 23 45 67 89',
                'lieu_sinistre' => 'Autoroute du Nord, Abobo',
                'circonstances' => 'Perte de contrôle du véhicule suite à un éclatement de pneu',
                'montant_estime' => 750000,
                'statut' => 'expertise_requise'
            ],
            [
                'nom_assure' => 'Marie-Claire Kone',
                'email_assure' => 'marie.kone@outlook.com',
                'telephone_assure' => '+225 07 98 76 54 32',
                'lieu_sinistre' => 'Rond-point de la Paix, Bouaké',
                'circonstances' => 'Collision arrière lors d\'un freinage d\'urgence',
                'montant_estime' => 950000,
                'statut' => 'en_attente_documents'
            ],
            [
                'nom_assure' => 'Ibrahim Ouattara',
                'email_assure' => 'ibrahim.ouattara@email.ci',
                'telephone_assure' => '+225 05 11 22 33 44',
                'lieu_sinistre' => 'Avenue Chardy, Plateau',
                'circonstances' => 'Vol à main armée avec dégâts sur le véhicule',
                'montant_estime' => 450000,
                'statut' => 'pret_reglement'
            ],
            [
                'nom_assure' => 'Akissi N\'Guessan',
                'email_assure' => 'akissi.nguessan@gmail.com',
                'telephone_assure' => '+225 07 55 66 77 88',
                'lieu_sinistre' => 'Carrefour Gesco, Marcory',
                'circonstances' => 'Incendie du véhicule suite à un court-circuit électrique',
                'montant_estime' => 2800000,
                'statut' => 'regle',
                'montant_regle' => 2650000
            ],
            [
                'nom_assure' => 'Seydou Bamba',
                'email_assure' => 'seydou.bamba@yahoo.com',
                'telephone_assure' => '+225 01 44 55 66 77',
                'lieu_sinistre' => 'Route de Bassam, Grand-Bassam',
                'circonstances' => 'Sortie de route due aux intempéries et mauvaise visibilité',
                'montant_estime' => 1100000,
                'statut' => 'refuse'
            ],
            [
                'nom_assure' => 'Amenan Yapi',
                'email_assure' => 'amenan.yapi@hotmail.com',
                'telephone_assure' => '+225 05 99 88 77 66',
                'lieu_sinistre' => 'Zone industrielle, Vridi',
                'circonstances' => 'Dégâts causés par la chute d\'un arbre lors d\'une tempête',
                'montant_estime' => 680000,
                'statut' => 'clos'
            ],
            [
                'nom_assure' => 'Bakary Coulibaly',
                'email_assure' => 'bakary.coulibaly@email.ci',
                'telephone_assure' => '+225 07 33 22 11 00',
                'lieu_sinistre' => 'Carrefour Stella, Adjamé',
                'circonstances' => 'Collision latérale lors d\'un changement de voie sans signalisation',
                'montant_estime' => 920000,
                'statut' => 'en_attente'
            ],
            [
                'nom_assure' => 'Raissa Gbagbo',
                'email_assure' => 'raissa.gbagbo@gmail.com',
                'telephone_assure' => '+225 01 77 88 99 00',
                'lieu_sinistre' => 'Boulevard de Marseille, Treichville',
                'circonstances' => 'Rayures importantes suite à un acte de vandalisme',
                'montant_estime' => 320000,
                'statut' => 'en_cours'
            ],
            [
                'nom_assure' => 'Moussa Sanogo',
                'email_assure' => 'moussa.sanogo@yahoo.fr',
                'telephone_assure' => '+225 05 12 34 56 78',
                'lieu_sinistre' => 'Carrefour CHU, Treichville',
                'circonstances' => 'Accident avec un deux-roues qui n\'a pas respecté la priorité',
                'montant_estime' => 580000,
                'statut' => 'expertise_requise'
            ],
            [
                'nom_assure' => 'Aya Doumbia',
                'email_assure' => 'aya.doumbia@outlook.com',
                'telephone_assure' => '+225 07 65 43 21 09',
                'lieu_sinistre' => 'Rue des Jardins, Cocody',
                'circonstances' => 'Bris de glace suite à la projection d\'une pierre par un camion',
                'montant_estime' => 180000,
                'statut' => 'pret_reglement'
            ],
            [
                'nom_assure' => 'Konan Koffi',
                'email_assure' => 'konan.koffi@email.ci',
                'telephone_assure' => '+225 01 98 76 54 32',
                'lieu_sinistre' => 'Autoroute Abidjan-Yamoussoukro, Anyama',
                'circonstances' => 'Crevaison ayant entraîné des dommages sur la jante et le pneu',
                'montant_estime' => 420000,
                'statut' => 'regle',
                'montant_regle' => 380000
            ],
            [
                'nom_assure' => 'Mariam Touré',
                'email_assure' => 'mariam.toure@gmail.com',
                'telephone_assure' => '+225 05 44 33 22 11',
                'lieu_sinistre' => 'Carrefour Banco, Abobo',
                'circonstances' => 'Collision frontale lors d\'un dépassement risqué',
                'montant_estime' => 1850000,
                'statut' => 'en_attente_documents'
            ],
            [
                'nom_assure' => 'Youssouf Diabaté',
                'email_assure' => 'youssouf.diabate@yahoo.com',
                'telephone_assure' => '+225 07 11 00 99 88',
                'lieu_sinistre' => 'Zone 4C, Marcory',
                'circonstances' => 'Inondation ayant causé des dégâts importants au moteur',
                'montant_estime' => 2200000,
                'statut' => 'en_cours'
            ],
            [
                'nom_assure' => 'Adjoua Assi',
                'email_assure' => 'adjoua.assi@hotmail.com',
                'telephone_assure' => '+225 01 55 44 33 22',
                'lieu_sinistre' => 'Carrefour Ficgayo, Yopougon',
                'circonstances' => 'Collision avec un poteau électrique suite à un malaise du conducteur',
                'montant_estime' => 1680000,
                'statut' => 'expertise_requise'
            ],
            [
                'nom_assure' => 'Dramane Fofana',
                'email_assure' => 'dramane.fofana@email.ci',
                'telephone_assure' => '+225 05 77 66 55 44',
                'lieu_sinistre' => 'Boulevard Latrille, Cocody',
                'circonstances' => 'Rayures profondes causées par un objet tranchant non identifié',
                'montant_estime' => 390000,
                'statut' => 'en_attente'
            ],
            [
                'nom_assure' => 'Gnéi Brou',
                'email_assure' => 'gnei.brou@gmail.com',
                'telephone_assure' => '+225 07 88 99 00 11',
                'lieu_sinistre' => 'Carrefour Canada, Marcory',
                'circonstances' => 'Accident de stationnement avec dégâts sur le pare-chocs arrière',
                'montant_estime' => 280000,
                'statut' => 'pret_reglement'
            ],
            [
                'nom_assure' => 'Salif Keita',
                'email_assure' => 'salif.keita@yahoo.fr',
                'telephone_assure' => '+225 01 22 33 44 55',
                'lieu_sinistre' => 'Rond-point Solibra, Yopougon',
                'circonstances' => 'Vol de rétroviseurs et tentative d\'effraction',
                'montant_estime' => 150000,
                'statut' => 'regle',
                'montant_regle' => 145000
            ],
            [
                'nom_assure' => 'Fatoumata Camara',
                'email_assure' => 'fatoumata.camara@outlook.com',
                'telephone_assure' => '+225 05 66 77 88 99',
                'lieu_sinistre' => 'Avenue 7, Treichville',
                'circonstances' => 'Dégâts causés par la grêle lors d\'un orage violent',
                'montant_estime' => 520000,
                'statut' => 'clos'
            ]
        ];

        foreach ($sinistresData as $index => $data) {
            // Générer des dates réalistes
            $createdAt = Carbon::now()->subDays(rand(1, 30));
            $dateSinistre = $createdAt->copy()->subDays(rand(0, 5));

            // Créer le sinistre
            $sinistre = Sinistre::create([
                'nom_assure' => $data['nom_assure'],
                'email_assure' => $data['email_assure'],
                'telephone_assure' => $data['telephone_assure'],
                'numero_police' => 'POL-' . date('Y') . '-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'date_sinistre' => $dateSinistre,
                'heure_sinistre' => $dateSinistre->copy()->addHours(rand(6, 22))->addMinutes(rand(0, 59)),
                'lieu_sinistre' => $data['lieu_sinistre'],
                'circonstances' => $data['circonstances'],
                'montant_estime' => $data['montant_estime'],
                'montant_regle' => $data['montant_regle'] ?? null,
                'statut' => $data['statut'],
                'created_at' => $createdAt,
                'updated_at' => $createdAt
            ]);

            // Ajouter date de règlement pour les sinistres réglés
            if ($data['statut'] === 'regle' && isset($data['montant_regle'])) {
                $sinistre->update([
                    'date_reglement' => $createdAt->copy()->addDays(rand(5, 20))
                ]);
            }

            // Calculer les jours en cours et le statut de retard
            $sinistre->calculerJoursEnCours();
        }

        $this->command->info('20 sinistres créés avec succès !');
    }
}
