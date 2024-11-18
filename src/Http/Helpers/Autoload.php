<?php


@include __DIR__ . '/Format.php';
@include __DIR__ . '/GenerateString.php';
@include __DIR__ . '/MasterPage.php';
@include __DIR__ . '/Auth.php';
@include __DIR__ . '/Database.php';
@include __DIR__ . '/Breadcrumb.php';
@include __DIR__ . '/JavascriptEvent.php';
@include __DIR__ . '/ComponentEvent.php';

if (! function_exists('debug')) {
    function debug($data)
    {
        if (! is_string($data)) {
            $content = json_encode($data);
        } else {
            $content = $data;
        }

        //Something to write to txt log
        $content = date('Y-m-d H:i:s') . ' ' . $content . PHP_EOL;

        $fileName = storage_path('logs/') . date('Y.m.d') . '.debug';

        //Save string to log, use FILE_APPEND to append.
        file_put_contents($fileName, $content, FILE_APPEND);
    }
}
