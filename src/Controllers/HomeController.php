<?php

namespace Layman\LaravelJournal\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('journal.auth');
    }

    public function index(Request $request)
    {
        $config  = config('journal');
        $logs    = $this->logs($config['channels']);
        $channel = $request->get('channel');
        $path    = $request->get('path');
        $page    = max((int)$request->get('page', 1), 1);
        $perPage = $config['perPage'] ?? 50;
        $search  = $request->get('search');

        $currentLogFile = null;
        $logLines       = [];
        $totalLines     = 0;

        if ($path && file_exists(storage_path($path))) {
            $logPath        = storage_path($path);
            $currentLogFile = $path;
            $totalLines     = $this->countFileLines($logPath);
            $logLines       = $this->getLogPageLines($logPath, $page, $perPage, $search);
        }

        return view('journal::home', compact(
            'logs', 'channel', 'currentLogFile',
            'logLines', 'page', 'perPage', 'totalLines'
        ));
    }

    private function logs($channels): array
    {
        $logs = [];
        foreach ($channels as $channel) {
            $channelConfig = config("logging.channels.{$channel}");
            if (!$channelConfig || !isset($channelConfig['path'])) {
                continue;
            }
            $storageDir     = str_replace(storage_path() . DIRECTORY_SEPARATOR, '', $channelConfig['path']);
            $logDir         = dirname($storageDir);
            $files          = glob(storage_path($logDir) . DIRECTORY_SEPARATOR . '*.log');
            $logs[$channel] = array_map(function ($file) {
                return [
                    'basename' => basename($file),
                    'path' => str_replace(storage_path() . DIRECTORY_SEPARATOR, '', $file),
                ];
            }, $files);
        }
        return $logs;
    }

    private function countFileLines(string $filePath): int
    {
        $lineCount = 0;
        $file      = new \SplFileObject($filePath, 'r');
        while (!$file->eof()) {
            $file->fgets();
            $lineCount++;
        }
        return $lineCount;
    }

    private function getLogPageLines(string $filePath, int $page, int $perPage, ?string $search = null): array
    {
        $lines     = [];
        $start     = ($page - 1) * $perPage;
        $readCount = 0;
        $file      = new \SplFileObject($filePath, 'r');
        $file->seek($start);

        while (!$file->eof() && $readCount < $perPage) {
            $line = rtrim($file->fgets(), "\r\n");
            if ($search && stripos($line, $search) === false) {
                continue;
            }
            $lines[] = $line;
            $readCount++;
        }
        return $lines;
    }
}
