<script setup lang="ts">
import { ref } from 'vue';
import { Bell, Mail, MessageSquare, Shield, Moon, Sun, Save, BadgeCheck } from 'lucide-vue-next';
import { useDarkMode } from '@/composables/useDarkMode';

const { isDark, toggleDarkMode } = useDarkMode();

const emailAlerts = ref(true);
const smsAlerts = ref(false);
const telemetries = ref(true);
const dailyReport = ref(true);
const systemUpdates = ref(false);

const isSaving = ref(false);
const showSuccess = ref(false);

const savePreferences = () => {
    isSaving.value = true;
    setTimeout(() => {
        isSaving.value = false;
        showSuccess.value = true;
        setTimeout(() => {
            showSuccess.value = false;
        }, 3000);
    }, 800);
};
</script>

<template>
    <section class="preferences-section">
        <!-- Theme Toggle Section (Premium Card Design) -->
        <div class="pref-card">
            <div class="pref-card__header">
                <div class="pref-card__info">
                    <h4 class="pref-title">Theme Preference</h4>
                    <p class="pref-desc">Switch between light and dark display modes for the dashboard layout.</p>
                </div>
            </div>
            <div class="pref-card__action">
                <button @click="toggleDarkMode" class="theme-toggle-btn">
                    <span v-if="isDark" class="flex items-center gap-2">
                        <Sun :size="14" class="text-amber-400 animate-spin-slow" />
                        <span>Light Mode</span>
                    </span>
                    <span v-else class="flex items-center gap-2">
                        <Moon :size="14" class="text-indigo-400" />
                        <span>Dark Mode</span>
                    </span>
                </button>
            </div>
        </div>

        <hr class="divider" />

        <div class="space-y-4 mt-4">
            <!-- Email notifications -->
            <div class="toggle-row">
                <div class="toggle-info">
                    <div class="toggle-icon bg-blue-500/10 text-blue-600 dark:text-blue-400">
                        <Mail :size="16" />
                    </div>
                    <div>
                        <label for="email_alerts" class="toggle-label">Email Notifications</label>
                        <p class="toggle-desc">Receive generation alerts, panel diagnostics, and billing statements via email.</p>
                    </div>
                </div>
                <div class="toggle-switch-wrapper">
                    <input type="checkbox" id="email_alerts" v-model="emailAlerts" class="toggle-checkbox" />
                    <label for="email_alerts" class="toggle-switch"></label>
                </div>
            </div>

            <!-- SMS notifications -->
            <div class="toggle-row">
                <div class="toggle-info">
                    <div class="toggle-icon bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                        <MessageSquare :size="16" />
                    </div>
                    <div>
                        <label for="sms_alerts" class="toggle-label">SMS Instant Alerts</label>
                        <p class="toggle-desc">Get text alerts directly to your phone when urgent system failures occur.</p>
                    </div>
                </div>
                <div class="toggle-switch-wrapper">
                    <input type="checkbox" id="sms_alerts" v-model="smsAlerts" class="toggle-checkbox" />
                    <label for="sms_alerts" class="toggle-switch"></label>
                </div>
            </div>

            <!-- Telemetry Logs -->
            <div class="toggle-row">
                <div class="toggle-info">
                    <div class="toggle-icon bg-amber-500/10 text-amber-600 dark:text-amber-400">
                        <Bell :size="16" />
                    </div>
                    <div>
                        <label for="telemetry_logs" class="toggle-label">Telemetry Optimization</label>
                        <p class="toggle-desc">Share anonymized inverter readings to help improve overall cloud predictive algorithms.</p>
                    </div>
                </div>
                <div class="toggle-switch-wrapper">
                    <input type="checkbox" id="telemetry_logs" v-model="telemetries" class="toggle-checkbox" />
                    <label for="telemetry_logs" class="toggle-switch"></label>
                </div>
            </div>

            <!-- Weekly summaries -->
            <div class="toggle-row">
                <div class="toggle-info">
                    <div class="toggle-icon bg-purple-500/10 text-purple-600 dark:text-purple-400">
                        <Shield :size="16" />
                    </div>
                    <div>
                        <label for="daily_report" class="toggle-label">Weekly Production Report</label>
                        <p class="toggle-desc">A comprehensive summary of active telemetry readings sent to your inbox every Sunday.</p>
                    </div>
                </div>
                <div class="toggle-switch-wrapper">
                    <input type="checkbox" id="daily_report" v-model="dailyReport" class="toggle-checkbox" />
                    <label for="daily_report" class="toggle-switch"></label>
                </div>
            </div>
        </div>

        <div class="actions-bar mt-6 pt-4">
            <button @click="savePreferences" class="save-btn" :disabled="isSaving">
                <Save :size="15" class="mr-1.5" />
                <span>{{ isSaving ? 'Saving...' : 'Save Preferences' }}</span>
            </button>

            <Transition
                enter-active-class="transition ease-out duration-300"
                enter-from-class="opacity-0 translate-x-2"
                leave-active-class="transition ease-in duration-200"
                leave-to-class="opacity-0 translate-x-2"
            >
                <p v-if="showSuccess" class="save-success-toast">
                    <BadgeCheck :size="16" class="text-emerald-500 mr-1" />
                    Preferences saved successfully.
                </p>
            </Transition>
        </div>
    </section>
