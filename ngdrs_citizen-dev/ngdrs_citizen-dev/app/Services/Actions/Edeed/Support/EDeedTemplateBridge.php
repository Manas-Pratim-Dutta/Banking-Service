<?php

namespace App\Services\Actions\EDeed\Support;
require_once app_path('Legacy/EDeedTemplate.php');
/**
 * EDeedTemplate.php declares no namespace and calls
 * $this->loadModel('num_to_words') / $this->num_to_words->in_words(...)
 * internally — pure CakePHP-isms. Rather than touch that file, we extend
 * it here and supply just enough compatibility surface.
 *
 * EDeedTemplate.php has no namespace, so it must be included via
 * composer's "files" autoload (see composer.json snippet below) or a
 * manual require before this class loads.
 */
#[\AllowDynamicProperties]

class EDeedTemplateBridge extends \EDeedTemplate
{
    public ?NumberToWords $num_to_words = null;

    public function loadModel(string $name): void
    {
        match ($name) {
            'num_to_words' => $this->num_to_words = new NumberToWords(),
            default => null, // extend here if a template later needs another "model"
        };
    }
}