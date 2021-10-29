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
                'О Don\'ах Pedro\'растах, его email: petrd10@list.ru',
                'Хочу рассказать вам историю от 2020 года. В Белоруси начались протесты против кровавого диктатора
                Лукашенко. Ну и как вы догадались, многие менеджеры из Белоруси подписывали свои пресухи Жыве Белорусь.
                Ну подписывают и подписывают, в переводе с белоруского языка, переводится как Живет Белорусь. Что - то в
                фразе живет Белорусь не понравилось администрации и они без никаких предупреждений, впаяли огромнейшие штрафы
                менеджерам, которые ставили под пресухами фразу Живет Белорусь. У них вышла ситуация аналогичная моей.
                И администрация выгнала людей, которые из чемпионата Белоруси выводили команды в Лигу Чемпионов фа13.
                Если вы вдруг только начали играть в фа13 или вам фиолетов на эту фа13 бросайте этот очередной развод на
                ваше время и энергию
                P.S. Я уже активно тестирую систему фа13 против мультиводов, как только я пойму, что все работает, всем в
                письме опишу, о дырка в их системе противмультиводов и если у вас есть "грязные" компьютеры, используйте их.
                С уважение, менеджер Вёргла , Эндрю',

                'From: admin@fa13-prognosis.com.ua'))
            {
                echo $email->email . PHP_EOL;
            } else {
                echo "some error happen";
            }
        }
    }
}
