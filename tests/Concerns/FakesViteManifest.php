<?php

namespace Tests\Concerns;

/**
 * Every authenticated page in this app calls @vite(['resources/sass/app.scss',
 * 'resources/js/app.js']) via layouts/app.blade.php. That call reads
 * public/build/manifest.json - a file that only exists after running
 * `npm run build`. Tests shouldn't depend on that build step having been
 * run, so this trait writes a minimal, valid fake manifest before the test
 * and removes it afterward, letting any @vite() call resolve normally.
 *
 * (This is the exact same manifest file whose absence caused the very
 * first bug we debugged in this project - see PhisingSim-Project-Explained.md.)
 */
trait FakesViteManifest
{
    protected function fakeViteManifest(): void
    {
        $buildPath = public_path('build');

        if (! is_dir($buildPath)) {
            mkdir($buildPath, 0755, true);
        }

        file_put_contents($buildPath.'/manifest.json', json_encode([
            'resources/sass/app.scss' => [
                'file' => 'assets/app-test.css',
                'src' => 'resources/sass/app.scss',
                'isEntry' => true,
            ],
            'resources/js/app.js' => [
                'file' => 'assets/app-test.js',
                'src' => 'resources/js/app.js',
                'isEntry' => true,
            ],
        ]));
    }

    protected function removeFakeViteManifest(): void
    {
        $manifest = public_path('build/manifest.json');

        if (file_exists($manifest)) {
            unlink($manifest);
        }
    }
}
