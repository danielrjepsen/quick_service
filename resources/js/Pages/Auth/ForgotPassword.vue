<template>
    <jet-authentication-card>
        <template #logo>
            <authentication-card-banner />
        </template>

        <div class="mb-4 text-sm text-gray-600">
            {{ $t('forgot_your_password_text') }}
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <jet-validation-errors class="mb-4" />

        <form @submit.prevent="submit">
            <div>
                <jet-label for="email" :value="$t('email')" />
                <el-input id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autofocus />
            </div>

            <div class="flex items-center justify-end mt-4">
                <el-button round native-type="submit" type="primary" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ $t('email_password_reset_link') }}
                </el-button>
            </div>
        </form>
    </jet-authentication-card>
</template>

<script>
    import JetAuthenticationCard from '@/Jetstream/AuthenticationCard'
    import AuthenticationCardBanner from '@/Components/AuthenticationCardBanner'
    import JetLabel from '@/Jetstream/Label'
    import JetValidationErrors from '@/Jetstream/ValidationErrors'

    export default {
        components: {
            JetAuthenticationCard,
            AuthenticationCardBanner,
            JetLabel,
            JetValidationErrors
        },

        props: {
            status: String
        },

        data() {
            return {
                form: this.$inertia.form({
                    email: ''
                })
            }
        },

        methods: {
            submit() {
                this.form.post(this.route('password.email'))
            }
        }
    }
</script>
