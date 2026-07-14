<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Lock, Eye, EyeOff, ShieldCheck, BadgeCheck } from 'lucide-vue-next';

const passwordInput = ref<HTMLInputElement | null>(null);
const currentPasswordInput = ref<HTMLInputElement | null>(null);

const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value?.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value?.focus();
            }
        },
    });
};
</script>

<template>
    <section class="password-update-section">
        <form @submit.prevent="updatePassword" class="mt-2 space-y-6">
            <!-- Current Password -->
            <div class="input-group">
                <InputLabel for="current_password" value="Current Password" class="custom-label" />
                <div class="input-wrapper">
                    <span class="input-icon-left">
                        <Lock :size="16" />
                    </span>
                    <input
                        id="current_password"
                        ref="currentPasswordInput"
                        v-model="form.current_password"
                        :type="showCurrentPassword ? 'text' : 'password'"
                        class="custom-input"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        required
                    />
                    <button 
                        type="button" 
                        class="toggle-visibility-btn" 
                        @click="showCurrentPassword = !showCurrentPassword"
                        title="Toggle password visibility"
                    >
                        <EyeOff v-if="showCurrentPassword" :size="16" />
                        <Eye v-else :size="16" />
                    </button>
                </div>
                <InputError :message="form.errors.current_password" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- New Password -->
                <div class="input-group">
                    <InputLabel for="password" value="New Password" class="custom-label" />
                    <div class="input-wrapper">
                        <span class="input-icon-left">
                            <Lock :size="16" />
                        </span>
                        <input
                            id="password"
                            ref="passwordInput"
                            v-model="form.password"
                            :type="showNewPassword ? 'text' : 'password'"
                            class="custom-input"
                            placeholder="Min. 8 characters"
                            autocomplete="new-password"
                            required
                        />
                        <button 
                            type="button" 
                            class="toggle-visibility-btn" 
                            @click="showNewPassword = !showNewPassword"
                            title="Toggle password visibility"
                        >
                            <EyeOff v-if="showNewPassword" :size="16" />
                            <Eye v-else :size="16" />
                        </button>
                    </div>
                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="input-group">
                    <InputLabel for="password_confirmation" value="Confirm New Password" class="custom-label" />
                    <div class="input-wrapper">
                        <span class="input-icon-left">
                            <Lock :size="16" />
                        </span>
                        <input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            :type="showConfirmPassword ? 'text' : 'password'"
                            class="custom-input"
                            placeholder="Match new password"
                            autocomplete="new-password"
                            required
                        />
                        <button 
                            type="button" 
                            class="toggle-visibility-btn" 
                            @click="showConfirmPassword = !showConfirmPassword"
                            title="Toggle password visibility"
                        >
                            <EyeOff v-if="showConfirmPassword" :size="16" />
                            <Eye v-else :size="16" />
                        </button>
                    </div>
                    <InputError :message="form.errors.password_confirmation" class="mt-2" />
                </div>
            </div>

            <!-- Password guidelines helper (Premium Quiet Alert) -->
            <div class="password-guidelines">
                <h5 class="guidelines-title">Security Recommendation:</h5>
                <ul class="guidelines-list">
                    <li>Use a combination of uppercase and lowercase letters</li>
                    <li>Incorporate at least one number and one special character</li>
                    <li>Avoid reusing previous passwords or common phrases</li>
                </ul>
            </div>

            <!-- Action bar -->
            <div class="actions-bar">
                <button 
                    type="submit" 
                    class="save-btn" 
                    :disabled="form.processing || !form.isDirty"
                >
                    <ShieldCheck :size="15" class="mr-1.5" />
                    <span>{{ form.processing ? 'Updating...' : 'Update Password' }}</span>
                </button>

                <Transition
                    enter-active-class="transition ease-out duration-300"
                    enter-from-class="opacity-0 translate-x-2"
                    leave-active-class="transition ease-in duration-200"
                    leave-to-class="opacity-0 translate-x-2"
                >
                    <p v-if="form.recentlySuccessful" class="save-success-toast">
                        <BadgeCheck :size="16" class="text-emerald-500 mr-1" />
                        Password changed.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>

