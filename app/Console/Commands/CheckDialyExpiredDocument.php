<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
class CheckDialyExpiredDocument extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-dialy-expired-document';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $botToken = config('env.TELEGRAM_BOT_TOKEN');
        $chatId = config('env.CHANEL_DS_DOCTRACKING');
         Http::withoutVerifying()->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => "Test Cron Job In laravel",
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => false,
        ]);
        return Command::SUCCESS;
    }
}
