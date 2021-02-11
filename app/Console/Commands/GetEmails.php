<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use KubAT\PhpSimple\HtmlDomParser;

class GetEmails extends Command
{
    const URL_FA13 = 'https://www.fa13.info';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:getEmails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $html = file_get_contents(self::URL_FA13 . '/tournament/regular');
        $dom = HtmlDomParser::str_get_html($html);
        $linkToParse = $dom->find('tbody a');

        foreach ($linkToParse as $e) {
            print_r('start work with:' . trim($e->plaintext) . PHP_EOL);
            $htmlRegularChamp = file_get_contents(self::URL_FA13 . $e->href . '/last');

            $domRegularChamp = HtmlDomParser::str_get_html($htmlRegularChamp);

//            print_r(self::URL_FA13 . $e->href . '/last' . PHP_EOL);
//            file_put_contents('1.txt', $domRegularChamp->find('table[class="alternated-rows-bg wide"] > tr'));


            foreach ($domRegularChamp->find('table[class="alternated-rows-bg wide"] > tr > td[class="teams main"]') as $elGame) {
                //пропускаем первый тег, в котором нет нужной информации
                $th = $elGame->find('th', 0) ? 1 : 0;

                if ($th) {
                    continue;
                }

                foreach ($elGame->find('a') as $item) {

                    $url = self::URL_FA13 . $item->href;
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__).'/cookie.txt'); // сохранять куки в файл
                    curl_setopt($ch, CURLOPT_COOKIEFILE,  dirname(__FILE__).'/cookie.txt');
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_USERAGENT, "GOOGLE");  // Обманочка
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, array(
                        'login'=>'_username',
                        'login:_username'=>'chel@activist.com',
                        'login:password'=>'12345678',
                    ));
                    echo isAuth($data = curl_exec($ch))?'Success':'Failed';
                    $output = curl_exec($ch);
                    curl_close($ch);
//                    echo $output;






                    $htmlCommand = file_get_contents(self::URL_FA13 . $item->href);
                    $domCommand = HtmlDomParser::str_get_html($htmlCommand);

                    $isManager = $domCommand->find('div[class="block-dark"]') ? false : true;

                    if (!$isManager) {
                        continue;
                    }

                    print_r(self::URL_FA13 . $item->href . PHP_EOL);
//                    print_r($domCommand->find('table[class="wide alternated-rows-bg"] > a[class="m-s-em"]', 0));
                    $tableManager = $domCommand->find('tr');
//                    $htmlManagerPage = file_get_contents(self::URL_FA13 . $tableManager->find('a', 0)->href);
                    $domManagerPage = HtmlDomParser::str_get_html($output);
                    file_put_contents('1.txt', $domManagerPage->find('table[class="wide alternated-rows-bg"]', 0)); die();

                }
            }
        }

    }
}
