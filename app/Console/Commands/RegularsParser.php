<?php

namespace App\Console\Commands;

use App\Models\ExtraTimeScore;
use App\Models\Game;
use App\Models\Season;
use App\Models\Team;
use App\Models\TeamTournament;
use App\Models\TextSource;
use App\Models\Tournament;
use App\Models\VideoSource;
use DateTime;
use Illuminate\Console\Command;
use KubAT\PhpSimple\HtmlDomParser;

class RegularsParser extends Command
{
    const URL_FA13 = 'https://www.fa13.info';
    const SEASON_NUMBER = 40;
    const STATUS_PLAYED = 'played';
    const STATUS_NOT_PLAYED = 'not_played';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:regulars {source_champ : must be schedule(regular) or cup(cup)}';

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

        if (!$season) {
            $season = new Season();
            $season->number = self::SEASON_NUMBER;
            $season->save();
        }

        $html = file_get_contents(self::URL_FA13 . '/tournament/regular');
        $dom = HtmlDomParser::str_get_html($html);

        if ($sourceChamp === 'cup') {
            //пробегаем по tr и выбираем ссылки только с первыми дивизионами
            $linkToParse = [];
            $i = 0;
            foreach ($dom->find('tbody tr') as $item) {
                if ($i === 0) {
                    $i++;
                    continue;
                }
                $linkToParse[] = $item->find('a', 0);
            }
        }

        if ($sourceChamp === 'schedule') {
            $linkToParse = $dom->find('tbody a');
        }

