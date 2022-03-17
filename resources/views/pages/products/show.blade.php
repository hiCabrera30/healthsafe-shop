@extends('pages.products.layout')

@section('page_title', __("pages.title", ["title" => "Products"]))
@section('header')
    <h1 class="text-white">Products</h1>
@endsection

@section('content')
    <div class="container mt-5">
        
        <product-form inline-template :product='@json($product)'>
            <div class="row">
                <div class="col-lg-9">
                    <card class="mb-5">
                        <template slot="body">
                            <form @submit.prevent="save">
                                <template>
                                    <div v-if="!editable" @click="toggleEdit" class="btn btn-link btn-sm text-blue float-right">
                                        <i class="fa fa-edit"></i> Edit
                                    </div>
                                    <div v-if="editable && !form.isRequesting" @click="toggleEdit" class="btn btn-link btn-sm text-danger float-right">
                                        <i class="fa fa-ban"></i> Cancel
                                    </div>
                                </template>
                                
                                <div v-if="editable" class="row">
                                    <h6 class="heading-small text-muted col-12">Product Information</h6>
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
                                    <div class="col-sm-12">
                                        <button v-if="!form.isRequesting" type="submit" class="btn btn-primary btn-rounded long float-right">
                                            <i class="fa fa-save mr-1"></i> Save
                                        </button>
                                        <button v-else disabled type="button" class="disabled btn btn-primary btn-rounded long float-right">
                                            <i class="fas fa-spin fa-spinner mr-2"></i> Please wait
                                        </button>
                                    </div>
                                </div>
                                <div v-else class="row">
                                    <h6 class="heading-small text-muted col-12">Product Information</h6>
                                    <labeled-data class="col-lg-12 mb-2" label="Name" :value="form.name" empty-value="No name given"></labeled-data>
                                    <labeled-data class="col-lg-12 mb-2" label="Description" :value="form.description" empty-value="No description given"></labeled-data>
                                    <labeled-data class="col-lg-12 mb-2" label="Price" :value="form.displayed_price" empty-value="No price given"></labeled-data>
                                    <labeled-data class="col-lg-12 mb-2" label="Stock" :value="form.stock" empty-value="No stocks available"></labeled-data>
                                    <div class="col-12 mt-4 text-right">
                                        <button type="button" class="btn btn-danger" @click="deleteProduct">
                                            Delete product <i class="fa fa-trash ml-2"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </template>
                    </card>
                </div>
                <div class="col-lg-3">
                    <avatar-uploader cardless
                        v-model="imageForm.image"
                        :size="{width:220, height:220}"
                        :rounded="false"
                        class="mt-5"
                        @change="updateImage"
                    ></avatar-uploader>
                </div>
            </div>
    </product-form>
    </div>
@endsection
