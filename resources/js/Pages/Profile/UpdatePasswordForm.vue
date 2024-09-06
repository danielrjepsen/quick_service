<template>
    <jet-form-section @submitted="updatePassword">
        <template #title>
            {{ $t('update_profile.password_title') }}
        </template>

        <template #description>
            {{ $t('update_profile.password_description') }}
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="current_password" :value="$t('update_profile.password_current')" />
                <el-input id="current_password" type="password" class="mt-1 block" v-model="form.current_password" ref="current_password" autocomplete="current-password" />
                <jet-input-error :message="form.errors.current_password" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="password" :value="$t('update_profile.password_new')" />
                <el-input id="password" type="password" class="mt-1 block" v-model="form.password" ref="password" autocomplete="new-password" />
                <jet-input-error :message="form.errors.password" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="password_confirmation" :value="$t('update_profile.password_new_confirm')" />
                <el-input id="password_confirmation" type="password" class="mt-1 block" v-model="form.password_confirmation" autocomplete="new-password" />
                <jet-input-error :message="form.errors.password_confirmation" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                {{ $t('saved') }}
            </jet-action-message>

            <el-button round native-type="submit" type="primary" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                {{ $t('save') }}
            </el-button>
        </template>
    </jet-form-section>
</template>

<script>
    import JetActionMessage from '@/Jetstream/ActionMessage'
    import JetFormSection from '@/Jetstream/FormSection'
    import JetInputError from '@/Jetstream/InputError'
    import JetLabel from '@/Jetstream/Label'

    export default {
        components: {
            JetActionMessage,
            JetFormSection,
            JetInputError,
            JetLabel,
        },

        data() {
            return {
                form: this.$inertia.form({
                    current_password: '',
                    password: '',
                    password_confirmation: '',
                }),
            }
        },

        methods: {
            updatePassword() {
                this.form.put(route('user-password.update'), {
                    errorBag: 'updatePassword',
                    preserveScroll: true,
                    onSuccess: () => this.form.reset(),
                    onError: () => {
                        if (this.form.errors.password) {
                            this.form.reset('password', 'password_confirmation')
                            this.$refs.password.focus()
                        }

                        if (this.form.errors.current_password) {
                            this.form.reset('current_password')
                            this.$refs.current_password.focus()
                        }
                    }
                })
            },
        },
    }
</script>
