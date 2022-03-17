<template>
    <modal v-model="modalShown" title="Create Expense Category" ref="modal" @hidden="modalShown = false" :small="false">
        <form @submit.prevent="save" class="text-left pt-4 pb-4 container">
            <div class="row">
                <div class="col-lg-6 col-sm-12">
                    <div class="row">
                        <div class="col-sm-12">
                            <form-input
                                label="Name"
                                placeholder="Name"
                                ref="name"
                                v-model="form.name"
                            ></form-input>
                        </div>
                        <div class="col-sm-12">
                            <form-input
                                label="Description"
                                placeholder="Description"
                                type="textarea"
                                :rows="3"
                                ref="description"
                                v-model="form.description"
                            ></form-input>
                        </div>
                        <div class="col-sm-12">
                            <form-input
                                label="Price"
                                placeholder="Price"
                                type="decimal"
                                ref="price"
                                v-model="form.price"
                            ></form-input>
                        </div>
                        <div class="col-sm-12">
                            <form-input
                                label="Stock"
                                placeholder="Stock"
                                type="integer"
                                ref="stock"
                                v-model="form.stock"
                            ></form-input>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <avatar-uploader cardless
                        v-model="form.image"
                        :size="{width:220, height:220}"
                        :rounded="false"
                        class="mt-5"
                    ></avatar-uploader>
                </div>
                <div class="col-sm-12">
                    <button v-if="!form.isRequesting" type="submit" class="btn btn-primary btn-rounded long float-right">
                        <i class="fa fa-save mr-1"></i> Save
                    </button>
                    <button v-else disabled type="button" class="disabled btn btn-primary btn-rounded long float-right">
                        <i class="fas fa-spin fa-spinner mr-2"></i> Please wait
                    </button>
                </div>
            </div>
        </form>
    </modal>
</template>


<script>
import { bus } from 'utils/bus';
import { confirm, promptSuccess, promptFormErrors } from 'utils/prompts';
import Form from 'models/components/Form';

export default {
    name: 'ProductCreateForm',

    data() {
        return {
            modalShown: false,
            form: new Form({
                name: "",
                description: "",
                image: "",
                price: "",
                stock: "",
            }),
        }
    },

    methods: {

        open() {
            this.initialize();
            this.$refs.modal.open();
        },

        close() {
            this.form.reset();
            this.$refs.modal.close();
        },
        
        initialize() {
            this.form.reset();
        },

        async save() {
            const isConfirmed = (await confirm("Are you sure you want to save this data?", "")).value;
            if (!isConfirmed) return;

            this.form.send("post", route('api.products.store'))
                .then(response => this.saved(response))
                .catch(error => promptFormErrors(this.$refs, error))
        },

        saved(response) {
            promptSuccess(response.data.message);
            this.$emit("success");
            this.close();
        },
    },
    
    props: {
        product: Object,
    },

    mounted() {
        bus.$on("products.show-create", this.open);
    }
}
</script>