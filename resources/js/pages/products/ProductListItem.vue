<template>
    <tr class="p-2">
        <td>
            <avatar-badge :size="{width: 100, height: 100}" :rounded="false" :src="product.image"></avatar-badge>
        </td>
        <td>
            <link-button route="pages.products.show" :params="product.id" class="mb-2">
                <h3 v-text="product.name" class="text-default"></h3>
            </link-button>
        </td>
        <td v-text="product.displayed_price" class=""></td>
        <td class="add-to-cart-wrapper text-md-right">
            <div class="m-auto mr-md-0" style="width: 200px">
                <number-input-spinner
                    v-if="availableStock > 0"
                    ref="spinner"
                    :min="1"
                    :max="availableStock"
                    :step="1"
                    button-class="vnis__button bg-primary"
                    :integer-only="true"
                    v-model="amount"
                ></number-input-spinner>
            </div>
            <div class="mt-5">
                <button v-if="availableStock > 0" class="btn btn-primary" @click="addToCart">Add to cart</button>
                <button v-else class="btn btn-primary disabled">All stocks are in cart</button>
            </div>
        </td>
    </tr>
</template>

<script>
import NumberInputSpinner from 'vue-number-input-spinner'

export default {
    name: 'ProductListItem',
    components: { NumberInputSpinner },

    computed: {
        availableStock() {
            return this.product.stock - this.amountInCart;
        },
    },

    data() {
        return {
            amountInCart: 0,
            amount: 1,
        }
    },

    methods: {

        resetAmount() {
            if (this.$refs.spinner) {
                this.amount = 0;
                this.$refs.spinner.increaseNumber();
            } else {
                this.amount = 1;
            }
        },

        initializeAmountInCart() {
            const product = this.cart.getProductInCart(this.product.id);
            this.amountInCart = product ? product.amount : 0;
        },

        addToCart() {
            this.$emit("addtocart", {
                id: this.product.id,
                amount: this.amount,
                stock: this.product.stock,
            });
            this.initializeAmountInCart();
            this.resetAmount();
        },

        deleteProduct() {
            this.$emit("delete", this.product.id);
        },
    },
    
    props: {
        product: Object,
        cart: Object,
    },

    mounted() {
        this.initializeAmountInCart();
    }
}
</script>