<?php

declare(strict_types=1);

namespace Draagvlak\Features\Scenario;

use Draagvlak\Core\Data\Database;
use Draagvlak\Core\Data\JsonStore;
use Draagvlak\Features\Contacts\ContactRepository;
use Draagvlak\Features\Score\ScoreRules;

/**
 * The starting position of the scenario, written into one account.
 *
 * Only the demo account gets this. Somebody who registers starts with an empty
 * list and a code to share, the way an app works; the scenario content exists
 * so a demo of the home screen has something to show.
 *
 * The people and their messages are Dutch, so they live in data/scenario.json.
 */
final readonly class Scenario
{
    public function __construct(
        private Database $database,
        private JsonStore $json,
        private ContactRepository $contacts,
    ) {}

    /**
     * Does nothing when the account already has contacts, so running it twice
     * is safe, and nothing when the file holds none.
     *
     * The shape it expects:
     *
     *     {"contacts": [{"name": "...", "relation": "...", "score": 61,
     *       "isEmergencyContact": true, "listsYou": true, "note": "...",
     *       "message": {"body": "...", "waitSeconds": 0, "respondWithinSeconds": 15}}]}
     *
     * @return int How many contacts were added.
     */
    public function seedFor(int $userId): int
    {
        if ($this->contacts->activeCount($userId) > 0) {
            return 0;
        }

        $scenario = $this->json->load('scenario');
        $contacts = \is_array($scenario['contacts'] ?? null) ? $scenario['contacts'] : [];

        foreach ($contacts as $contact) {
            $this->addContact($userId, $contact);
        }

        return \count($contacts);
    }

    /** @param array<string, mixed> $contact One entry of the contacts list. */
    private function addContact(int $userId, array $contact): void
    {
        $contactId = $this->database->insert('contacts', [
            'user_id' => $userId,
            'name' => (string) $contact['name'],
            'relation' => (string) ($contact['relation'] ?? ''),
            'score' => (int) ($contact['score'] ?? ScoreRules::CONTACT_THRESHOLD),
            'is_emergency_contact' => (int) (bool) ($contact['isEmergencyContact'] ?? false),
            'lists_you' => (int) (bool) ($contact['listsYou'] ?? true),
            'note' => (string) ($contact['note'] ?? ''),
        ]);

        if (!\is_array($contact['message'] ?? null)) {
            return;
        }

        $message = $contact['message'];

        $this->database->insert('messages', [
            'user_id' => $userId,
            'contact_id' => $contactId,
            'body' => (string) $message['body'],
            'received_at' => date('Y-m-d H:i:s', time() - (int) ($message['waitSeconds'] ?? 0)),
            'respond_within_seconds' => (int) ($message['respondWithinSeconds'] ?? 0),
        ]);
    }
}