<style scoped>
.password-update-section {
    display: flex;
    flex-direction: column;
}

/* Inputs styling (Linear/Vercel Aesthetic) */
.custom-label {
    font-size: 0.8rem;
    font-weight: 700;
    color: #475569;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
:root.dark .custom-label,
.dark .custom-label {
    color: #94a3b8;
}

.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.input-icon-left {
    position: absolute;
    left: 14px;
    color: #94a3b8;
    pointer-events: none;
    display: flex;
    align-items: center;
}
:root.dark .input-icon-left,
.dark .input-icon-left {
    color: #475569;
}

.custom-input {
    width: 100%;
    padding: 12px 42px 12px 42px;
    border-radius: 12px;
    font-size: 0.9rem;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #0f172a;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 2px rgba(0,0,0,0.01);
}
.custom-input:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}
.custom-input::placeholder {
    color: #cbd5e1;
}

:root.dark .custom-input,
.dark .custom-input {
    border-color: #1e293b;
    background: #0b0f19;
    color: #f1f5f9;
}
:root.dark .custom-input:focus,
.dark .custom-input:focus {
    border-color: #34d399;
    box-shadow: 0 0 0 3px rgba(52, 211, 153, 0.1);
}

.toggle-visibility-btn {
    position: absolute;
    right: 14px;
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    display: flex;
    align-items: center;
    padding: 6px;
    border-radius: 6px;
    transition: all 0.2s;
}
.toggle-visibility-btn:hover {
    color: #475569;
    background: #f1f5f9;
}
:root.dark .toggle-visibility-btn,
.dark .toggle-visibility-btn {
    color: #475569;
}
:root.dark .toggle-visibility-btn:hover,
.dark .toggle-visibility-btn:hover {
    color: #cbd5e1;
    background: #151b2d;
}

/* Password Guidelines panel (Subtle, sleek quiet alert box) */
.password-guidelines {
    padding: 16px 20px;
    border-radius: 12px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
}
:root.dark .password-guidelines,
.dark .password-guidelines {
    background: rgba(21, 27, 45, 0.4);
    border-color: #1e293b;
}

.guidelines-title {
    font-size: 0.75rem;
    font-weight: 800;
    color: #475569;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}
:root.dark .guidelines-title,
.dark .guidelines-title {
    color: #94a3b8;
}

.guidelines-list {
    font-size: 0.78rem;
    color: #64748b;
    list-style-type: none;
    padding-left: 0;
    display: flex;
    flex-direction: column;
    gap: 6px;
}
:root.dark .guidelines-list,
.dark .guidelines-list {
    color: #475569;
}

.guidelines-list li {
    position: relative;
    padding-left: 14px;
}

.guidelines-list li::before {
    content: "•";
    color: #10b981;
    position: absolute;
    left: 0;
    font-weight: bold;
}

:root.dark .guidelines-list li::before {
    color: #34d399;
}

/* Button & Actions bar */
.actions-bar {
    display: flex;
    align-items: center;
    gap: 16px;
    padding-top: 12px;
}

.save-btn {
    display: inline-flex;
    align-items: center;
    padding: 11px 24px;
    border-radius: 12px;
    background: #10b981;
    color: white;
    font-size: 0.85rem;
    font-weight: 700;
    border: none;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 3px 10px rgba(16, 185, 129, 0.2);
}

.save-btn:hover {
    background: #059669;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
    transform: translateY(-1px);
}
.save-btn:active {
    transform: translateY(0);
}
.save-btn:disabled {
    background: #e2e8f0;
    color: #94a3b8;
    cursor: not-allowed;
    box-shadow: none;
    transform: none;
}
:root.dark .save-btn:disabled,
.dark .save-btn:disabled {
    background: #151b2d;
    color: #334155;
}

.save-success-toast {
    display: inline-flex;
    align-items: center;
    font-size: 0.8rem;
    font-weight: 600;
    color: #059669;
}
:root.dark .save-success-toast,
.dark .save-success-toast {
    color: #34d399;
}
</style>
