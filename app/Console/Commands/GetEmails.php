<?php

namespace App\Console\Commands;

use App\Models\Team;
use App\Models\UserFa13Email;
use Illuminate\Console\Command;
use KubAT\PhpSimple\HtmlDomParser;
use Illuminate\Support\Facades\Http;

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
    protected $description = 'get emails fa13';

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
     * Перед запуском команды необходимо is_active задать параметр 0
     *
     * @return int
     */
    public function handle()
    {
        $html = Http::timeout(30)->get(self::URL_FA13 . '/tournament/regular');
        $dom = HtmlDomParser::str_get_html($html);
        $linkToParse = $dom->find('tbody a');

        foreach ($linkToParse as $e) {
            print_r('start work with:' . trim($e->plaintext) . PHP_EOL);

            $htmlRegularChamp = Http::timeout(30)->get(self::URL_FA13 . $e->href . '/last');
            $domRegularChamp = HtmlDomParser::str_get_html($htmlRegularChamp);

            foreach ($domRegularChamp->find('table[class="alternated-rows-bg wide"] > tr > td[class="teams main"]') as $elGame) {
                //пропускаем первый тег, в котором нет нужной информации
                $th = $elGame->find('th', 0) ? 1 : 0;

                if ($th) {
                    continue;
                }

                foreach ($elGame->find('a') as $item) {
                    sleep(rand(0,1));

                    $htmlCommand = Http::timeout(15)->get(self::URL_FA13 . $item->href);
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
                        //ToDo create request for Guzzle
                        $htmlPage = file_get_contents(self::URL_FA13 . $item->href, false, $context);
                    } catch (\Exception $e) {
                        continue;
                    }
                    $dom = HtmlDomParser::str_get_html($htmlPage);

                    $teamName = $dom->find('div[class="team-header"] > h1', 0)->plaintext;

                    $name = $dom->find('table[class="wide alternated-rows-bg"]', 0)
                        ->find('tr', 1)
                        ->find('a', 0)
                        ->plaintext;

                    $email = explode(':', $dom->find('a[class="m-s-em"]', 0)->href);

                    $userFa13Email = UserFa13Email::where('email', trim($email[1]));
                    $checkEmail = $userFa13Email->exists();

                    if ($checkEmail) {
                        $userFa13Email = $userFa13Email->firstOrFail();
                        $userFa13Email->is_active = 1;
                        $userFa13Email->save();
                    }

                    if(!$checkEmail) {
                        $userFa13Email = new UserFa13Email();
                        $userFa13Email->name = trim($name);
                        $userFa13Email->email = trim($email[1]);
                        $userFa13Email->is_active = 1;
                        $userFa13Email->save();
                    }

                    $checkTeam = Team::where([
                        ['user_fa13_email_id', '=', $userFa13Email->id],
                        ['name', '=', trim($teamName)]
                    ])->exists();

                    if (!$checkTeam) {
                        $team = Team::where('name', trim($teamName))->firstOrFail();
                        $team->user_fa13_email_id = $userFa13Email->id;
                        $team->save();
                    }
                }
            }
        }

    }
}
