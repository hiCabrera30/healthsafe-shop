<template>
    <card class="mb-3">
        <template slot="body">
            <table class="w-100">
                <thead>
                    <tr>
                        <th></th>
                        <th>Product name</th>
                        <th class="text-center">Price</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-center">Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr is="cart-list-item"
                        v-for="cartItem in cartItems" :key="cartItem.id"
                        :product="cartItem"
                        :currency-sign="currencySign"
                        @setamount="updateProductAmount"
                        @remove="removeProduct"
                    ></tr>
                    <tr v-if="cartItems.length == 0">
                        <td colspan="6">
                            <h2 class="text-center text-muted pt-4 pb-6">No items in cart</h2>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-if="cartItems.length > 0" class="text-center p-3">
                <h4>Grand Total: <span class="font-weight-bold" v-text="displayedGrandTotal"></span></h4>
                
                <button class="btn btn-success btn-block" @click="checkout">Checkout</button>
            </div>
        </template>
    </card>
</template>
<script>
import { get } from 'utils/network';
import { redirect } from 'utils/redirects';
import { confirm, promptSuccess, promptErrors } from 'utils/prompts';
import Cart from 'models/Cart';
import CartListItem from './CartListItem';

export default {
    name: 'CartList',

    components: { CartListItem },

    computed: {
        grandTotal() {
            const totals = this.cartItems.map(item => item.price * item.amount);

            return totals.reduce((sum, a) => sum + a, 0);
        },
        displayedGrandTotal() {
            return `${this.currencySign} ${parseFloat(this.grandTotal).toFixed(2)}`;
        },
    },

    data() {
        return {
            cart: new Cart(),
            cartItems: [],
        }
    },

    methods: {
        initialize() {
            const items = this.cart.getCartData();
            const params = {
                ids: JSON.stringify(items.map(item => item.id)),
                paginate: false,
            };

            get(route("api.products.index"), { params })
                .then(response => {
                    this.cartItems = response.data.result.products.map(product => {
                        product.amount = this.cart.getProductInCart(product.id).amount;
                        return product;
                    });
                })
                .catch(() => promptErrors("Unable to fetch cart data. Please reload page."));
        },
        updateProductAmount(data) {
            this.cart.setProductAmount(data.id, data.amount);
        },

        async removeProduct(id) {
            const isConfirmed = (await confirm("Are you sure you want to remove this item from cart?", "")).value;
            if (!isConfirmed) return;

            this.cart.removeProduct(id);
            const index = this.cartItems.findIndex(item => item.id == id);
            this.cartItems.splice(index, 1);
        },

        async checkout() {
            const isConfirmed = (await confirm("Are you sure you want to checkout your cart?", "")).value;
            if (!isConfirmed) return;

            this.cart.clear();
            await promptSuccess("Successfully checked out your cart")
            redirect(route("pages.products.index"))
        },
    },
    
    props: {
        currencySign: String,
    },

    mounted() {
        this.initialize();
    }
}
</script>