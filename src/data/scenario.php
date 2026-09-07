<?php

declare(strict_types=1);

/**
 * Giving a new account the starting position of the scenario.
 *
 * The people and their messages are Dutch, so they live in data/scenario.json
 * and not in this file. Change that JSON between two sessions and the next
 * participant gets the new version, without touching any code.
 *
 * Every participant starts from the same position, otherwise two sessions
 * cannot be compared.
 */

/**
 * Write the contacts and messages from data/scenario.json for one account.
 *
 * Does nothing when the account already has contacts, so running it twice is
 * safe, and nothing when the file holds no contacts, so an empty scenario file
 * simply means an empty inbox instead of an error.
 *
 * The shape it expects:
 *
 *     {"contacts": [{"name": "...", "relation": "...", "score": 61,
 *       "isEmergencyContact": true, "listsYou": true, "note": "...",
 *       "message": {"body": "...", "waitSeconds": 0, "respondWithinSeconds": 15}}]}
 *
 * @return int How many contacts were added.
 */
function seedScenarioFor(int $userId): int
{
    if (activeContactCount($userId) > 0) {
        return 0;
    }

    $scenario = loadJson('scenario');
    $contacts = is_array($scenario['contacts'] ?? null) ? $scenario['contacts'] : [];

    foreach ($contacts as $contact) {
        $contactId = dbInsert('contacts', [
            'user_id' => $userId,
            'name' => (string) $contact['name'],
            'relation' => (string) ($contact['relation'] ?? ''),
            'score' => (int) ($contact['score'] ?? CONTACT_THRESHOLD),
            'is_emergency_contact' => (int) (bool) ($contact['isEmergencyContact'] ?? false),
            'lists_you' => (int) (bool) ($contact['listsYou'] ?? true),
            'note' => (string) ($contact['note'] ?? ''),
        ]);

        if (!isset($contact['message'])) {
            continue;
        }

        $message = $contact['message'];

        dbInsert('messages', [
            'user_id' => $userId,
            'contact_id' => $contactId,
            'body' => (string) $message['body'],
            'received_at' => date('Y-m-d H:i:s', time() - (int) ($message['waitSeconds'] ?? 0)),
            'respond_within_seconds' => (int) ($message['respondWithinSeconds'] ?? 0),
        ]);
    }

    return count($contacts);
}
