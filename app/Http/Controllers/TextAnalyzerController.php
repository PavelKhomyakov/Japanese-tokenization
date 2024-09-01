<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class TextAnalyzerController extends Controller
{
    public function analyze(Request $request)
    {
        $text = $request->input('text');

        $env = [
            'PATH' => '/usr/local/bin:' . getenv('PATH'),
        ];

        // Run the Kuromoji
        $process = new Process(['npm', 'run', 'tokenize'], null, $env);
        $process->setInput($text);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $rawOutput = $process->getOutput();
        file_put_contents('npm_output.log', $rawOutput);

        // extract JSON content
        if (preg_match('/\[\{.*\}\]/s', $rawOutput, $matches)) {
            $jsonOutput = $matches[0];
        } else {
            throw new \Exception('Failed to extract JSON from output.');
        }

        $tokens = json_decode($jsonOutput, true);

        if ($tokens === null) {
            throw new \Exception('JSON decode error: ' . json_last_error_msg() . "\nOutput: " . $rawOutput);
        }

        if (!is_array($tokens)) {
            return response()->json(['error' => 'Tokens should be an array.'], 500);
        }

        $results = [];
        foreach ($tokens as $token) {
            $keyword = urlencode($token['surface_form']);
            $response = Http::get("https://jisho.org/api/v1/search/words?keyword=$keyword");

            if ($response->successful()) {
                $data = $response->json();
                $results[] = [
                    'word' => $token['surface_form'],
                    'meanings' => $data['data'],
                ];
            }
        }

        return view('analyze', ['results' => $results, 'text' => $text]);
    }
}
