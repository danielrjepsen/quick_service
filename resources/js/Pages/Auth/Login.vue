<template>
        <jet-authentication-card>
            <template #logo>
                <authentication-card-banner />
            </template>

            <h1 class="text-4xl font-bold mb-3">{{ $t('login_on_site_title') }}</h1>

            <jet-validation-errors class="mb-4" />

            <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                {{ status }}
            </div>

            <form @submit.prevent="submit">
                <div>
                    <jet-label for="email" :value="$t('email')" />
                    <el-input id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autofocus />
                </div>

                <div class="mt-4">
                    <jet-label for="password" :value="$t('password')" />
                    <el-input id="password" type="password" class="mt-1 block w-full" style="background-color: #f4f4f4" v-model="form.password" required autocomplete="current-password" />
                </div>

                <div class="block mt-4">
                    <label class="flex items-center">
                        <jet-checkbox name="remember" v-model="form.remember" />
                        <span class="ml-2 text-sm text-gray-600">{{ $t('remember_me') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-start mt-4">
                    <el-button round native-type="submit" type="primary" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        {{ $t('login') }}
                    </el-button>

                    <inertia-link :href="route('register')" class="ml-4 underline text-sm text-gray-600 hover:text-gray-900">
                        {{ $t('register') }}
                    </inertia-link>

                    <inertia-link v-if="canResetPassword" :href="route('password.request')" class="ml-4 underline text-sm text-gray-600 hover:text-gray-900">
                        {{ $t('forgot_your_password') }}
                    </inertia-link>
                </div>
            </form>
        </jet-authentication-card>
</template>

<script>
    import JetAuthenticationCard from '@/Jetstream/AuthenticationCard'
    import AuthenticationCardBanner from '@/Components/AuthenticationCardBanner'
    import JetCheckbox from '@/Jetstream/Checkbox'
    import JetLabel from '@/Jetstream/Label'
    import JetValidationErrors from '@/Jetstream/ValidationErrors'

    export default {
        components: {
            JetAuthenticationCard,
            AuthenticationCardBanner,
            JetCheckbox,
            JetLabel,
            JetValidationErrors
        },

        props: {
            canResetPassword: Boolean,
            status: String
        },

        data() {
            return {
                form: this.$inertia.form({
                    email: '',
                    password: '',
                    remember: false
                })
            }
        },

        methods: {
            submit() {
                this.form
                    .transform(data => ({
                        ... data,
                        remember: this.form.remember ? 'on' : ''
                    }))
                    .post(this.route('login'), {
                        onFinish: () => this.form.reset('password'),
                    })
            }
        }
    }
</script>
