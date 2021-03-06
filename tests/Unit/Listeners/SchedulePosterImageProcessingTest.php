<?php

namespace Tests\Unit\Listeners;

use App\ConcertFactory;
use App\Events\ConcertAdded;
use App\Jobs\ProcessPosterImage;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SchedulePosterImageProcessingTest extends TestCase
{
    /** @test */
    function it_queues_a_job_to_process_a_poser_image_if_a_poster_image_is_present()
    {
        Queue::fake();

        $concert = ConcertFactory::createPublished([
            'poster_image_path' => 'poster/example-poster-image.png',
        ]);

        ConcertAdded::dispatch($concert);

        Queue::assertPushed(ProcessPosterImage::class, function ($job) use ($concert) {
            return $job->concert->is($concert);
        });
    }

    /** @test */
    function a_job_is_not_queued_if_a_poster_image_is_not_present()
    {
        Queue::fake();

        $concert = ConcertFactory::createPublished([
            'poster_image_path' => null,
        ]);

        ConcertAdded::dispatch($concert);

        Queue::assertNotPushed(ProcessPosterImage::class, function ($job) use ($concert) {
            return $job->concert->is($concert);
        });
    }
}