        foreach ($linkToParse as $e) {

            print_r('start work with:' . trim($e->plaintext) . PHP_EOL);

            $htmlRegularChamp = file_get_contents(self::URL_FA13 . $e->href . '/' . $sourceChamp);

            $domRegularChamp = HtmlDomParser::str_get_html($htmlRegularChamp);

            foreach ($domRegularChamp->find('div[class="col col50"]') as $elRegularChamp) {
//                sleep(rand(0,3));
                foreach ($elRegularChamp->find('table[class="alternated-rows-bg wide"] > tr') as $elGame) {
                    //sleep(rand(0,3));
                    $nameRegularChamp = trim($domRegularChamp->find('h2', 0)->plaintext);

                    $tournament = Tournament::where([
                            ['name', $nameRegularChamp],
                            ['status', $kindChamp]
                        ])
                        ->first();

                    if (!$tournament) {
                        $tournament = new Tournament();
                        $tournament->name = $nameRegularChamp;
                        $tournament->status = $kindChamp;
                        $tournament->save();
                    }

                    //пропускаем первый тег, в котором нет нужной информации
                    $th = $elGame->find('th', 0) ? 1 : 0;

                    if ($th) {
                        continue;
                    }

                    $tourDate = explode(',', strip_tags(implode(',', $elRegularChamp->find('h3'))));
                    $tour = trim($tourDate[0]);
                    $date = DateTime::createFromFormat('d.m.Y', trim($tourDate[1]))->format('Y-m-d');

                    // если игры еще не прошли, голы заводим в null, проверяем по количеству зрителей
                    if (trim($elGame->find('.non-mobile', 0)->plaintext) == 0) {
                        $scoreFirst = null;
                        $scoreSecond = null;
                        $scoreFirstExtraTime = null;
                        $scoreSecondExtraTime = null;
                    } else {
                        $score = trim($elGame->find('span', 0)->plaintext);

                        // если в счете отсутствует (пен.4:5), то разделяем по ":"
                        if (!stripos($score, 'н')) {
                            $score = explode(":", $score);
                            $scoreFirst = (int)$score[0];
                            $scoreSecond = (int)$score[1];
                            $scoreFirstExtraTime = null;
                            $scoreSecondExtraTime = null;
                        } else {
                            $mainTimeScore = stristr($score, '(', true);
                            $mainTimeScore = explode(":", $mainTimeScore);

                            $scoreFirst = (int)$mainTimeScore[0];
                            $scoreSecond = (int)$mainTimeScore[1];

                            $extraTimeScore = trim(stristr(trim(ltrim(stristr($score, '.')), '.'), ')', true));

                            $extraTimeScore = explode(":", $extraTimeScore);
                            $scoreFirstExtraTime = (int)$extraTimeScore[0];
                            $scoreSecondExtraTime = (int)$extraTimeScore[1];
                        }
                    }

                    $teamFirst = trim($elGame->find('a', 0)->plaintext);
                    $team1 = Team::where('name', $teamFirst)
                        ->with(['teamTournaments' => function($query) use($tournament){
                            $query->where('tournament_id', $tournament->id)->first();
                        }])
                        ->first();

                    $teamSecond = trim($elGame->find('a', 1)->plaintext);
                    $team2 = Team::where('name', $teamSecond)
                        ->with(['teamTournaments' => function($query) use($tournament){
                            $query->where('tournament_id', $tournament->id)->first();
                        }])
                        ->first();

                    if (isset($team1['teamTournament']) && count($team1['teamTournament']) == 0) {
                        $teamTournament = new TeamTournament();
                        $teamTournament->team_id = $team1->id;
                        $teamTournament->tournament_id = $tournament->id;
                        $teamTournament->season_id = $season->id;
                        $teamTournament->save();
                    }

                    if (isset($team2['teamTournament']) && count($team2['teamTournament']) == 0) {
                        $teamTournament = new TeamTournament();
                        $teamTournament->team_id = $team1->id;
                        $teamTournament->tournament_id = $tournament->id;
                        $teamTournament->season_id = $season->id;
                        $teamTournament->save();
                    }

                    if (!$team1) {
                        $team1 = new Team();
                        $team1->name = $teamFirst;
                        $team1->save();

                        $teamTournament = new TeamTournament();
                        $teamTournament->team_id = $team1->id;
                        $teamTournament->tournament_id = $tournament->id;
                        $teamTournament->season_id = $season->id;
                        $teamTournament->save();
                    }

                    if (!$team2) {
                        $team2 = new Team();
                        $team2->name = $teamSecond;
                        $team2->save();

                        $teamTournament = new TeamTournament();
                        $teamTournament->team_id = $team2->id;
                        $teamTournament->tournament_id = $tournament->id;
                        $teamTournament->season_id = $season->id;
                        $teamTournament->save();
                    }

                    $game = Game::where([
                        ['tour', $tour],
                        ['season_id', $season->id],
                        ['tournament_id', $tournament->id],
                        ['first_team_id', $team1->id],
                        ['second_team_id', $team2->id],
                    ])->first();

                    if (!$game) {
                        $game = new Game();
                        $game->tour = $tour;
                        $game->date = $date;
                        ($scoreFirst !== null) ? $game->status = self::STATUS_PLAYED : $game->status = self::STATUS_NOT_PLAYED;
                        $game->season_id = $season->id;
                        $game->tournament_id = $tournament->id;
                        $game->first_team_id = $team1->id;
                        $game->second_team_id = $team2->id;
                        $game->first_team_score = $scoreFirst;
                        $game->second_team_score = $scoreSecond;
                        $game->save();
                    }

                    if ($scoreFirst !== null && $scoreSecond !== null && $game->status == self::STATUS_NOT_PLAYED) {
                        $game->first_team_score = $scoreFirst;
                        $game->second_team_score = $scoreSecond;
                        $game->save();
                    }

                    if ($scoreFirstExtraTime !== null
                        && $scoreSecondExtraTime !== null
                        && $game->status == self::STATUS_PLAYED
                        && !ExtraTimeScore::where('game_id', $game->id)->exists()
                    ) {
                        $extraTimeScore = new ExtraTimeScore();
                        $extraTimeScore->first_team_score = $scoreFirstExtraTime;
                        $extraTimeScore->second_team_score = $scoreSecondExtraTime;
                        $extraTimeScore->game_id = $game->id;
                        $extraTimeScore->save();
                    }

                    if ($scoreFirst !== null && $scoreSecond !== null) {
                        $videoSource = explode("'", trim($elGame->find('td nobr a', 0)->attr['onclick']))[1];
                        $textSource = 'http:' . trim($elGame->find('td nobr a', 2)->attr['href']);

                        $videoS = VideoSource::where('game_id', $game->id)->first();
                        if (!$videoS) {
                            $videoNewSource = new VideoSource();
                            $videoNewSource->source = $videoSource;
                            $videoNewSource->game_id = $game->id;
                            $videoNewSource->save();
                        }

                        $textS = TextSource::where('game_id', $game->id)->first();
                        if (!$textS) {
                            $textNewSource = new TextSource();
                            $textNewSource->source = $textSource;
                            $textNewSource->game_id = $game->id;
                            $textNewSource->save();
                        }
                    }
                }
            }
        }
    }
}
