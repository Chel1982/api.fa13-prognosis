<?php

namespace App\Console\Commands;

use App\Models\UserFa13Email;
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

            foreach ($domRegularChamp->find('table[class="alternated-rows-bg wide"] > tr > td[class="teams main"]') as $elGame) {
                //пропускаем первый тег, в котором нет нужной информации
                $th = $elGame->find('th', 0) ? 1 : 0;

                if ($th) {
                    continue;
                }

                foreach ($elGame->find('a') as $item) {
//                    sleep(rand(0,3));
                    $htmlCommand = file_get_contents(self::URL_FA13 . $item->href);
                    $domCommand = HtmlDomParser::str_get_html($htmlCommand);

                    $isManager = $domCommand->find('div[class="block-dark"]') ? false : true;

                    if (!$isManager) {
                        continue;
                    }

                    $opts = array(
                        'http'=>array(
                            'method'=>"GET",
                            'header'=>"Accept-language: en\r\n" .
                                "Cookie: " . env('PARSER_COOKIE')
                        )
                    );
                    $context = stream_context_create($opts);

                    try{
                        $htmlPage = file_get_contents(self::URL_FA13 . $item->href, false, $context);
                    } catch (\Exception $e) {
                        continue;
                    }
                    $dom = HtmlDomParser::str_get_html($htmlPage);

                    $name = $dom->find('table[class="wide alternated-rows-bg"]', 0)
                        ->find('tr', 1)
                        ->find('a', 0)
                        ->plaintext;

                    $email = explode(':', $dom->find('a[class="m-s-em"]', 0)->href);

                    $checkEmail = UserFa13Email::where('email', trim($email[1]))->exists();
                    if(!$checkEmail) {
                        $userFa13Email = new UserFa13Email();
                        $userFa13Email->name = trim($name);
                        $userFa13Email->email = trim($email[1]);
                        $userFa13Email->save();
                    }
                }
            }
        }

    }
}
