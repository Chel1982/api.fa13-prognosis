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
                'Новый проект от комьюнити fa13',
                'Доброго времени суток, уважаемый менеджер ' . $email->name
                . '. Хочу вам показать проект от сообщества fa13, в котором вы сможете
                обсуждать матчи всех чемпионатов (реализованы чаты на все турниры
                и общий чат, просьба обновлять, через перезагрузку страницы),
                читать все пресс-конференции(время обновления с 4 утра до 22 вечера).
                Поскольку проект является не коммерческим, разработка проекта ведется
                мной в свободное от работы и досуга время.
                При регистарации используйте тот же email, что и при регистрации в fa13,
                тогда вам будут приходить уведомления, если кто-то на ваш матч оставли комментарий.
                Прошу вас пожаловать на сайт http://fa13-prognosis.com.ua/ .
                С уважнием, Эндрю',
                'From: admin@fa13-prognosis.com.ua'))
            {
                echo $email->email . PHP_EOL;
            } else {
                echo "some error happen";
            }
        }
    }
}
