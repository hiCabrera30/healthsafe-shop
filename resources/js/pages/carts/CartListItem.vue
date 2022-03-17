<template>
    <tr class="line-separated">
        <td>
            <avatar-badge :size="{width: 100, height: 100}" :rounded="false" :src="product.image"></avatar-badge>
        </td>
        <td>
            <link-button route="pages.products.show" :params="product.id" class="mb-2">
                <span v-text="product.name" class="text-default"></span>
            </link-button>
        </td>
        <td class="font-weight-bold text-center" v-text="product.displayed_price"></td>
        <td class="text-center">
            <div class="m-auto" style="width: 150px">
                <number-input-spinner
                    ref="spinner"
                    :min="1"
                    :max="product.stock"
                    :step="1"
                    button-class="vnis__button bg-primary"
                    :integer-only="true"
                    v-model="amount"
                    @input="amountChange"
                ></number-input-spinner>
            </div>
        </td>
        <td class="font-weight-bold text-center" v-text="displayedTotalPrice"></td>
        <td class="text-center">
            <button class="btn btn-primary" @click="removeFromCart">Remove from cart</button>
        </td>
    </tr>
</template>
<script>
import NumberInputSpinner from 'vue-number-input-spinner';

export default {
    name: 'CartListItem',
    
    components: { NumberInputSpinner },

    computed: {
        totalPrice() {
            return this.product.price * this.amount;
        },
        displayedTotalPrice() {
            return `${ this.currencySign } ${ parseFloat(this.totalPrice).toFixed(2) }`;
        },
    },

    data() {
        return {
            amount: this.product.amount,
        }
    },

    methods: {
        initialize() {
        },
        amountChange(newAmount) {
            this.$emit("setamount", {
                id: this.product.id,
                amount: newAmount,
            })
        },
        removeFromCart() {
            this.$emit("remove", this.product.id);
        },
    },
    
    props: {
        product: Object,
        cart: Object,
        currencySign: String,
    },

    mounted() {
        this.initialize();
    }
}
</script>