<?php

namespace App\Console\Commands;

use App\Services\TranscodingManager;
use Illuminate\Console\Command;

class CheckTranscodingMethods extends Command
{
    protected $signature = 'video:check-methods';
    protected $description = 'Check available video transcoding methods';

    public function handle(TranscodingManager $transcodingManager)
    {
        $this->info('Checking available video transcoding methods...');
        $this->newLine();

        $methods = $transcodingManager->getAvailableMethods();
        $currentMethod = env('VIDEO_TRANSCODING_METHOD', 'simple');

        $this->table(
            ['Method', 'Status', 'Description'],
            [
                ['ffmpeg', in_array('ffmpeg', array_keys($methods)) ? '✅ Available' : '❌ Not Available', 'Local FFmpeg processing'],
                ['external', in_array('external', array_keys($methods)) ? '✅ Available' : '❌ Not Available', 'External processing server'],
                ['client', in_array('client', array_keys($methods)) ? '✅ Available' : '❌ Not Available', 'Client-side processing'],
                ['simple', in_array('simple', array_keys($methods)) ? '✅ Available' : '❌ Not Available', 'Simple MP4 streaming'],
            ]
        );

        $this->newLine();
        $this->info("Current method: <fg=green>{$currentMethod}</>");
        
        if ($currentMethod === 'ffmpeg' && !in_array('ffmpeg', array_keys($methods))) {
            $this->warn('⚠️  FFmpeg is not available but is set as the current method!');
            $this->info('Consider switching to "simple" method for hosting without FFmpeg.');
        }

        $this->newLine();
        $this->info('To change the method, update VIDEO_TRANSCODING_METHOD in your .env file');
    }
}
