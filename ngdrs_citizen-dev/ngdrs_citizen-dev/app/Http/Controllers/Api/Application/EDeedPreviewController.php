<?php

namespace App\Http\Controllers\Api\Application;

use App\Http\Controllers\Controller;
use App\Services\Actions\EDeed\EDeedDataQueryService;
use App\Services\Actions\EDeed\EDeedTemplateDispatcher;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Throwable;

class EDeedPreviewController extends Controller
{
    public function __construct(
        private EDeedDataQueryService $dataQuery,
        private EDeedTemplateDispatcher $dispatcher,
    ) {
    }

    public function generate(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'token_no' => ['required', 'string'],
            'article_id' => ['required', 'integer'],
            'language' => ['required', Rule::in(['E', 'A', 'B'])],
        ]);
        // dd($validated);

        try {
            $articleId = (int) $validated['article_id'];

            $data = $this->dataQuery->build($validated['token_no'], $articleId);
            // dd($data);
            $html = $this->dispatcher->render($articleId, $validated['language'], $data);
            // dd($data); 
            // dd($html);
            return response()->json(['status' => 'success', 'html' => $html]);
        } catch (Throwable $e) {

            dd([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            report($e);

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }
}