</template>

<style scoped>
.preferences-section {
    display: flex;
    flex-direction: column;
}

.divider {
    border: 0;
    height: 1px;
    background: #e2e8f0;
    margin: 20px 0;
}
:root.dark .divider,
.dark .divider {
    background: #1e293b;
}

.pref-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    padding: 16px 20px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
}
:root.dark .pref-card,
.dark .pref-card {
    background: rgba(21, 27, 45, 0.4);
    border-color: #1e293b;
}

.pref-title {
    font-size: 0.9rem;
    font-weight: 700;
    color: #0f172a;
    letter-spacing: -0.01em;
}
:root.dark .pref-title,
.dark .pref-title {
    color: #f1f5f9;
}

.pref-desc {
    font-size: 0.78rem;
    color: #64748b;
    margin-top: 2px;
}
:root.dark .pref-desc,
.dark .pref-desc {
    color: #475569;
}

.theme-toggle-btn {
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 0.8rem;
    font-weight: 700;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #334155;
    cursor: pointer;
    box-shadow: 0 1px 2px rgba(0,0,0,0.01);
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.theme-toggle-btn:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
    transform: translateY(-0.5px);
}
:root.dark .theme-toggle-btn,
.dark .theme-toggle-btn {
    background: #0f1322;
    border-color: #1e293b;
    color: #f1f5f9;
}
:root.dark .theme-toggle-btn:hover,
.dark .theme-toggle-btn:hover {
    background: #151b2d;
    border-color: #334155;
}

.animate-spin-slow {
    animation: spin 8s linear infinite;
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Toggle Switch row (Premium Glassmorphic List Style) */
.toggle-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 14px 18px;
    border-radius: 12px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.toggle-row:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    transform: translateY(-0.5px);
}
:root.dark .toggle-row,
.dark .toggle-row {
    background: #0f1322;
    border-color: #1e293b;
}
:root.dark .toggle-row:hover,
.dark .toggle-row:hover {
    background: #151b2d;
    border-color: #334155;
}

.toggle-info {
    display: flex;
    align-items: center;
    gap: 14px;
    flex: 1;
    min-w: 0;
}

.toggle-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 8px;
    flex-shrink: 0;
}

.toggle-label {
    font-size: 0.85rem;
    font-weight: 700;
    color: #334155;
    cursor: pointer;
}
:root.dark .toggle-label,
.dark .toggle-label {
    color: #cbd5e1;
}

.toggle-desc {
    font-size: 0.75rem;
    color: #64748b;
    margin-top: 1px;
}
:root.dark .toggle-desc,
.dark .toggle-desc {
    color: #475569;
}

/* Premium iOS Switch styling */
.toggle-switch-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.toggle-checkbox {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    border: 0;
}

.toggle-switch {
    position: relative;
    display: block;
    width: 44px;
    height: 24px;
    border-radius: 99px;
    background: #cbd5e1;
    cursor: pointer;
    transition: background 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
:root.dark .toggle-switch,
.dark .toggle-switch {
    background: #1e293b;
}

.toggle-switch::after {
    content: '';
    position: absolute;
    top: 2px;
    left: 2px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #ffffff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.15);
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.toggle-checkbox:checked + .toggle-switch {
    background: #10b981;
}
:root.dark .toggle-checkbox:checked + .toggle-switch {
    background: #10b981;
}

.toggle-checkbox:checked + .toggle-switch::after {
    transform: translateX(20px);
}

/* Save Preferences Bar */
.actions-bar {
    display: flex;
    align-items: center;
    gap: 16px;
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
