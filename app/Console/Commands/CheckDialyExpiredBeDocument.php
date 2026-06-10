<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class CheckDialyExpiredBeDocument extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-dialy-expired-be-document';

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
        $noteExpiredDocuments = DB::select('
        SELECT
            be_documents.`code`, 
            be_documents.article_at, 
            be_documents.article, 
            be_documents.source, 
            be_documents.note, 
            be_document_send_tos.`status`, 
            be_document_send_tos.to_gd, 
            be_document_send_tos.respect_at, 
            be_document_send_tos.send_at, 
            gds.`name` AS gd, 
            departments.`name` AS dept, 
            personels.`name`, 
            personels.position
        FROM
            be_document_send_tos
            INNER JOIN
            be_documents
            ON 
                be_document_send_tos.be_document_id = be_documents.id
            LEFT JOIN
            gds
            ON 
                be_document_send_tos.gd_id = gds.id
            LEFT JOIN
            departments
            ON 
                be_document_send_tos.department_id = departments.id
            LEFT JOIN
            personels
            ON 
                be_document_send_tos.personel_id = personels.id
        WHERE
            be_document_send_tos.`status` = "ត្រូវតាមដាន"
        ');
        foreach ($noteExpiredDocuments as $noteExpiredDocument) {
            $msg = "";
            $msg .= "<b>📕 មានឯកសារ B.E ត្រូវតាមដាន</b>\n";
            $msg .= "-------------------------------------\n";
            $msg .= '<b>' . __('លេខលិខិត') . '៖ </b><code>' . $noteExpiredDocument->code . "</code>\n";
            $msg .= '<b>' . __('កម្មវត្ថុ') . '៖ </b>' . $noteExpiredDocument->article . "\n";
            $msg .= '<b>' . __('កាលបរិច្ឆេទលិខិត') . '៖ </b>' . Carbon::parse($noteExpiredDocument->article_at)->format('d-m-Y') . "\n";
            $msg .= '<b>' . __('ប្រភពមកពី') . '៖ </b>' . $noteExpiredDocument->source . "\n";
            $msg .= "-------------------------------------\n";
            if ($noteExpiredDocument->to_gd) {
                if ($noteExpiredDocument->dept) {
                    $msg .= '<b>' . __('ជូនអង្គភាព ឬស្ថាប័ន') . '៖ </b>' . $noteExpiredDocument->gd;
                    $msg .= '(' . $noteExpiredDocument->dept . ")\n";
                } else {
                    $msg .= '<b>' . __('ជូនអង្គភាព ឬស្ថាប័ន') . '៖ </b>' . $noteExpiredDocument->gd . "\n";
                }
                $msg .= '<b>' . __('កាលបរិច្ឆេទបញ្ជូន​') . '៖ </b>' . Carbon::parse($noteExpiredDocument->send_at)->format('d-m-Y') . "\n";
            } else {
                $msg .= '<b>' . __('ជូន​បុគ្គល') . '៖ </b>' . $noteExpiredDocument->name . "\n";
            }
            $msg .= '<b>' . __('កាលបរិច្ឆេទបញ្ជូន​') . '៖ </b>' . Carbon::parse($noteExpiredDocument->send_at)->format('d-m-Y') . "\n";
            $msg .= '<b>' . __('កាលបរិច្ឆេទត្រលប់​') . '៖ </b>' . Carbon::parse($noteExpiredDocument->respect_at)->format('d-m-Y') . "\n";
            $msg .= "-------------------------------------\n";
            Http::withoutVerifying()->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $msg,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => false,
            ]);
            Http::withoutVerifying()->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $msg,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => false,
            ]);
        }


        return Command::SUCCESS;
    }
}
