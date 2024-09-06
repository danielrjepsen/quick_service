<template>
    <jet-action-section>
        <template #title>
            {{ $t('update_profile.browser_sessions_title') }}
        </template>

        <template #description>
            {{ $t('update_profile.browser_sessions_description') }}
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600">
                {{ $t('update_profile.browser_sessions_content') }}
            </div>

            <!-- Other Browser Sessions -->
            <div class="mt-5 space-y-6" v-if="sessions.length > 0">
                <div class="flex items-center" v-for="(session, i) in sessions" :key="i">
                    <div>
                        <svg fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor" class="w-8 h-8 text-gray-500" v-if="session.agent.is_desktop">
                            <path d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" class="w-8 h-8 text-gray-500" v-else>
                            <path d="M0 0h24v24H0z" stroke="none"></path><rect x="7" y="4" width="10" height="16" rx="1"></rect><path d="M11 5h2M12 17v.01"></path>
                        </svg>
                    </div>

                    <div class="ml-3">
                        <div class="text-sm text-gray-600">
                            {{ session.agent.platform }} - {{ session.agent.browser }}
                        </div>

                        <div>
                            <div class="text-xs text-gray-500">
                                {{ session.ip_address }},

                                <span class="text-green-500 font-semibold" v-if="session.is_current_device">{{ $t('update_profile.this_device') }}</span>
                                <span v-else>{{ $t('update_profile.last_active') }} {{ session.last_active }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center mt-5">
                <el-button round native-type="submit" type="primary" @click.native="confirmLogout">
                    {{ $t('update_profile.logout_other_browser_sessions') }}
                </el-button>

                <jet-action-message :on="form.recentlySuccessful" class="ml-3">
                    {{ $t('done') }}
                </jet-action-message>
            </div>

            <!-- Logout Other Devices Confirmation Modal -->
            <jet-dialog-modal :show="confirmingLogout" @close="closeModal">
                <template #title>
                    {{ $t('update_profile.logout_other_browser_sessions') }}
                </template>

                <template #content>
                    {{ $t('update_profile.logout_other_browser_sessions_content') }}

                    <div class="mt-4">
                        <el-input type="password" class="mt-1 block w-3/4" placeholder="Password"
                                    ref="password"
                                    v-model="form.password"
                                    @keyup.enter.native="logoutOtherBrowserSessions" />

                        <jet-input-error :message="form.errors.password" class="mt-2" />
                    </div>
                </template>

                <template #footer>
                    <el-button round native-type="button" @click.native="closeModal">
                        {{ $t('nevermind') }}
                    </el-button>

                    <el-button round native-type="submit" type="primary" class="ml-2" @click.native="logoutOtherBrowserSessions" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        {{ $t('update_profile.logout_other_browser_sessions_content') }}
                    </el-button>
                </template>
            </jet-dialog-modal>
        </template>
    </jet-action-section>
</template>

<script>
    import JetActionMessage from '@/Jetstream/ActionMessage'
    import JetActionSection from '@/Jetstream/ActionSection'
    import JetDialogModal from '@/Jetstream/DialogModal'
    import JetInputError from '@/Jetstream/InputError'

    export default {
        props: ['sessions'],

        components: {
            JetActionMessage,
            JetActionSection,
            JetDialogModal,
            JetInputError,
        },

        data() {
            return {
                confirmingLogout: false,

                form: this.$inertia.form({
                    password: '',
                })
            }
        },

        methods: {
            confirmLogout() {
                this.confirmingLogout = true

                setTimeout(() => this.$refs.password.focus(), 250)
            },

            logoutOtherBrowserSessions() {
                this.form.delete(route('other-browser-sessions.destroy'), {
                    preserveScroll: true,
                    onSuccess: () => this.closeModal(),
                    onError: () => this.$refs.password.focus(),
                    onFinish: () => this.form.reset(),
                })
            },

            closeModal() {
                this.confirmingLogout = false

                this.form.reset()
            },
        },
    }
</script>
