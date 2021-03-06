<?php

namespace App\Services\Threads;

use App\Models\Thread;
use App\Repositories\ParticipantRepository;
use App\Repositories\ThreadRepository;
use Illuminate\Support\Facades\DB;

class CreateThreadService
{
    /**
     * @var ThreadRepository
     */
    private ThreadRepository $threadRepository;
    /**
     * @var ParticipantRepository
     */
    private ParticipantRepository $participantRepository;

    public function __construct(ThreadRepository $threadRepository, ParticipantRepository $participantRepository)
    {
        $this->threadRepository = $threadRepository;
        $this->participantRepository = $participantRepository;
    }

    public function __invoke(array $participants, string $description, string $title): Thread
    {
        debugbar()->log('Calling CreateThreadService...');

        $thread = null;

        DB::transaction(function () use ($participants, $title, $description, &$thread) {
            debugbar()->log('Creating the thread...');
            /** @var Thread $thread */
            $thread = $this->threadRepository->create([
                'title' => $title,
                'description' => $description,
            ]);
            foreach ($participants as $participant) {
                debugbar()->log('Creating Participant with uuid', $participant);
                $this->participantRepository->create([
                    'thread_id' => $thread->getKey(),
                    'user_id' => $participant,
                ]);
            }
        });

        return $thread;
    }
}
