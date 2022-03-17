<template>
    <div class="w-100">
        <h3>Products</h3>
        <paginated-table ref="table" :fetch-link="fetchLink" paginate-path="data.result.products" has-custom-rows >
            <template slot="table-forms" slot-scope="props">
                <slot name="forms" :table="props.table"></slot>
            </template>
            <template slot="head">
                <th></th>
                <th>Product name</th>
                <th>Price</th>
                <th></th>
            </template>
            <template slot="body" slot-scope="props">
                <tr is="product-list-item" :product="props.record" @delete="deleteProduct" @addtocart="addToCart" :cart="cart" ></tr>
            </template>
        </paginated-table>
    </div>
</template>

<script>
import { confirm, promptSuccess, promptErrors } from 'utils/prompts';
import Cart from 'models/Cart';
import ProductListItem from './ProductListItem';

export default {
    name: 'ProductList',

    components: { ProductListItem },

    data() {
        return {
            cart: new Cart(),
        }
    },

    methods: {

        async deleteProduct(id) {
            const isConfirmed = (await confirm("Are you sure you want to delete this product?", "")).value;
            if (!isConfirmed) return;

            this.form.send("delete", route('api.products.destroy', id))
                .then(async response => {
                    await promptSuccess(response.data.message);
                    this.$refs.table.refreshPage();
                    this.cart.removeProduct(id);
                })
                .catch(error => promptErrors(error.response.data.message));
        },

        addToCart(data) {
            const product = this.cart.getProductInCart(data.id);
            const totalAmount = data.amount + (product ? product.amount : 0);
            
            if (totalAmount > data.stock) {
                promptErrors
            }
                
            this.cart.addProduct(data.id, data.amount);
            promptSuccess("Added product to cart")
        },
    },
    
    props: {
        fetchLink: String,
    },

    mounted() {
        // this.cart.clear();
    }
}
</script>