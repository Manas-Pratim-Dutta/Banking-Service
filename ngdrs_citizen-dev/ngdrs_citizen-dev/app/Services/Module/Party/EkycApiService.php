<?php

namespace App\Services\Module\Party;

use App\Services\Actions\Party\ProcessEkycAction;

class EkycApiService
{
    protected ProcessEkycAction
        $processEkycAction;

    public function __construct(
        ProcessEkycAction
        $processEkycAction
    ) {

        $this->processEkycAction =
            $processEkycAction;

    }

    public function process(): array
    {
        return $this
            ->processEkycAction
            ->execute();
    }
}