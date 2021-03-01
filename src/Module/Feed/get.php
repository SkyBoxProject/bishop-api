<?php
declare(strict_types=1);
require '../app/bootstrap.php';

set_error_handler(
    static function ($err_severity, $err_msg, $err_file, $err_line) {
        throw new ErrorException ($err_msg, 0, $err_severity, $err_file, $err_line);
    }
);

$licenses = [
    'http://hafid.market/xml/14s_avito.xml'  => 'b83097620ca9183bc26f209fcdb1ad67590ecaa604692b031374e89dd8aedf34',    // Толя
    'http://hafid.market/xml/14s_client.xml' => 'b83097620ca9183bc26f209fcdb1ad67590ecaa604692b031374e89dd8aedf34',    // Толя
    'http://butuz.club/xml/vp_avito.xml'     => 'ae4aed906f3f6065b76d1163469b095d9815e1d68e42f53199e755d49d84afdf', //до 05.11.2020
    'http://hafid.market/xml/vp_client.xml'  => 'ae4aed906f3f6065b76d1163469b095d9815e1d68e42f53199e755d49d84afdf', //до 05.11.2020
    //    'http://alisa.market/xml/11h_avito.xml'  => 'ddbed3bd680bf8584a1d3030679a815b3c850a12fbf1e611c6ae9c1b0e014038',
    './xml/test_perfomance.xml'              => 'ddbed3bd680asdfasdf64asd6f84a63c850a12fbf1e611c6ae9c1b0e01403g48',
    './xml/test_perfomance_x500.xml'         => 'ddbed3bd680asdfasdf64asd6f84a63c850a12fbf1e611c6ae9c1b0e01403g48',
    './xml/test_perfomance_x1000.xml'        => 'ddbed3bd680asdfasdf64asd6f84a63c850a12fbf1e611c6ae9c1b0e01403g48',
    './xml/test_perfomance_x2000.xml'        => 'ddbed3bd680asdfasdf64asd6f84a63c850a12fbf1e611c6ae9c1b0e01403g48',
    './xml/test_perfomance_x3000.xml'        => 'ddbed3bd680asdfasdf64asd6f84a63c850a12fbf1e611c6ae9c1b0e01403g48',
    './xml/test_perfomance_x5000.xml'        => 'ddbed3bd680asdfasdf64asd6f84a63c850a12fbf1e611c6ae9c1b0e01403g48',
    './xml/test_perfomance_x10000.xml'       => 'ddbed3bd680asdfasdf64asd6f84a63c850a12fbf1e611c6ae9c1b0e01403g48',
];

$request_body = file_get_contents('php://input');
$post         = json_decode($request_body);

if (isset($post->url)) {
    $postUrl = $post->url;
} else {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    exit();
}
if (isset($post->key)) {
    $key = $post->key;
} else {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    exit();
}

file_put_contents(
    './logs/' . date('d.m.Y') . '.log',
    "\n[" . date('d.m.Y H:i:s') . '] Проверка лицензии: ' . $postUrl,
    FILE_APPEND | LOCK_EX
);

if (
    $licenses[$postUrl] === $key
) // Display the decrypted string
{
    file_put_contents(
        './logs/' . date('d.m.Y') . '.log',
        "\n[" . date('d.m.Y H:i:s') . '] Лицензия пройдена: ' . $postUrl,
        FILE_APPEND | LOCK_EX
    );
    header('Content-Type: text/csv; charset=windows-1251');
    header('Content-Disposition: attachment; filename="export-approved.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $feedService = new \App\Feeds\FeedService();

    $feedService->setUrl($postUrl);
    $feedService->getContent($postUrl);

    $feed = $feedService->getFeed();

    if (isset($post->cut)) {
        $feed->setIsCutText(true, $post->cut);
    }

    if (isset($post->city)) {
        $feed->setIsAddCity(true, $post->city);
    }

    if (isset($post->end_description_text)) {
        $feed->setIsAppendEndDescriptionText(true, $post->end_description_text);
    }

    if (isset($post->stop)) {
        $feed->setIsStopWords(true, $post->stop);
    }

    if (isset($post->skip_last_image) && $post->skip_last_image) {
        $feed->setIsSkipLastImage(true);
    }

    if (isset($post->skip_not_available) && $post->skip_not_available) {
        $feed->setIsSkipNotAvailable(true);
    }

    $feed->run();

    file_put_contents(
        './logs/' . date('d.m.Y') . '.log',
        "\n[" . date('d.m.Y H:i:s') . '] Файл сгенерирован: ' . $postUrl,
        FILE_APPEND | LOCK_EX
    );
    $handle = $feed->readFile();
    exit($handle);
}

file_put_contents(
    './logs/' . date('d.m.Y') . '.log',
    "\n[" . date('d.m.Y H:i:s') . '] Проверка лицензии отклонена: ' . $postUrl,
    FILE_APPEND | LOCK_EX
);
header($_SERVER['SERVER_PROTOCOL'] . ' 500 License error', true, 500);
echo 'License key not found!';
