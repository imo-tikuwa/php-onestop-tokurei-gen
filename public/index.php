<?php
use Siler\Route;
use Siler\Twig;
use Siler\Http\Request;
use Siler\Http\Response;
use Wareki\Wareki;

require dirname(__DIR__) . '/vendor/autoload.php';

Response\header('Access-Control-Allow-Methods', 'GET, POST');
Twig\init(dirname(__DIR__) . '/templates');

Route\get('/', function () {
    $params = Request\get();
    Response\html(Twig\render('index.twig', $params));
});

Route\post('/', function () {
    $params = Request\post();

    # 生年月日
    if (isset($params['birth_date'])) {
        $birth_date = new Wareki($params['birth_date']);
        $birth_date_gengo = $birth_date->format('{gengou}');
        $birth_year = $birth_date->format('{nendo}');
        $birth_date = new DateTime($params['birth_date']);
        $birth_month = $birth_date->format('n');
        $birth_day = $birth_date->format('j');
    }

    # 出力年月日
    if (isset($params['output_date'])) {
        $output_date = new Wareki($params['output_date']);
        $output_year = $output_date->format('{nendo}');
        $output_date = new DateTime($params['output_date']);
        $output_month = $output_date->format('n');
        $output_day = $output_date->format('j');
    }

    # 寄附年月日
    if (isset($params['donation_date'])) {
        $donation_date = new Wareki($params['donation_date']);
        $donation_year = $donation_date->format('{nendo}');
        $donation_date = new DateTime($params['donation_date']);
        $donation_month = $donation_date->format('n');
        $donation_day = $donation_date->format('j');
    }

    try {
        $report = new Thinreports\Report(dirname(__DIR__) . '/tlfs/onestop.tlf');
        $page = $report->addPage();
        $page->setItemValue('top_current_year', $output_year);
        $page->setItemValue('current_year', $output_year);
        $page->setItemValue('current_month', $output_month);
        $page->setItemValue('current_day', $output_day);
        $page->setItemValue('mayor', $params['mayor']);
        $page->setItemValue('zip', "〒{$params['zip']}");
        $page->setItemValue('address', $params['address']);
        $page->setItemValue('phone_number', $params['phone_number']);
        $page->setItemValue('name_kana', $params['name_kana']);
        $page->setItemValue('name', $params['name']);
        $page->setItemValue('my_number', $params['my_number']);
        switch ($params['sex']) {
            case 'man':
                $page->setItemValue('man', "〇");
                break;
            case 'woman':
                $page->setItemValue('woman', "〇");
                break;
        }
        switch ($birth_date_gengo) {
            case '明治':
                $page->setItemValue('year_meiji', "〇");
                break;
            case '大正':
                $page->setItemValue('year_taisyo', "〇");
                break;
            case '昭和':
                $page->setItemValue('year_syowa', "〇");
                break;
            case '平成':
                $page->setItemValue('year_heisei', "〇");
                break;
            case '令和':
                $page->setItemValue('year_reiwa', "〇");
                break;
        }
        $page->setItemValue('birth_year', $birth_year);
        $page->setItemValue('birth_month', $birth_month);
        $page->setItemValue('birth_day', $birth_day);
        $page->setItemValue('donation_year', $donation_year);
        $page->setItemValue('donation_month', $donation_month);
        $page->setItemValue('donation_day', $donation_day);
        $page->setItemValue('donation_amount', number_format($params['donation_amount']));
        if ("yes" === $params['check_one']) {
            $page->setItemValue('check_one', "\u2713");
        }
        if ("yes" === $params['check_two']) {
            $page->setItemValue('check_two', "\u2713");
        }

        $filename = (new DateTime())->format('YmdHis') . '_onestop.pdf';
        $filepath = dirname(__DIR__) . '/output/' . $filename;
        $report->generate($filepath);

        Response\header('Content-Type', 'application/octet-stream');
        Response\header('Content-Length', filesize($filepath));
        Response\header('Content-Disposition', "attachment;filename=\"{$filename}\"");
        readfile($filepath);
    } catch (Exception $e) {
        var_dump($e->getMessage());
    }
});

// public以下のリソースを返すルーティング
// これ必要？？（無いと全部text/htmlで読み込まれてしまうので定義してみる）
Route\get('/(.+)', function ($route_param) {
    $filepath = dirname(__FILE__) . '/' . $route_param[1];
    $fileext = pathinfo($filepath, PATHINFO_EXTENSION);
    if (file_exists($filepath)) {
        $content_type = 'text/html';
        switch ($fileext) {
            case 'css':
                $content_type = 'text/css; charset=UTF-8';
                break;
            case 'js':
                $content_type = 'application/javascript';
                break;
            case 'jpg':
            case 'jpeg':
                $content_type = 'image/jpeg';
                break;
            case 'ico':
                $content_type = 'image/x-icon';
                break;
        }
        Response\header('Content-Type', $content_type);
        Response\header('Content-Length', filesize($filepath));
        readfile($filepath);
    }
});

if (!Route\did_match()) {
    Response\html('Not found', 404);
}