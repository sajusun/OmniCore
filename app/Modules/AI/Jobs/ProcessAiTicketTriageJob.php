<?php

namespace App\Modules\AI\Jobs;

use App\Modules\AI\Services\AiService;
use App\Modules\Ticket\Enums\TicketPriority;
use App\Modules\Ticket\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessAiTicketTriageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $backoff = 5;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Ticket $ticket
    ) {}

    /**
     * Execute the job.
     */
    public function handle(AiService $aiService): void
    {
        try {
            $triage = $aiService->triageTicket($this->ticket);

            if (! empty($triage['priority'])) {
                $priorityEnum = match (strtolower($triage['priority'])) {
                    'critical', 'urgent' => TicketPriority::CRITICAL ?? TicketPriority::HIGH,
                    'high' => TicketPriority::HIGH,
                    'medium' => TicketPriority::MEDIUM,
                    'low' => TicketPriority::LOW,
                    default => $this->ticket->priority,
                };

                $this->ticket->update([
                    'priority' => $priorityEnum,
                ]);

                Log::info('AI Ticket Triage completed asynchronously', [
                    'ticket_id' => $this->ticket->id,
                    'predicted_priority' => $triage['priority'],
                    'confidence' => $triage['confidence'] ?? null,
                    'sentiment' => $triage['sentiment'] ?? null,
                ]);
            }
        } catch (Throwable $e) {
            Log::error('ProcessAiTicketTriageJob error: '.$e->getMessage(), [
                'ticket_id' => $this->ticket->id,
            ]);
            throw $e;
        }
    }
}
