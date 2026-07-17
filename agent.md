# SolarLink AI Agent Rules & Bug Fix Guidelines

This document serves as a rulebook and task list for AI agents working on the SolarLink project. It outlines the architecture, common anti-patterns detected in the codebase, and step-by-step instructions for fixing them. 

## 🏗️ Architecture & Stack
- **Backend:** Laravel 10
- **Frontend:** Vue 3 (Composition API, `<script setup lang="ts">`) + Inertia.js
- **Styling:** Tailwind CSS (v3) + Lucide Icons
- **Roles:** Customer, Technician, Vendor, Admin

## 🚨 Detected Anti-Patterns & Bugs

### 1. Hardcoded Routing (Inertia + Laravel)
**Issue:** Vue components (`Login.vue`, `DashboardLayout.vue`, `Chat/Index.vue`) use hardcoded paths (e.g., `href="/user/login"`, `href="/technician/logout"`). 
**Fix:** Always use Laravel's `Ziggy` package for routing. Replace hardcoded URLs with the `route()` helper function.
- *Bad:* `<Link href="/login">`
- *Good:* `<Link :href="route('login')">`

### 2. Optimistic UI State Corruption (Ghost Messages)
**Issue:** In `Chat/Index.vue`, the `sendMessage` and `sendAttachment` methods push new messages to the local `messages.value` array *before* awaiting the server response. If the `axios.post` request fails, the local state isn't reverted, leaving a "ghost message" in the UI that doesn't exist in the database.
**Fix:** Implement a state rollback mechanism. Keep track of the temporary message index and remove it in the `catch` block if the API call fails. Add error alerts (`useAlert()`) to notify the user.

### 3. Mixed HTTP Clients (Axios vs Inertia)
**Issue:** `Chat/Index.vue` uses raw `axios` to fetch and post messages. While acceptable for pure API endpoints, mixing `axios` with Inertia's `router` can cause fragmented session handling (CSRF mismatch) and bypasses Inertia's built-in progress bar and error handling.
**Fix:** Prefer `@inertiajs/vue3`'s `useForm` or `router.post/get` where state mutations align with Inertia page visits. If `axios` must be used for background polling, ensure interceptors are properly configured for CSRF token refreshes.

### 4. Lack of Real-time Connectivity in Chat
**Issue:** The service chat (`Chat/Index.vue`) lacks WebSockets (Laravel Echo/Reverb) or a polling mechanism. Messages are only fetched when clicking a conversation.
**Fix:** Implement a background polling loop using `setInterval` (ensure to `clearInterval` in `onBeforeUnmount`), or preferably, integrate Laravel Echo for real-time `MessageSent` events.

### 5. Missing Loading States on Heavy Operations
**Issue:** Concluding or deleting a group chat in `Chat/Index.vue` triggers a state change, but if the network is slow, there's no visual loading indicator.
**Fix:** Use Inertia's `onStart` and `onFinish` callbacks to toggle a `processing` ref, disabling the buttons and showing a spinner.

---

## 🛠️ Instructions for the Agent

When tasked to fix the SolarLink project, follow these steps:

1. **Routing Refactor:** 
   - Search the `resources/js` directory for hardcoded `href="/...` links.
   - Inject Ziggy's `route()` helper and refactor all static URLs to use named routes.

2. **Fix Chat Optimistic UI:**
   - Open `resources/js/Pages/Customer/Chat/Index.vue`.
   - Update `sendMessage` and `sendAttachment`.
   - Store the generated ID of the optimistic message.
   - Add a `try/catch` block. On catch, filter out the message by ID and show an error toast.

3. **Implement Real-time Polling (Temporary fix before WebSockets):**
   - Add a `setInterval` in `onMounted` of `Chat/Index.vue` to call `fetchMessages()` every 10 seconds.
   - Clear the interval in `onBeforeUnmount` to prevent memory leaks and infinite background requests.

4. **Verify Role Configurations:**
   - Ensure `DashboardLayout.vue` dynamic `logout` URLs correctly align with defined Laravel routes using Ziggy.

5. **General Cleanliness:**
   - Maintain the premium aesthetic (Tailwind gradients, Lucide icons). DO NOT strip out styling when editing Vue components.
   - Preserve existing TypeScript interfaces (`Participant`, `Conversation`, `Message`).
