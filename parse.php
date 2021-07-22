<?php
// Developed with PHP7.4

include "./classes/AudioBookRenderer.php";
include "./classes/XMLFileReader.php";

$arguments = getopt(null, ['f:', 'cs:', 'mct:', 'lcs:']);

if (count($arguments) != 4) {
    die('Some of required parameters skipped f - filename, cs - (seconds) chapter silent, mct - (seconds) max chapter time, lcs - (seconds) long chapter silent time');
}

try {
    $audioBookRender = new AudioBookRenderer(
        (string) $arguments['f'],
        (float) $arguments['cs'],
        (float) $arguments['mct'],
        (float) $arguments['lcs']
    );
    $data = $audioBookRender->render();

    print_r($data);
    try {
        $name = explode('.', $arguments['f']);
        $name = 'result_' . $name[0] . '_' . date('YmdHis') . '.json';
        file_put_contents(
            'result\\' . $name,
            json_encode($data)
        );
        echo 'Saved to json file: ' . $name;
    } catch (\Throwable $exception) {
        echo 'Can not save to json file: ' . $exception->getMessage();
    }
} catch (\Throwable $exception) {
    echo "Error: " . $exception->getMessage();
}

