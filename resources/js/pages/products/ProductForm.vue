<script>
import { redirect } from 'utils/redirects';
import { confirm, promptSuccess, promptErrors, promptFormErrors } from 'utils/prompts';
import Form from 'models/components/Form';
import Cart from 'models/Cart';

export default {
    name: 'ProductForm',

    data() {
        return {
            cart: new Cart(),
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
        this.cart.removeProduct(1)
    }
}
</script>