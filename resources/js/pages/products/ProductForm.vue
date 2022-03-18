<script>
import { redirect } from 'utils/redirects';
import { confirm, promptSuccess, promptErrors, promptFormErrors } from 'utils/prompts';
import Form from 'models/components/Form';
import Cart from 'models/Cart';
import NumberInputSpinner from 'vue-number-input-spinner';

export default {
    name: 'ProductForm',
    components: { NumberInputSpinner },
    
    computed: {
        availableStock() {
            return this.form.stock - this.amountInCart;
        },
        hasStock() {
            return this.form.stock > 0;
        },
    },

    data() {
        return {
            cart: new Cart(),
            amountInCart: 0,
            amount: 1,
            editable: false,
            imageForm: new Form({
                image: "",
            }),
            form: new Form({
                id: "",
                name: "",
                description: "",
                price: "",
                stock: "",
            }),
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
            const product = this.cart.getProductInCart(this.form.id);
            this.amountInCart = product ? product.amount : 0;
        },

        addToCart() {
            const product = this.cart.getProductInCart(this.product.id);
            const totalAmount = this.amount + (product ? product.amount : 0);
            


            if (totalAmount > this.product.stock) {
                this.initializeAmountInCart();
                return promptErrors("Product has invalid number of stocks");
            }
                
            this.cart.addProduct(this.product.id, this.amount);
            promptSuccess("Added product to cart");
            this.initializeAmountInCart();
            this.resetAmount();
        },

        initializeProduct(product) {
            this.form.id = product.id;
            this.form.name = product.name;
            this.form.description = product.description;
            this.form.price = product.price;
            this.form.stock = product.stock;
            this.form.displayed_price = product.displayed_price;

            this.imageForm.image = product.image;
        },

        toggleEdit() {
            (this.editable = !this.editable)
                ? this.form.commit()
                : this.form.reset();
        },

        async deleteProduct() {
            const isConfirmed = (await confirm("Are you sure you want to delete this product?", "")).value;
            if (!isConfirmed) return;

            this.form.send("delete", route('api.products.destroy', this.form.id))
                .then(async response => {
                    await promptSuccess(response.data.message);
                    this.cart.removeProduct(this.form.id);
                    redirect(route("pages.products.index"));
                })
                .catch(error => this.failedToSave(error))
        },

        async save() {
            const isConfirmed = (await confirm("Are you sure you want to save this data?", "")).value;
            if (!isConfirmed) return;

            this.form.send("put", route('api.products.update', this.form.id))
                .then(response => this.saved(response))
                .catch(error => promptErrors(error.response.data.message))
        },

        saved(response) {
            promptSuccess(response.data.message);
            this.initializeProduct(response.data.result.product);
            this.editable = false;
        },

        failedToSave(error) {
            promptFormErrors(this.$refs, error);
        },
        
        updateImage() {
            const url = route("api.products.update-image", this.form.id)
            this.imageForm.send("patch", url)
                .then(response => promptSuccess(response.data.message))
                .catch(error => {
                    promptErrors(error.response.data.message);
                    this.displayImage.reset();
                });
        },
    },
    
    props: {
        product: Object,
    },

    mounted() {
        this.initializeProduct(this.product);
        this.initializeAmountInCart();
    }
}
</script>