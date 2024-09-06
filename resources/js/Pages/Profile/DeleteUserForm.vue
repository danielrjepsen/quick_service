<template>
    <jet-action-section>
        <template #title>
            {{ $t('update_profile.delete_account_title') }}
        </template>

        <template #description>
            {{ $t('update_profile.delete_account_description') }}
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600">
                {{ $t('update_profile.delete_account_content') }}
            </div>

            <div class="mt-5">
                <el-button round native-type="button" type="danger" @click.native="confirmUserDeletion">
                    {{ $t('update_profile.delete_account') }}
                </el-button>
            </div>

            <!-- Delete Account Confirmation Modal -->
            <jet-dialog-modal :show="confirmingUserDeletion" @close="closeModal">
                <template #title>
                    {{ $t('update_profile.delete_account') }}
                </template>

                <template #content>
                    {{ $t('update_profile.delete_account_confirm') }}

                    <div class="mt-4">
                        <el-input type="password" class="mt-1 block w-3/4" placeholder="Password"
                                    ref="password"
                                    v-model="form.password"
                                    @keyup.enter.native="deleteUser" />

                        <jet-input-error :message="form.errors.password" class="mt-2" />
                    </div>
                </template>

                <template #footer>
                    <el-button round native-type="button" @click.native="closeModal">
                        {{ $t('nevermind') }}
                    </el-button>

                    <el-button round native-type="button" type="danger" class="ml-2" @click.native="deleteUser" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        {{ $t('update_profile.delete_account') }}
                    </el-button>
                </template>
            </jet-dialog-modal>
        </template>
    </jet-action-section>
</template>

<script>
    import JetActionSection from '@/Jetstream/ActionSection'
    import JetDialogModal from '@/Jetstream/DialogModal'
    import JetInputError from '@/Jetstream/InputError'

    export default {
        components: {
            JetActionSection,
            JetDialogModal,
            JetInputError,
        },

        data() {
            return {
                confirmingUserDeletion: false,

                form: this.$inertia.form({
                    password: '',
                })
            }
        },

        methods: {
            confirmUserDeletion() {
                this.confirmingUserDeletion = true;

                setTimeout(() => this.$refs.password.focus(), 250)
            },

            deleteUser() {
                this.form.delete(route('current-user.destroy'), {
                    preserveScroll: true,
                    onSuccess: () => this.closeModal(),
                    onError: () => this.$refs.password.focus(),
                    onFinish: () => this.form.reset(),
                })
            },

            closeModal() {
                this.confirmingUserDeletion = false

                this.form.reset()
            },
        },
    }
</script>
