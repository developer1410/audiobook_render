<?php

/**
 * Class AudioBookRender
 */
class AudioBookRenderer
{
    protected string $fileName;
    protected float $chapterSilent;
    protected float $maxChapterTime;
    protected float $longChapterSilent;

    /**
     * AudioBookRender constructor.
     * @param string $fileName
     * @param float $chapterSilent
     * @param float $maxChapterTime
     * @param float $longChapterSilent
     * @throws Exception
     */
    public function __construct(string $fileName, float $chapterSilent, float $maxChapterTime, float $longChapterSilent)
    {
        if (!file_exists($fileName)) {
            throw new \Exception("File: $fileName does not exists");
        }
        $this->maxChapterTime = $maxChapterTime;
        $this->fileName = $fileName;
        if ($chapterSilent <= $longChapterSilent) {
            throw new \Exception('Silence duration of long chapter (lcs) is bigger than chapter silent duration (cs)');
        }
        $this->chapterSilent = $chapterSilent;
        $this->longChapterSilent = $longChapterSilent;
    }

    /**
     * Render audio book on chapters
     */
    public function render()
    {
        $xmlRender = new XMLFileReader($this->fileName);

        $chapter = 1;
        $data = [
            [
                'title' => 'Chapter ' . $chapter,
                'silent' => 0,
                'offset' => 'PT0S'
            ]
        ];
        // Previous chapter time durations history
        $prevDurations = [];
        $prevChapterSeconds = 0;
        $array = $xmlRender->toArray();
        $lastIndex = count($array) - 1;
        foreach($array as $index => $item) {
            // Separates on chapters
            $fromSeconds = $this->getTimeInSeconds($item['from']);
            $toSeconds = $this->getTimeInSeconds($item['until']);
            $silentTime = $toSeconds - $fromSeconds;
            if ($silentTime >= $this->chapterSilent || $lastIndex == $index) {
                // Check how long is Chapter
                if ($fromSeconds - $prevChapterSeconds > $this->maxChapterTime) {
                    // Remove previous chapter to separate it for parts
                    $lastElement = array_pop($data);
                    // Separate previous chapter for parts
                    $part = 1;
                    $data[] = [
                        'title' => 'Chapter ' . $chapter . ', Part ' . $part,
                        'silent' => $lastElement['silent'],
                        'offset' => $lastElement['offset']
                    ];
                    // Separate on Parts
                    foreach ($prevDurations as $duration) {
                        $silentTimePart = $duration['to'] - $duration['from'];
                        if ($silentTimePart >= $this->longChapterSilent) {
                            $data[] = [
                                'title' => 'Chapter ' . $chapter . ', Part ' . (++$part),
                                'silent' => $silentTimePart,
                                'offset' => $duration['offset']
                            ];
                        }
                    }
                }

                if ($lastIndex != $index) {
                    $chapter += 1;
                    $data[] = [
                        'title' => 'Chapter ' . $chapter,
                        'silent' => $silentTime,
                        'offset' => $item['from']
                    ];
                    $prevChapterSeconds = $fromSeconds;
                }

                // Remove history
                $prevDurations = [];
            } else {
                $prevDurations[] = [
                    'from' => $fromSeconds,
                    'to' => $toSeconds,
                    'offset' => $item['from']
                ];
            }
        }

        return $data;
    }

    /**
     * @param string $time
     * @return float|int
     * @throws Exception
     */
    protected function getTimeInSeconds(string $time) {
        $d = new DateInterval(preg_replace('/(\.[\d]+)/', '', $time));
        preg_match('/\.[\d]+/', $time, $microseconds);
        $microseconds = !$microseconds ? 0 : (float)$microseconds[0];
        return $d->s + ($d->i * 60) + ($d->h * 3600) + ($d->d * 86400) + $microseconds;
    }
}