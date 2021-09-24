<?php

namespace App\Console\Commands;

use App\Models\UserFa13Email;
use Illuminate\Console\Command;

class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sendEmails';

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
        $emails = UserFa13Email::all();

        foreach ($emails as $email) {
            sleep(rand(3,7));
            if (mail(
                $email->email,
                'Don Pedro, такой Don Pedro, его email: petrd10@list.ru',
                'Никакой реакции на мою массовую рассылку писем менеджерам fa13 не последовало!!!
                Хотя уже надо задуматься, популярности проекту fa13 явно не прибавит...
                А требуется, всего-навсего выгнать Don Pedro, вернуть мне клуб Вёргл и сразу будет видно,
                что у вас изменилось отношении к комьюнити fa13.
                Ок, ребята, не хотите по-хорошему, будет по-плохому. Напомню вам, вы связались с web-программистом
                с 10 летним стажем работы.
                Я всегда свой ум и энергию направлял на создание продукта, но тут придется направить всю энергию на
                уничтожение проекта fa13.
                Что будет дальше?
                1. Массовые рассылки писем менеджерам проекта продолжится, можете забыть о развитии проекта.
                2. Я взломаю вашу систему защиты от "мультиводов" и сделаю массовую рассылку, как устроена ваша защита,
                как она работает, и как её обойти, так же укажу свои клубы твинки. И да, я уже примерно представляю,
                как ваша система безопасности устроена и как её обойти.
                Соответственно, "мультиводов" у вас будет полно и договорняков аналогично, а в других
                виртуальных футбольных менеджеров их не будет.
                P.S. Если вдруг решили посмеяться над моим письмом, скоро будем смеяться вместе, вы над убитым проектом,
                я над вами. И да, вы получаете деньги от google рекламы? Этого тоже скоро не будет :)',
                'From: admin@fa13-prognosis.com.ua'))
            {
                echo $email->email . PHP_EOL;
            } else {
                echo "some error happen";
            }
        }
    }
}
