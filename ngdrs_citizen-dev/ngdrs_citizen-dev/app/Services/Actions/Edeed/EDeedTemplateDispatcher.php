<?php

namespace App\Services\Actions\EDeed;

use App\Services\Actions\EDeed\Support\EDeedTemplateBridge;
use InvalidArgumentException;

class EDeedTemplateDispatcher
{
    public function render(int $articleId, string $languageType, array $data): string
    {
        $method = config("edeed_templates.$articleId.$languageType");

        if (!$method) {
            throw new InvalidArgumentException(
                "No e-deed template configured for article_id={$articleId}, language={$languageType}."
            );
        }

        $template = new EDeedTemplateBridge();

        if (!method_exists($template, $method)) {
            throw new InvalidArgumentException(
                "EDeedTemplate::{$method}() does not exist (article_id={$articleId}, language={$languageType})."
            );
        }

        return $template->{$method}($data);
    }
}