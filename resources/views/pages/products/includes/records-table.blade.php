<product-list fetch-link="{{ route('api.products.index') }}">

    <template slot="forms" slot-scope="props">
        <product-create-form @success="props.table.refreshPage"></product-create-form>
        <div class="text-right mt-lg-n5 mb-3">
            <link-button
                emit="products.show-create"
                class="btn btn-warning">
                <i class="fa fa-plus pr-2"></i> Register Product
            </link-button>
        </div>
    </template>

</product-list>