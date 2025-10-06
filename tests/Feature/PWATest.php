<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PWATest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_manifest_file()
    {
        // Vérifier que le fichier manifest existe
        $this->assertFileExists(public_path('manifest.json'));
        
        // Vérifier le contenu du manifest
        $manifest = json_decode(file_get_contents(public_path('manifest.json')), true);
        $this->assertEquals('SAARCISinistres', $manifest['short_name']);
        $this->assertEquals('SAARCISinistres - Gestion des sinistres', $manifest['name']);
    }

    #[Test]
    public function it_has_service_worker()
    {
        // Vérifier que le fichier service worker existe
        $this->assertFileExists(public_path('sw.js'));
        
        // Vérifier le contenu du service worker
        $content = file_get_contents(public_path('sw.js'));
        $this->assertStringContainsString('self.addEventListener', $content);
    }

    #[Test]
    public function it_has_pwa_icons()
    {
        $iconSizes = [16, 32, 48, 72, 96, 128, 144, 152, 192, 384, 512];

        foreach ($iconSizes as $size) {
            $iconPath = public_path("icons/icon-{$size}x{$size}.png");
            $this->assertFileExists($iconPath, "Icon {$size}x{$size} should exist");
        }
    }

    #[Test]
    public function it_has_offline_page()
    {
        // Vérifier que le fichier offline existe
        $this->assertFileExists(public_path('offline.html'));
        
        // Vérifier le contenu de la page offline
        $content = file_get_contents(public_path('offline.html'));
        $this->assertStringContainsString('Vous êtes hors ligne', $content);
    }

    #[Test]
    public function it_has_pwa_meta_tags()
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('manifest.json', false);
        $response->assertSee('mobile-web-app-capable', false);
        $response->assertSee('theme-color', false);
    }
}
