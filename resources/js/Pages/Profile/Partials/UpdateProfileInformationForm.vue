<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { User, Mail, Shield, BadgeCheck, Camera, CloudUpload, X, Check } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{
    mustVerifyEmail?: Boolean;
    status?: String;
}>();

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});

// Avatar Form and references
const avatarInput = ref<HTMLInputElement | null>(null);
const avatarPreview = ref<string | null>(null);

const avatarForm = useForm({
    avatar: null as File | null,
});

const triggerAvatarUpload = () => {
    avatarInput.value?.click();
};

const handleAvatarChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) {
        avatarForm.avatar = file;
        
        const reader = new FileReader();
        reader.onload = (event) => {
            avatarPreview.value = event.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const saveAvatar = () => {
    avatarForm.post(route('profile.avatar.update'), {
        forceFormData: true,
        onSuccess: () => {
            avatarForm.reset();
            avatarPreview.value = null;
        },
    });
};

const cancelAvatarSelection = () => {
    avatarForm.reset();
    avatarPreview.value = null;
    if (avatarInput.value) {
        avatarInput.value.value = '';
    }
};

const getRoleBadgeColor = (role: string) => {
    switch (role) {
        case 'admin': return 'role-badge--admin';
        case 'technician': return 'role-badge--tech';
        case 'vendor': return 'role-badge--vendor';
        default: return 'role-badge--customer';
    }
};
</script>

<template>
    <section class="profile-info-section">
        <!-- Live Avatar Upload Area (Premium Glowing Style) -->
        <div class="avatar-upload-container">
            <div class="avatar-preview-container">
                <div class="avatar-preview-wrapper" @click="triggerAvatarUpload" title="Update Profile Picture">
                    <img 
                        v-if="avatarPreview || user.avatar" 
                        :src="avatarPreview || user.avatar" 
                        alt="Profile Avatar" 
                        class="avatar-image"
                    />
                    <div v-else class="avatar-fallback">
                        <span class="avatar-initials">{{ user.name.charAt(0).toUpperCase() }}</span>
                    </div>
                    <div class="avatar-overlay">
                        <Camera :size="16" class="camera-icon" />
                        <span>Upload new</span>
                    </div>
                </div>
                <!-- Permanent edit icon floating badge -->
                <button 
                    type="button" 
                    class="avatar-edit-badge" 
                    @click="triggerAvatarUpload"
                    title="Change Profile Photo"
                >
                    <Camera :size="13" />
                </button>
            </div>
            
            <div class="avatar-text-details">
                <div class="flex items-center gap-3">
                    <h4 class="avatar-name">{{ user.name }}</h4>
                    <span :class="['role-badge', getRoleBadgeColor(user.role)]">
                        <Shield :size="10" class="mr-1" />
                        {{ user.role }}
                    </span>
                </div>
                
                <!-- Conditionally render save/cancel buttons when new photo is chosen -->
                <div v-if="avatarForm.avatar" class="avatar-actions-bar animate-scale-up">
                    <button @click="saveAvatar" class="avatar-save-btn" :disabled="avatarForm.processing">
                        <Check :size="12" class="mr-1" />
                        <span>{{ avatarForm.processing ? 'Uploading...' : 'Save Photo' }}</span>
                    </button>
                    <button @click="cancelAvatarSelection" class="avatar-cancel-btn" :disabled="avatarForm.processing">
                        <X :size="12" class="mr-1" />
                        <span>Cancel</span>
                    </button>
                </div>
                <p v-else class="avatar-meta">Click photo to update. Supports JPG, PNG (Max 2MB)</p>
            </div>
            
            <input 
                type="file" 
                ref="avatarInput" 
                class="hidden" 
                accept="image/*" 
                @change="handleAvatarChange"
            />
        </div>

        <form @submit.prevent="form.patch(route('profile.update'))" class="mt-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name Input Group -->
                <div class="input-group">
                    <InputLabel for="name" value="Full Name" class="custom-label" />
                    <div class="input-wrapper">
                        <span class="input-icon-left">
                            <User :size="16" />
                        </span>
                        <input
                            id="name"
                            type="text"
                            class="custom-input"
                            v-model="form.name"
                            required
                            autofocus
                            placeholder="Enter your full name"
                            autocomplete="name"
                        />
                    </div>
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <!-- Email Input Group -->
                <div class="input-group">
                    <InputLabel for="email" value="Email Address" class="custom-label" />
                    <div class="input-wrapper">
                        <span class="input-icon-left">
                            <Mail :size="16" />
                        </span>
                        <input
                            id="email"
                            type="email"
                            class="custom-input"
                            v-model="form.email"
                            required
                            placeholder="Enter your email address"
                            autocomplete="username"
                        />
                    </div>
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>
            </div>

            <!-- Email verification alert -->
            <div v-if="mustVerifyEmail && user.email_verified_at === null" class="verify-alert">
                <div class="verify-alert__content">
                    <p class="verify-alert__text">
                        Your email address is unverified.
                    </p>
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="verify-alert__link"
                    >
                        Re-send Verification Email
                    </Link>
                </div>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="verify-alert__success"
                >
                    <BadgeCheck :size="16" class="mr-1" />
                    A new verification link has been sent to your email.
                </div>
            </div>

            <!-- Actions Bar -->
            <div class="actions-bar">
                <button 
                    type="submit" 
                    class="save-btn" 
                    :disabled="form.processing || !form.isDirty"
                >
                    <CloudUpload :size="15" class="mr-1.5" />
                    <span>{{ form.processing ? 'Saving Changes...' : 'Save Settings' }}</span>
                </button>

                <Transition
                    enter-active-class="transition ease-out duration-300"
                    enter-from-class="opacity-0 translate-x-2"
                    leave-active-class="transition ease-in duration-200"
                    leave-to-class="opacity-0 translate-x-2"
                >
                    <p v-if="form.recentlySuccessful" class="save-success-toast">
                        <BadgeCheck :size="16" class="text-emerald-500 mr-1" />
                        Profile updated successfully.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>

<style scoped>
.profile-info-section {
    display: flex;
    flex-direction: column;
}

/* Avatar Upload styling (Premium Glowing & Refined) */
.avatar-upload-container {
    display: flex;
    align-items: center;
    gap: 24px;
    padding-bottom: 28px;
    border-bottom: 1px dashed #e2e8f0;
    margin-bottom: 24px;
}
:root.dark .avatar-upload-container,
.dark .avatar-upload-container {
    border-bottom-color: #1e293b;
}

@media (max-width: 640px) {
    .avatar-upload-container {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
}

.avatar-preview-container {
    position: relative;
    width: 92px;
    height: 92px;
}

.avatar-preview-wrapper {
    position: relative;
    width: 88px;
    height: 88px;
    border-radius: 50%;
    overflow: hidden;
    cursor: pointer;
    border: 3px solid #ffffff;
    box-shadow: 0 4px 16px rgba(0,0,0,0.06);
    background: #f1f5f9;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
:root.dark .avatar-preview-wrapper,
.dark .avatar-preview-wrapper {
    border-color: #1e293b;
    background: #151b2d;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
}

.avatar-preview-wrapper:hover {
    transform: scale(1.03);
    border-color: #10b981;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
}

:root.dark .avatar-preview-wrapper:hover,
.dark .avatar-preview-wrapper:hover {
    border-color: #34d399;
    box-shadow: 0 0 0 4px rgba(52, 211, 153, 0.15);
}

.avatar-edit-badge {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #10b981;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2.5px solid #ffffff;
    box-shadow: 0 3px 8px rgba(16, 185, 129, 0.3);
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 5;
}

.avatar-edit-badge:hover {
    background: #059669;
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
}

:root.dark .avatar-edit-badge,
.dark .avatar-edit-badge {
    border-color: #0f1322;
    background: #34d399;
    color: #0f172a;
    box-shadow: 0 3px 8px rgba(52, 211, 153, 0.2);
}
:root.dark .avatar-edit-badge:hover,
.dark .avatar-edit-badge:hover {
    background: #10b981;
    color: white;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.avatar-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-fallback {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.avatar-initials {
    font-size: 2.4rem;
    font-weight: 800;
}

.avatar-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(15, 23, 42, 0.8);
    color: white;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-size: 0.68rem;
    font-weight: 700;
    opacity: 0;
    gap: 4px;
    transition: opacity 0.25s ease;
}

.avatar-preview-wrapper:hover .avatar-overlay {
    opacity: 1;
}

.avatar-text-details {
    display: flex;
    flex-direction: column;
    gap: 10px;
    text-align: left;
}

@media (max-width: 640px) {
    .avatar-text-details {
        align-items: center;
        text-align: center;
    }
}

.avatar-name {
    font-size: 1.25rem;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.02em;
}
:root.dark .avatar-name,
.dark .avatar-name {
    color: #f1f5f9;
}

.avatar-meta {
    font-size: 0.78rem;
    color: #64748b;
    font-weight: 500;
}
:root.dark .avatar-meta,
.dark .avatar-meta {
    color: #475569;
}

/* Avatar Change Actions (Beautiful Micro-animation) */
.avatar-actions-bar {
    display: flex;
    gap: 8px;
    align-items: center;
}

.animate-scale-up {
    animation: scaleUp 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes scaleUp {
    from { transform: scale(0.95); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

.avatar-save-btn {
    display: inline-flex;
    align-items: center;
    padding: 6px 14px;
    border-radius: 8px;
    background: #10b981;
    color: white;
    font-size: 0.75rem;
    font-weight: 700;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2);
}
.avatar-save-btn:hover {
    background: #059669;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
}

.avatar-cancel-btn {
    display: inline-flex;
    align-items: center;
    padding: 6px 14px;
    border-radius: 8px;
    background: #f1f5f9;
    color: #475569;
    font-size: 0.75rem;
    font-weight: 600;
    border: 1px solid #cbd5e1;
    cursor: pointer;
    transition: all 0.2s;
}
.avatar-cancel-btn:hover {
    background: #e2e8f0;
    border-color: #94a3b8;
}
:root.dark .avatar-cancel-btn,
.dark .avatar-cancel-btn {
    background: #151b2d;
    border-color: #1e293b;
    color: #94a3b8;
}
:root.dark .avatar-cancel-btn:hover,
.dark .avatar-cancel-btn:hover {
    background: #1e293b;
    color: #f1f5f9;
}

/* Role badge */
.role-badge {
    display: inline-flex;
    align-items: center;
    padding: 2.5px 10px;
    border-radius: 9999px;
    font-size: 0.65rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}
.role-badge--admin {
    background: rgba(168, 85, 247, 0.1);
    border: 1px solid rgba(168, 85, 247, 0.15);
    color: #a855f7;
}
.role-badge--tech {
    background: rgba(16, 185, 129, 0.1);
    border: 1px solid rgba(16, 185, 129, 0.15);
    color: #10b981;
}
.role-badge--vendor {
    background: rgba(79, 70, 229, 0.1);
    border: 1px solid rgba(79, 70, 229, 0.15);
    color: #4f46e5;
}
.role-badge--customer {
    background: rgba(245, 158, 11, 0.1);
    border: 1px solid rgba(245, 158, 11, 0.15);
    color: #f59e0b;
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
    padding: 12px 16px 12px 42px;
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

/* Verification Banner */
.verify-alert {
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: 12px;
    padding: 16px;
}
:root.dark .verify-alert,
.dark .verify-alert {
    background: rgba(245, 158, 11, 0.04);
    border-color: rgba(245, 158, 11, 0.15);
}

.verify-alert__content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}

.verify-alert__text {
    font-size: 0.85rem;
    font-weight: 600;
    color: #d97706;
}

.verify-alert__link {
    font-size: 0.8rem;
    font-weight: 700;
    color: #b45309;
    text-decoration: underline;
    background: none;
    border: none;
    cursor: pointer;
    transition: color 0.2s;
}
.verify-alert__link:hover {
    color: #78350f;
}
:root.dark .verify-alert__link,
.dark .verify-alert__link {
    color: #fbbf24;
}
:root.dark .verify-alert__link:hover,
.dark .verify-alert__link:hover {
    color: #f59e0b;
}

.verify-alert__success {
    margin-top: 10px;
    display: flex;
    align-items: center;
    font-size: 0.8rem;
    font-weight: 700;
    color: #10b981;
}
</style>
