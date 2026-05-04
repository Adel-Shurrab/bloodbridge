<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class PublishAchievementIcons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'achievements:publish-icons';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish raw Arabic-named achievement icons to the public storage disk with English names.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $map = [
            "الأسطورة.png" => "legend.png",
            "الاستجابة السریعة.png" => "quick-response.png",
            "البطل المحلي.png" => "local-hero.png",
            "الجار الكريم.png" => "generous-neighbor.png",
            "الدقيق.png" => "the-precise.png",
            "الرد الفوري.png" => "immediate-reply.png",
            "الشھر المنتظم.png" => "regular-month.png",
            "العابر للحدود.png" => "cross-border.png",
            "العامان المتواصلان.png" => "two-continuous-years.png",
            "الفصیلة النادرة.png" => "rare-blood-type.png",
            "الكمالیة.png" => "perfection.png",
            "الكنز الثمین.png" => "precious-treasure.png",
            "المائة الذھبیة.png" => "golden-hundred.png",
            "المسافر الطویل.png" => "long-traveler.png",
            "المسافر.png" => "traveler.png",
            "المساھم.png" => "contributor.png",
            "المستقبل العالمي.png" => "global-future.png",
            "الملتزم.png" => "committed.png",
            "المنضبط.png" => "disciplined.png",
            "المنقذ العالمي.png" => "global-savior.png",
            "المنقذ.png" => "savior.png",
            "الموثوق.png" => "reliable.png",
            "بطل الطوار ئ.png" => "emergency-hero.png",
            "بطل رمضان.png" => "ramadan-hero.png",
            "جولة القطاع.png" => "sector-tour.png",
            "دائما جاھز.png" => "always-ready.png",
            "ربع السنة الملتزم.png" => "quarter-year-committed.png",
            "صاعقة البرق.png" => "lightning-bolt.png",
            "عام من العطاء.png" => "year-of-giving.png",
            "قطرة الحياة الاولى.png" => "first-drop.png",
            "لا إلغاء.png" => "no-cancellation.png",
            "نصف العام الثابت.png" => "steady-half-year.png",
            "یوم المتبرعل العالمي.png" => "world-donor-day.png"
        ];

        $srcDir = resource_path('images/raw_achievements/');
        $dstDisk = Storage::disk('public');
        
        if (!File::isDirectory($srcDir)) {
            $this->error("Source directory not found: {$srcDir}");
            return Command::FAILURE;
        }

        $this->info("Publishing achievement icons...");

        $successCount = 0;
        $failCount = 0;

        foreach ($map as $src => $dst) {
            $srcPath = $srcDir . $src;
            $dstPath = 'achievements/' . $dst;

            if (File::exists($srcPath)) {
                $dstDisk->put($dstPath, File::get($srcPath));
                $this->line("Copied <info>{$src}</info> => <comment>{$dstPath}</comment>");
                $successCount++;
            } else {
                $this->error("File not found: {$srcPath}");
                $failCount++;
            }
        }

        $this->info("Done! Published: {$successCount}, Failed: {$failCount}.");
        return Command::SUCCESS;
    }
}
