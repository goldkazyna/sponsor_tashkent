<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ApplyAiTitles extends Command
{
    protected $signature = 'posts:apply-ai';
    protected $description = 'Переносит title_ai → title, discription_ai → discription для проверенных объявлений (check=1)';

    public function handle(): int
    {
        $posts = DB::table('post')
            ->where('check', 1)
            ->whereNotNull('title_ai')
            ->where('title_ai', '!=', '')
            ->get(['id', 'title', 'title_ai', 'discription', 'discription_ai']);

        if ($posts->isEmpty()) {
            $this->info('Нет объявлений для обновления.');
            return 0;
        }

        $this->info("Найдено объявлений: {$posts->count()}");

        foreach ($posts as $post) {
            DB::table('post')->where('id', $post->id)->update([
                'title' => $post->title_ai,
                'discription' => $post->discription_ai ?? '',
                'title_ai' => '',
                'discription_ai' => '',
            ]);

            $this->line("  #{$post->id}: «{$post->title}» → «{$post->title_ai}»");
        }

        $this->info('Готово!');
        return 0;
    }
}
