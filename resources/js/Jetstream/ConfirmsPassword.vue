<template>
    <span>
        <span @click="startConfirmingPassword">
            <slot />
        </span>

        <jet-dialog-modal :show="confirmingPassword" @close="closeModal">
            <template #title>
                {{ title }}
            </template>

            <template #content>
                {{ content }}

                <div class="mt-4">
                    <el-input type="password" class="mt-1 block w-3/4" placeholder="Password"
                                ref="password"
                                v-model="form.password"
                                @keyup.enter.native="confirmPassword" />

                    <jet-input-error :message="form.error" class="mt-2" />
                </div>
            </template>

            <template #footer>
                <el-button round native-type="button" @click.native="closeModal">
                    Nevermind
                </el-button>

                <el-button round native-type="submit" class="ml-2" @click.native="confirmPassword" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ button }}
                </el-button>
            </template>
        </jet-dialog-modal>
    </span>
</template>

<script>
    import JetDialogModal from './DialogModal'
    import JetInputError from './InputError'

    export default {
        props: {
            title: {
                default: 'Confirm Password',
            },
            content: {
                default: 'For your security, please confirm your password to continue.',
            },
            button: {
                default: 'Confirm',
            }
        },

        components: {
            JetDialogModal,
            JetInputError,
        },

        data() {
            return {
                confirmingPassword: false,
                form: {
                    password: '',
                    error: '',
                },
            }
        },

        methods: {
            startConfirmingPassword() {
                axios.get(route('password.confirmation')).then(response => {
                    if (response.data.confirmed) {
                        this.$emit('confirmed');
                    } else {
                        this.confirmingPassword = true;

                        setTimeout(() => this.$refs.password.focus(), 250)
                    }
                })
            },

            confirmPassword() {
                this.form.processing = true;

                axios.post(route('password.confirm'), {
                    password: this.form.password,
                }).then(() => {
                    this.form.processing = false;
                    this.closeModal()
                    this.$nextTick(() => this.$emit('confirmed'));
                }).catch(error => {
                    this.form.processing = false;
                    this.form.error = error.response.data.errors.password[0];
                    this.$refs.password.focus()
                });
            },

            closeModal() {
                this.confirmingPassword = false
                this.form.password = '';
                this.form.error = '';
            },
        }
    }
</script>
