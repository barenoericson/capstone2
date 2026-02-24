# Messaging Enhancement Plan: Real-Time Features + UI/UX Upgrade

## Current State Analysis

**What already works:**
- Real-time messaging via Laravel Reverb + Echo (MessageSent event broadcasts)
- Typing indicator via whisper on presence conversation channels
- Online/Offline status via presence channels (conversation-level + global)
- `is_read` + `read_at` fields exist in the messages table
- `markConversationRead()` backend method exists
- Optimistic message sending with avatar-based Messenger-style layout
- File attachments support
- Profile photos on both sides

**What's missing/needs enhancement:**
1. **"Seen" indicator** — messages show `is_read` in DB but the UI doesn't show read receipts (no checkmarks/seen status)
2. **Real-time "Seen" broadcast** — when user reads messages, sender isn't notified in real-time
3. **"Last seen" / offline duration** — no `last_seen_at` column on users table, UI just shows "Offline" with no time info
4. **UI/UX polish** — the current design is functional but can be enhanced with better visual hierarchy, animations, and modern chat feel

---

## Implementation Plan

### Step 1: Database — Add `last_seen_at` to users table
- Create migration: `add_last_seen_at_to_users_table`
- Add `$table->timestamp('last_seen_at')->nullable()` column
- Add `last_seen_at` to User model `$fillable` and `$casts`
- Run migration

### Step 2: Backend — Heartbeat endpoint to track online status
- Add `POST /api/user/heartbeat` route (inside auth:sanctum)
- Controller method updates `auth()->user()->last_seen_at = now()`
- Return the current timestamp
- The `getConversation()` method already returns `otherUser` — add `last_seen_at` to that response

### Step 3: Backend — Real-time "Seen" event
- Create `MessageRead` event (ShouldBroadcastNow)
  - Broadcasts on `PrivateChannel('chat.' . $senderId)` to notify the sender
  - Payload: `{ reader_id, conversation_with, read_at }`
- Update `markConversationRead()` to dispatch `MessageRead` event after marking messages read
- This way, when buyer opens the chat → marks as read → agent gets a real-time "Seen" notification

### Step 4: Frontend — Heartbeat + Last Seen Display
- In `MessageConversation.vue` and `ConversationsList.vue`:
  - Send heartbeat every 30 seconds via `setInterval`
  - When user is offline, show "Last seen X minutes/hours ago" instead of just "Offline"
  - Format: "Active 5m ago", "Active 2h ago", "Active yesterday"
- The `otherUser.last_seen_at` comes from the conversation API response

### Step 5: Frontend — "Seen" Read Receipts on Messages
- In `MessageConversation.vue`:
  - Listen for `.MessageRead` event on the private chat channel
  - When received, update all sent messages to show "Seen" status
  - Show beneath the last sent message: "Seen" with a small timestamp
  - Visual: double checkmark or "Seen" text below the last message that was read
  - Only show on the LAST consecutive sent message (not every message)

### Step 6: Frontend — Real-time "Seen" notification from recipient
- When user opens a conversation → `markAsRead()` is called → backend fires `MessageRead` event
- The sender (on the other browser) receives this event and updates their UI to show "Seen"

### Step 7: UI/UX Enhancement — MessageConversation.vue
Enhance the chat interface:
- **Chat header**: Cleaner layout, show last seen time, online pulse animation
- **Message bubbles**: Slightly larger, better spacing, subtle shadow, smooth entrance animation
- **Sent message status**: Show delivery/read status icons (single check = sent, double check = delivered, blue double check = seen)
- **Typing indicator**: Already exists, polish the animation
- **Input area**: More modern feel — rounded input, smooth focus transitions, emoji-style send button
- **Date separators**: Group messages by date with "Today", "Yesterday", date headers
- **Empty state**: More inviting empty chat illustration

### Step 8: UI/UX Enhancement — ConversationsList.vue
Enhance the conversations list:
- **Search bar** at top to filter conversations
- **Last seen time** below each conversation name
- **Online pulse dot** animation for online users
- **Better unread styling** — bold preview text for unread conversations
- **Smoother hover/click transitions**

---

## Files to Modify/Create

### New Files:
1. `database/migrations/xxxx_add_last_seen_at_to_users_table.php`
2. `app/Events/MessageRead.php`

### Modified Files:
3. `app/Models/User.php` — add `last_seen_at` to fillable/casts
4. `app/Http/Controllers/Api/MessageController.php` — dispatch MessageRead event in markConversationRead(), add heartbeat method
5. `routes/api.php` — add heartbeat route
6. `routes/channels.php` — no changes needed (chat.{userId} already exists)
7. `resources/js/pages/MessageConversation.vue` — major UI/UX overhaul + seen/heartbeat
8. `resources/js/pages/ConversationsList.vue` — UI/UX overhaul + last seen
