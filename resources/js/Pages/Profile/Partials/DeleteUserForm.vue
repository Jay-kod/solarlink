<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';
import { Trash2, AlertTriangle, X, ShieldAlert } from 'lucide-vue-next';

const confirmingUserDeletion = ref(false);
const passwordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value?.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => {
            form.reset();
        },
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.reset();
};
</script>

<template>
    <section class="danger-zone-section">
        <div class="mt-2">
            <button
                @click="confirmUserDeletion"
                class="delete-btn"
                v-if="!confirmingUserDeletion"
            >
                <Trash2 :size="15" class="mr-1.5" />
                <span>Delete Account</span>
            </button>
        </div>

        <!-- Inline Confirmation Panel (Premium Glassmorphic Caution Box) -->
        <Transition
            enter-active-class="transition ease-out duration-250 cubic-bezier(0.34, 1.56, 0.64, 1)"
            enter-from-class="opacity-0 translate-y-2 scale-98"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 translate-y-2 scale-98"
        >
            <div v-if="confirmingUserDeletion" class="confirm-panel">
                <div class="confirm-panel__header">
                    <div class="warning-icon-badge">
                        <AlertTriangle :size="16" />
                    </div>
                    <h3 class="confirm-panel__title">Irreversible Operation</h3>
                </div>
                
                <p class="confirm-panel__text">
                    This action is permanent and cannot be undone. All your panel configurations, service records, 
                    telemetry archives, and settings will be instantly destroyed. 
                    Please enter your password to authorize this action.
                </p>

                <div class="mt-4">
                    <InputLabel for="delete_password" value="Verify Password" class="custom-label" />
                    <div class="delete-input-wrapper">
                        <TextInput
                            id="delete_password"
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            class="delete-text-input"
                            placeholder="Enter password to confirm"
                            @keyup.enter="deleteUser"
                        />
                    </div>
                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-5 flex justify-end gap-3 flex-wrap">
                    <button @click="closeModal" class="cancel-btn">
                        <X :size="13" class="mr-1" />
                        <span>Cancel</span>
                    </button>
                    <button
                        @click="deleteUser"
                        class="confirm-delete-btn"
                        :class="{ 'opacity-50 pointer-events-none': form.processing }"
                        :disabled="form.processing"
                    >
                        <ShieldAlert :size="14" class="mr-1" />
                        <span>Yes, Delete Account</span>
                    </button>
                </div>
            </div>
        </Transition>
    </section>
</template>

<style scoped>
.danger-zone-section {
    display: flex;
    flex-direction: column;
}

/* Delete Button (Premium, Subtle Red Outlined Style) */
.delete-btn {
    display: inline-flex;
    align-items: center;
    padding: 10px 22px;
    border-radius: 12px;
    font-size: 0.85rem;
    font-weight: 700;
    border: 1px solid #fee2e2;
    background: #fef2f2;
    color: #ef4444;
    cursor: pointer;
    box-shadow: 0 1px 2px rgba(239, 68, 68, 0.05);
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.delete-btn:hover {
    background: #ef4444;
    color: white;
    border-color: #ef4444;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
    transform: translateY(-0.5px);
}

:root.dark .delete-btn,
.dark .delete-btn {
    background: rgba(239, 68, 68, 0.06);
    border-color: rgba(239, 68, 68, 0.15);
    color: #fca5a5;
    box-shadow: none;
}
:root.dark .delete-btn:hover,
.dark .delete-btn:hover {
    background: #ef4444;
    color: white;
    border-color: #ef4444;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
}

/* Premium Caution Panel layout */
.confirm-panel {
    padding: 24px;
    background: #fef2f2;
    border: 1px solid #fee2e2;
    border-radius: 16px;
    box-shadow: 0 4px 16px rgba(239, 68, 68, 0.03);
}

:root.dark .confirm-panel,
.dark .confirm-panel {
    background: rgba(239, 68, 68, 0.02);
    border-color: rgba(239, 68, 68, 0.1);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.confirm-panel__header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}

.warning-icon-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.confirm-panel__title {
    font-size: 0.95rem;
    font-weight: 700;
    color: #991b1b;
    letter-spacing: -0.01em;
}
:root.dark .confirm-panel__title,
.dark .confirm-panel__title {
    color: #fca5a5;
}

.confirm-panel__text {
    font-size: 0.8rem;
    color: #b91c1c;
    line-height: 1.6;
}
:root.dark .confirm-panel__text,
.dark .confirm-panel__text {
    color: #fca5a5;
    opacity: 0.85;
}

.custom-label {
    font-size: 0.75rem;
    font-weight: 800;
    color: #991b1b;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}
:root.dark .custom-label,
.dark .custom-label {
    color: #fca5a5;
}

.delete-text-input {
    width: 100% !important;
    padding: 11px 16px !important;
    border-radius: 12px !important;
    font-size: 0.9rem !important;
    border: 1px solid #fca5a5 !important;
    background: #ffffff !important;
    color: #0f172a !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

.delete-text-input:focus {
    outline: none !important;
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.12) !important;
}

:root.dark .delete-text-input,
.dark .delete-text-input {
    border-color: rgba(239, 68, 68, 0.2) !important;
    background: #0f1322 !important;
    color: #f1f5f9 !important;
}

:root.dark .delete-text-input:focus,
.dark .delete-text-input:focus {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15) !important;
}

/* Action Buttons inside Confirm panel */
.cancel-btn {
    display: inline-flex;
    align-items: center;
    padding: 9px 18px;
    border-radius: 10px;
    font-size: 0.8rem;
    font-weight: 700;
    background: #ffffff;
    color: #475569;
    border: 1px solid #cbd5e1;
    cursor: pointer;
    transition: all 0.2s;
}
.cancel-btn:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
}
:root.dark .cancel-btn,
.dark .cancel-btn {
    background: #151b2d;
    border-color: #1e293b;
    color: #94a3b8;
}
:root.dark .cancel-btn:hover,
.dark .cancel-btn:hover {
    background: #1e293b;
    color: #f1f5f9;
}

.confirm-delete-btn {
    display: inline-flex;
    align-items: center;
    padding: 9px 18px;
    border-radius: 10px;
    font-size: 0.8rem;
    font-weight: 700;
    background: #ef4444;
    color: white;
    border: none;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.2);
    transition: all 0.2s;
}
.confirm-delete-btn:hover {
    background: #dc2626;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.35);
}
</style>
