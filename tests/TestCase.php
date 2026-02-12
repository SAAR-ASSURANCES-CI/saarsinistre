<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->ensureViteManifestExists();
    }

    /**
     * Ensure Vite manifest exists for tests that render views with @vite().
     */
    protected function ensureViteManifestExists(): void
    {
        $buildDir = public_path('build');
        $manifestPath = $buildDir . '/manifest.json';
        
        if (!file_exists($buildDir)) {
            mkdir($buildDir, 0755, true);
        }
        
        if (!file_exists($manifestPath)) {
            file_put_contents($manifestPath, json_encode([
                'resources/css/app.css' => [
                    'file' => 'assets/app-test.css',
                    'src' => 'resources/css/app.css',
                ],
                'resources/js/app.js' => [
                    'file' => 'assets/app-test.js',
                    'src' => 'resources/js/app.js',
                ],
            ], JSON_PRETTY_PRINT));
        }
        
        $assetsDir = $buildDir . '/assets';
        if (!file_exists($assetsDir)) {
            mkdir($assetsDir, 0755, true);
        }
        
        $cssFile = $assetsDir . '/app-test.css';
        $jsFile = $assetsDir . '/app-test.js';
        
        if (!file_exists($cssFile)) {
            file_put_contents($cssFile, '/* Test CSS file */');
        }
        
        if (!file_exists($jsFile)) {
            file_put_contents($jsFile, '// Test JS file');
        }
    }
}
