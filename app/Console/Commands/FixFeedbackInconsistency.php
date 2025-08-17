<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Feedback;

class FixFeedbackInconsistency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feedback:fix-inconsistency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corrige les incohérences entre note_service et humeur_emoticon dans les feedbacks';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Recherche des feedbacks avec des incohérences...');
        
        // Mapping entre notes et émoticons
        $noteToEmoticon = [
            1 => '😠', 
            2 => '😕',  
            3 => '😐', 
            4 => '🙂', 
            5 => '😊' 
        ];
        
        $feedbacks = Feedback::whereNotNull('note_service')->whereNotNull('humeur_emoticon')->get();
        $fixedCount = 0;
        $totalCount = $feedbacks->count();
        
        if ($totalCount === 0) {
            $this->warn('Aucun feedback trouvé avec note et émoticon.');
            return;
        }
        
        $this->info("📊 Total des feedbacks à vérifier : {$totalCount}");
        
        $headers = ['ID', 'Note', 'Émoticon actuel', 'Émoticon attendu', 'Status'];
        $tableData = [];
        
        foreach ($feedbacks as $feedback) {
            $expectedEmoticon = $noteToEmoticon[$feedback->note_service] ?? null;
            $currentEmoticon = $feedback->humeur_emoticon;
            
            $status = $currentEmoticon === $expectedEmoticon ? '✅ Correct' : '❌ Incohérent';
            
            $tableData[] = [
                $feedback->id,
                $feedback->note_service . '/5',
                $currentEmoticon,
                $expectedEmoticon ?? 'N/A',
                $status
            ];
            
            if ($currentEmoticon !== $expectedEmoticon && $expectedEmoticon) {
                $fixedCount++;
            }
        }
        
        $this->table($headers, $tableData);
        
        if ($fixedCount === 0) {
            $this->info('🎉 Tous les feedbacks sont déjà cohérents !');
            return;
        }
        
        $this->warn("⚠️  {$fixedCount} feedbacks nécessitent une correction.");
        
        if ($this->confirm('Voulez-vous corriger automatiquement ces incohérences ?')) {
            $this->info('🔧 Correction en cours...');
            
            $progressBar = $this->output->createProgressBar($fixedCount);
            $progressBar->start();
            
            foreach ($feedbacks as $feedback) {
                $expectedEmoticon = $noteToEmoticon[$feedback->note_service] ?? null;
                
                if ($feedback->humeur_emoticon !== $expectedEmoticon && $expectedEmoticon) {
                    $feedback->update(['humeur_emoticon' => $expectedEmoticon]);
                    $progressBar->advance();
                }
            }
            
            $progressBar->finish();
            $this->newLine();
            $this->info("✅ {$fixedCount} feedbacks corrigés avec succès !");
            
            $this->info('📋 État après correction :');
            $correctedData = [];
            
            foreach ($feedbacks->fresh() as $feedback) {
                $expectedEmoticon = $noteToEmoticon[$feedback->note_service] ?? null;
                $status = $feedback->humeur_emoticon === $expectedEmoticon ? '✅ Correct' : '❌ Toujours incohérent';
                
                $correctedData[] = [
                    $feedback->id,
                    $feedback->note_service . '/5',
                    $feedback->humeur_emoticon,
                    $expectedEmoticon ?? 'N/A',
                    $status
                ];
            }
            
            $this->table($headers, $correctedData);
        } else {
            $this->info('Correction annulée.');
        }
    }
}