<?php

namespace App\Console\Commands;

use App\Models\Game;
use App\Models\PressConference;
use App\Models\Season;
use App\Models\Team;
use App\Models\Tournament;
use DateTime;
use Illuminate\Console\Command;
use KubAT\PhpSimple\HtmlDomParser;

class PressesParser extends Command
{
    const URL_FA13 = 'https://www.fa13.info';
    const SEASON_NUMBER = 40;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:presses {source_champ : must be schedule(regular) or cup(cup)}';

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
        $sourceChamp = $this->argument('source_champ');

        switch ($sourceChamp) {
            case 'schedule':
                $kindChamp = 'regular';
                break;
            case 'cup':
                $kindChamp = 'cup';
                break;
            default:
                die('wrong parameter source_champ' . PHP_EOL);
        }

        $season = Season::where('number', self::SEASON_NUMBER)->first();

        if ($season === null) {
            $season = new Season();
            $season->number = self::SEASON_NUMBER;
            $season->save();
        }

        $html = file_get_contents(self::URL_FA13 . '/tournament/regular');
        $dom = HtmlDomParser::str_get_html($html);

        foreach ($dom->find('tbody a') as $e) {

            print_r('start work with:' . trim($e->plaintext) . PHP_EOL);

            $htmlRegularChamp = file_get_contents(self::URL_FA13 . $e->href . '/' . $sourceChamp);

            $domRegularChamp = HtmlDomParser::str_get_html($htmlRegularChamp);

            $nameRegularChamp = trim($domRegularChamp->find('h2', 0)->plaintext);

            $tournament = Tournament::where([
                    ['name', $nameRegularChamp],
                    ['status', $kindChamp]
                ])
                ->first();

            if (!$tournament) {
                continue;
            }

            foreach ($domRegularChamp->find('div[class="col col50"]') as $elRegularChamp) {
//                sleep(rand(0,3));
                foreach ($elRegularChamp->find('table[class="alternated-rows-bg wide"] > tr') as $elGame) {

//                    sleep(rand(0,3));

                    $th = $elGame->find('th', 0) ? 1 : 0;

                    $pressBool = $elGame->find('.l-g-press-release-3', 0) ? 1 : 0;

                    if ($th || $pressBool) {
                        continue;
                    }

                    if (trim($elGame->find('.non-mobile', 0)->plaintext) == 0) {
                        continue 2;
                    }

                    $tourDate = explode(',', strip_tags(implode(',', $elRegularChamp->find('h3'))));
                    $tour = trim($tourDate[0]);

                    $teamFirst = trim($elGame->find('a', 0)->plaintext);
                    $team1 = Team::where('name', $teamFirst)->first();

                    $teamSecond = trim($elGame->find('a', 1)->plaintext);
                    $team2 = Team::where('name', $teamSecond)->first();

                    $game = Game::where([
                            ['tour', $tour],
                            ['season_id', $season->id],
                            ['tournament_id', $tournament->id],
                            ['first_team_id', $team1->id],
                            ['second_team_id', $team2->id],
                        ])
                        ->first();

//                    file_put_contents('1.txt', $game); die();

                    if ($game === null) {
                        continue;
                    }

                    $press = PressConference::where('game_id', $game->id)->first();

                    $pressFirstManger = $elGame->find('.l-g-press-release-1', 0) ? 1 : 0;
                    $pressSecondManger = $elGame->find('.l-g-press-release-2', 0) ? 1 : 0;
                    $pressMangers = $elGame->find('.l-g-press-release', 0) ? 1 : 0;

                    // проверяем уже созданные комментарии, если они есть, пропускаем
                    if ($pressFirstManger && isset($press->first_team_id)) {
                        continue;
                    }

                    if ($pressSecondManger && isset($press->second_team_id)) {
                        continue;
                    }

                    if ($pressMangers && isset($press->second_team_id) && isset($press->second_team_id)) {
                        continue;
                    }

                    $hrefPress = trim($elGame->find('nobr > a', 3)->href);
                    $pressHtml = file_get_contents(self::URL_FA13 . $hrefPress);
                    $pressDom = HtmlDomParser::str_get_html($pressHtml);

                    //для первого или второго менедежера
                    $date = trim($pressDom->find('.press-release time', 0)->innertext);
                    $date = DateTime::createFromFormat('H:i d.m.Y', $date)->format('Y-m-d H:i:s');

                    $pressConference = trim($pressDom->find('.press-release', 0)->innertext);

                    // проверяем, когда 2 менеджера оставили комментарии и обновляем, если созданы
                    if ($pressFirstManger) {

                        $firstPressManager = PressConference::where([
                                ['first_team_id', $game->first_team_id],
                                ['game_id', $game->id],
                            ])
                            ->first();

                        if (!$firstPressManager) {
                            $press = new PressConference();
                            $press->first_team_id = $game->first_team_id;
                            $press->game_id = $game->id;
                            $press->press_conference = $pressConference;
                            $press->date = $date;
                            $press->save();
                        } else {
                            // если вдруг менеджер дополнил комментарий, то дополняем его
                            $firstPressManager->press_conference = $pressConference;
                            $firstPressManager->date = $date;
                            $firstPressManager->save();
                        }
                    }

                    if ($pressSecondManger) {
                        $secondPressManager = PressConference::where([
                                ['second_team_id', $game->second_team_id],
                                ['game_id', $game->id],
                            ])
                            ->first();

                        if (!$secondPressManager) {
                            $press = new PressConference();
                            $press->second_team_id = $game->second_team_id;
                            $press->game_id = $game->id;
                            $press->press_conference = $pressConference;
                            $press->date = $date;
                            $press->save();
                        } else {
                            $secondPressManager->press_conference = $pressConference;
                            $secondPressManager->date = $date;
                            $secondPressManager->save();
                        }
                    }

                    if ($pressMangers) {
                        // когда пришли оба менеджера, надо дополнить прессуху 2го менеджера
                        $dateBoth = trim($pressDom->find('.press-release time', 1)->innertext);
                        $dateBoth = DateTime::createFromFormat('H:i d.m.Y', $dateBoth)->format('Y-m-d H:i:s');

                        $pressConferenceBoth = trim($pressDom->find('.press-release', 1)->innertext);

                        $firstPressManager = PressConference::where([
                                ['first_team_id', $game->first_team_id],
                                ['game_id', $game->id],
                            ])
                            ->first();

                        if (!$firstPressManager) {
                            $press = new PressConference();
                            $press->first_team_id = $game->first_team_id;
                            $press->game_id = $game->id;
                            $press->press_conference = $pressConference;
                            $press->date = $date;
                            $press->save();
                        } else {
                            $firstPressManager->press_conference = $pressConference;
                            $firstPressManager->date = $date;
                            $firstPressManager->save();
                        }

                        $secondPressManager = PressConference::where([
                                ['second_team_id', $game->second_team_id],
                                ['game_id', $game->id],
                            ])
                            ->first();

                        if (!$secondPressManager) {
                            $press = new PressConference();
                            $press->second_team_id = $game->second_team_id;
                            $press->game_id = $game->id;
                            $press->press_conference = $pressConferenceBoth;
                            $press->date = $dateBoth;
                            $press->save();
                        } else {
                            $secondPressManager->press_conference = $pressConferenceBoth;
                            $secondPressManager->date = $dateBoth;
                            $secondPressManager->save();
                        }
                    }
                }
            }
        }
    }
}
