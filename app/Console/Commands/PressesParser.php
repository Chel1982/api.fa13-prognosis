<?php

namespace App\Console\Commands;

use App\Models\Game;
use App\Models\PressConference;
use App\Models\Season;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Console\Command;
use KubAT\PhpSimple\HtmlDomParser;

class PressesParser extends Command
{
    const URL_FA13 = 'https://www.fa13.info';
    const SEASON_NUMBER = 40;
    const KIND_CHAMP = 'regular';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:presses';

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

            $tournament = Tournament::where([
                ['name', trim($e->plaintext)],
                ['status', self::KIND_CHAMP]
            ])
                ->first();

            if ($tournament === null) {
                $tournament = new Tournament();
                $tournament->name = trim($e->plaintext);
                $tournament->status = self::KIND_CHAMP;
                $tournament->save();
            }

            $htmlRegularChamp = file_get_contents(self::URL_FA13 . $e->href . '/schedule');

            $domRegularChamp = HtmlDomParser::str_get_html($htmlRegularChamp);

            foreach ($domRegularChamp->find('div[class="col col50"]') as $elRegularChamp) {
//                sleep(rand(0,3));
                foreach ($elRegularChamp->find('table[class="alternated-rows-bg wide"] > tr') as $elGame) {sleep(rand(0,3));
                    $th = $elGame->find('th', 0) ? 1 : 0;

                    $pressBool = $elGame->find('.l-g-press-release-3', 0) ? 1 : 0;

                    if ($th || $pressBool) {
                        continue;
                    }

                    if (trim($elGame->find('.non-mobile', 0)->plaintext) == 0) {
                        continue 2;
                    }

                    $tourDate = strip_tags(implode(',', $elRegularChamp->find('h3')));
                    $tour = substr($tourDate, 7, 1);

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

                    if ($game == null) {
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

                    // проверяем, когда 2 менеджера оставили комментарии и обновляем, если созданы
                    if ($pressFirstManger) {

                        $firstPressManager = PressConference::where([
                            ['first_team_id', $game->first_team_id],
                            ['game_id', $game->id],
                        ])
                            ->first();

                        if ($firstPressManager === null) {
                            $press = new PressConference();
                            $press->first_team_id = $game->first_team_id;
                            $press->game_id = $game->id;
                            $press->press_conference = trim($pressDom->find('.press-release', 0)->innertext);
                            $press->save();
                        } else {
                            $firstPressManager->comment = trim($pressDom->find('.press-release', 0)->innertext);
                            $firstPressManager->save();
                        }
                    }

                    if ($pressSecondManger) {

                        $secondPressManager = PressConference::where([
                            ['second_team_id', $game->second_team_id],
                            ['game_id', $game->id],
                        ])
                            ->first();

                        if ($secondPressManager === null) {
                            $press = new PressConference();
                            $press->second_team_id = $game->second_team_id;
                            $press->game_id = $game->id;
                            $press->press_conference = trim($pressDom->find('.press-release', 0)->innertext);
                            $press->save();
                        } else {
                            $secondPressManager->comment = trim($pressDom->find('.press-release', 0)->innertext);
                            $secondPressManager->save();
                        }
                    }

                    if ($pressMangers) {
                        $firstPressManager = PressConference::where([
                            ['first_team_id', $game->first_team_id],
                            ['game_id', $game->id],
                        ])
                            ->first();

                        if ($firstPressManager === null) {
                            $press = new PressConference();
                            $press->first_team_id = $game->first_team_id;
                            $press->game_id = $game->id;
                            $press->press_conference = trim($pressDom->find('.press-release', 0)->innertext);
                            $press->save();
                        } else {
                            $firstPressManager->comment = trim($pressDom->find('.press-release', 0)->innertext);
                            $firstPressManager->save();
                        }

                        $secondPressManager = PressConference::where([
                            ['second_team_id', $game->second_team_id],
                            ['game_id', $game->id],
                        ])
                            ->first();

                        if ($secondPressManager === null) {
                            $press = new PressConference();
                            $press->second_team_id = $game->second_team_id;
                            $press->game_id = $game->id;
                            $press->press_conference = trim($pressDom->find('.press-release', 1)->innertext);
                            $press->save();
                        } else {
                            $secondPressManager->comment = trim($pressDom->find('.press-release', 1)->innertext);
                            $secondPressManager->save();
                        }
                    }
                }
            }
            print_r('ended work with:' . trim($e->plaintext) . PHP_EOL);
        }
    }
}