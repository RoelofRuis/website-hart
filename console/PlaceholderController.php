<?php

namespace app\console;

use app\components\Storage;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Console controller for generating placeholder images.
 */
class PlaceholderController extends Controller
{
    /**
     * Generate placeholder images and save them to storage.
     */
    public function actionGenerate()
    {
        $storage = new Storage();
        $placeholders = [
            'course' => $this->getMusicNoteSvg(),
            'teacher' => $this->getPersonSvg(),
            'static' => $this->getInfoSvg(),
        ];

        $this->stdout("Generating and registering placeholder images...\n", Console::BOLD);

        foreach ($placeholders as $type => $svg) {
            $slug = "placeholder-{$type}";
            $this->stdout("Saving placeholder for {$type} (slug: {$slug})... ");
            try {
                $storage->save($svg, 'image/svg+xml', ['slug' => $slug]);
                $this->stdout("OK\n", Console::FG_GREEN);
            } catch (\Exception $e) {
                $this->stdout("FAILED\n", Console::FG_RED);
                $this->stderr($e->getMessage() . "\n");
                return ExitCode::UNSPECIFIED_ERROR;
            }
        }

        $this->stdout("\nAll placeholder images generated and registered successfully!\n", Console::FG_GREEN, Console::BOLD);
        return ExitCode::OK;
    }

    private function getMusicNoteSvg(): string
    {
        $bgColor = '#f8f9fa';
        $iconColor = '#dee2e6';
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 16 16" fill="$iconColor">
  <rect width="16" height="16" fill="$bgColor"/>
  <g transform="translate(2, 2) scale(0.75)">
    <path d="M6 13c0 1.105-1.12 2-2.5 2S1 14.105 1 13c0-1.104 1.12-2 2.5-2s2.5.896 2.5 2zm9-2c0 1.105-1.12 2-2.5 2s-2.5-.895-2.5-2 1.12-2 2.5-2 2.5.895 2.5 2z"/>
    <path fill-rule="evenodd" d="M14 11V2h1v9h-1zM6 3v10H5V3h1z"/>
    <path d="M5 2.905a1 1 0 0 1 .9-.995l8-.8a1 1 0 0 1 1.1.995V3L5 4V2.905z"/>
  </g>
</svg>
SVG;
    }

    private function getPersonSvg(): string
    {
        $bgColor = '#f8f9fa';
        $iconColor = '#dee2e6';
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 16 16" fill="$iconColor">
  <rect width="16" height="16" fill="$bgColor"/>
  <g transform="translate(2, 2) scale(0.75)">
    <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
  </g>
</svg>
SVG;
    }

    private function getInfoSvg(): string
    {
        $bgColor = '#f8f9fa';
        $iconColor = '#dee2e6';
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 16 16" fill="$iconColor">
  <rect width="16" height="16" fill="$bgColor"/>
  <g transform="translate(2, 2) scale(0.75)">
    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
  </g>
</svg>
SVG;
    }
}
