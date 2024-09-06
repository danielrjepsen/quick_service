<template>
    <tenant-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $t('billing.title') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col lg:flex-row">
                <div class="lg:w-1/2 px-0 lg:px-4 w-full">
                    {{ $t('billing.plans') }}
                    <div class="bg-white relative z-0 overflow-hidden shadow-md mt-1 border border-gray-200 rounded-lg cursor-pointer mb-10 p-4 flex flex-col">
                        <button type="button" class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none"
                            :class="{'border-t border-gray-200 rounded-t-none': i > 0, 'rounded-b-none': i != Object.keys($page.props.plans).length - 1}"
                            @click="chosenPlan = plan"
                            v-for="(plan, i) in $page.props.plans"
                            :key="plan.id">
                            <div class="w-full">
                                <div class="flex items-center w-full">
                                    <div class="text-sm text-gray-600" :class="{'font-bold': chosenPlan == plan.id}">
                                        {{ plan.name }}
                                    </div>

                                    <svg v-if="chosenPlan.id == plan.id" class="ml-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>

                                    <div class="text-sm text-gray-600 justify-self-end ml-auto" v-if="i == 0">
                                        {{ $page.props.services_this_month }} / {{ $page.props.free_monthly }}
                                        {{ $t('billing.services') }}
                                    </div>
                                </div>

                                <div class="mt-2 text-xs text-gray-600 flex flex-row justify-between w-full">
                                    <span class="text-left">{{ plan.description }}</span>
                                    <span v-if="i > 0">{{ plan.price | currency_dkk }} / {{ $t('billing.user') }}</span>
                                </div>
                            </div>
                        </button>
                    </div>

                        <!-- Stripe Elements Placeholder -->

                        <!-- <button id="card-button" :data-secret="$page.props.intent.client_secret"> -->
                            <!-- Update Payment Method -->
                        <!-- </button> -->
                    {{ $t('billing.payment') }}
                    <div class="bg-white overflow-hidden shadow-md border border-gray-200 sm:rounded-lg mb-10 p-4 flex flex-col">
                        <el-input
                            :placeholder="$t('billing.promotion_code')"
                            v-model="promotionCode"
                            class="mb-6"
                            clearable>
                        </el-input>

                        <el-input
                            :placeholder="$t('billing.card_holder_name')"
                            v-model="cardHolderName"
                            id="card-holder-name"
                            clearable>
                        </el-input>

                        <div id="card-element" class="mt-6"></div>
                        <div class="text-sm text-red-600" v-if="cardError">
                            {{ cardError }}
                        </div>

                        <el-input
                            class="mt-6"
                            :placeholder="$t('billing.billing_email')"
                            v-model="billingEmail"
                            clearable>
                        </el-input>
                    </div>
                </div>
                <div class="lg:w-1/2 px-0 lg:px-4 w-full">
                    {{ $t('billing.billing') }}
                    <div class="bg-white overflow-hidden shadow-md mt-1 border border-gray-200 sm:rounded-lg mb-10 p-4 flex flex-col">
                        <div class="flex flex-row mt-4">
                            <el-radio class="mb-4" v-model="yearly" :label="false">{{ $t('billing.monthly') }}</el-radio>
                            <el-radio v-model="yearly" :label="true">{{ $t('billing.yearly') }}</el-radio>
                        </div>
                        <div class="text-sm text-gray-600 mb-4 ml-auto" v-if="yearly">
                            {{ $t('billing.achieved_discounts') }}: {{ achievedDiscounts | currency_dkk }}
                        </div>
                        <div class="text-sm text-gray-600 mb-4 ml-auto" v-else>
                            {{ $t('billing.missed_discounts') }}: {{ achievedDiscounts | currency_dkk }}
                        </div>
                        <div class="text-sm text-gray-600 mb-4">
                            {{ $t('billing.total') }}: {{ currentPrice | currency_dkk }}
                        </div>
                        <el-button type="primary" round @click="submit">{{ $t('confirm') }}</el-button>
                    </div>
                </div>
            </div>
        </div>
    </tenant-layout>
</template>

<script>
    import TenantLayout from '@/Layouts/TenantLayout'
    import {loadStripe} from '@stripe/stripe-js';

    export default {
        components: {
            TenantLayout,
        },
        computed: {
            achievedDiscounts: function() {
                return ((this.chosenPlan.price * this.$page.props.user_count)  * 12) * 0.10;
            },
            currentPrice: function() {
                let userSum = this.chosenPlan.price * this.$page.props.user_count;
                let yearly = 0.00;
                if (this.yearly) {
                    userSum = userSum * 12;
                    yearly = 0.10;
                }
                let discountAmount = userSum * yearly
                return userSum - discountAmount;
            },
        },
        data: function() {
            return {
                cardError: "",
                stripe: null,
                stipeElements: null,
                chosenPlan: {},
                cardHolderName: "",
                billingEmail: "",
                promotionCode: "",
                yearly: true,
            }
        },
        methods: {
            submit: async function() {
                const { setupIntent, error } = await this.stripe.confirmCardSetup(
                    this.$page.props.intent.client_secret, {
                        payment_method: {
                            card: this.stripeCardElement,
                            billing_details: {
                                name: this.cardHolderName,
                            }
                        }
                    }
                )

                if (error) {
                    this.cardError = error.message;
                    return;
                }

                const values = {
                    plan_id: this.yearly ? this.chosenPlan.yearly : this.chosenPlan.id,
                    promo_code: this.promotionCode,
                    payment_method: setupIntent.payment_method,
                    billing_email: this.billingEmail,
                };

                this.$inertia.post('/billing', values);
            },
        },
        mounted: async function() {
            this.$page.props.plans.forEach(plan => {
                this.chosenPlan = plan;
            });
            this.stripe = await loadStripe(this.$page.props.stripe_key);
            this.stripeElements = this.stripe.elements();

            var style = {
                base: {
                    color: '#32325d',
                    fontFamily: 'inherit',
                    fontSmoothing: 'antialiased',
                    '::placeholder': {
                    color: '#C0C4CC'
                    }
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                }
            };

            // Add an instance of the card Element into the `card-element` <div>.
            this.stripeCardElement = this.stripeElements.create('card', {style})
            this.stripeCardElement.mount('#card-element');
        },
    }
</script>

<style scoped>
.StripeElement {
  box-sizing: border-box;

  height: 40px;

  padding: 10px 12px;

  border: 1px solid #DCDFE6;
  border-radius: 4px;
  background-color: white;
}


.StripeElement--invalid {
  border-color: #fa755a;
}

.StripeElement--webkit-autofill {
  background-color: #fefde5 !important;
}
</style>
