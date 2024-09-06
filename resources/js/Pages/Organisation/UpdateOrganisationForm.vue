<template>
    <jet-form-section @submitted="updateOrganisation">
        <template #title>
            {{ $t('organisation_title') }}
        </template>

        <template #description>
            {{ $t('organisation_description') }}
        </template>

        <template #form>

            <div class="col-span-6 lg:col-span-5">
                <label for="organisationName">{{ $t('organisation_name') }}</label>
                <el-input id="organisationName"
                          type="text"
                          class="mt-1 block border-1 border-black"
                          v-model="form.name" />

                <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>

            <div class="col-span-6 lg:col-span-5">
                <label for="billingEmail">{{ $t('organisation_billing_email') }}</label>
                <el-input id="billingEmail"
                          type="email"
                          class="mt-1 block"
                          v-model="form.billing_email" />

                <jet-input-error :message="form.errors.billing_email" class="mt-2" />
            </div>

            <div class="col-span-6 lg:col-span-5">
                <label for="billingInfo">{{ $t('organisation_billing_info') }}</label>
                <el-input id="billingInfo"
                          type="textarea"
                          :rows="4"
                          :placeholder="$t('organisation_billing_info')"
                          class="mt-1 block"
                          v-model="form.billing_info" />

                <jet-input-error :message="form.errors.billing_info" class="mt-2" />
            </div>

        </template>

        <template #actions>
            <el-button round native-type="submit" type="primary" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                {{ $t('save') }}
            </el-button>
            <jet-action-message :on="form.recentlySuccessful" class="ml-3">
                {{ $t('saved') }}
            </jet-action-message>
        </template>
    </jet-form-section>
</template>

<script>

import JetFormSection from '@/Jetstream/FormSection'
import JetInputError from '@/Jetstream/InputError'
import JetLabel from '@/Jetstream/Label'
import JetActionMessage from '@/Jetstream/ActionMessage'

export default {
    name: "UpdateOrganisationForm",
    props: ['organisation'],
    components: {
        JetActionMessage,
        JetFormSection,
        JetInputError,
        JetLabel,
    },

    data() {
        return {
            form: this.$inertia.form({
                name: this.organisation.name,
                billing_email: this.organisation.billing_email,
                billing_info: this.organisation.billing_info
            })
        }
    },
    methods: {
        updateOrganisation() {
            this.form.put(route('settings.organisation.billing.update', this.organisation), {
                errorBag: 'updateOrganisation',
                preserveScroll: true
            });
        },
    },
}
</script>
