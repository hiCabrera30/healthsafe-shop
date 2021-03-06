<template>
    <div>
        <div class="forms">
            <slot name="forms" :table="self"></slot>
        </div>
        <div :id="id" class="table-component">
            <h2 class="table-title" v-text="displayedTitle"></h2>
            <div v-if="searchable" class="table-toolbar row">
                <div class="table-toolbar-search">
                    <i class="fa fa-search table-toolbar-search-icon"></i>
                    <input class="table-toolbar-search-field" type="text" placeholder="Search" v-model="search" @keyup.enter="startSearch()">
                </div>
            </div>
            <div class="table-wrapper">
                <table class="w-100">
                    <thead>
                        <tr>
                            <slot name="thead" :table="self"></slot>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="fetching">
                            <td :colspan="headerCount">
                                <i class="fa fa-spinner fa-spin pr-1"></i>
                                Loading data, please wait ...
                            </td>
                        </tr>
                        <tr v-else-if="recordsEmpty">
                            <td :colspan="headerCount">
                                No record was found
                            </td>
                        </tr>
                        <template v-else-if="!hasCustomRows">
                            <tr v-for="(record, key) in pagination.data" :key="key">
                                <slot name="tbody" :record="record" :table="self" :confirm="confirm"></slot>
                            </tr>
                        </template>
                        <template v-else>
                            <template v-for="record in pagination.data" >
                                <slot name="tbody" :record="record" :mixins="self" :refresh-page="refreshPage"></slot>
                            </template>
                        </template>
                    </tbody>
                </table>
            </div>
            
            <div class="table-pagination text-sm-center">
                <data-table-pagination
                    v-if="(pagination.meta.last_page || pagination.meta.last) > 1"
                    :simplified="simplified"
                    :total-pages="pagination.meta.last_page || pagination.meta.last"
                    :total="pagination.meta.total"
                    :current-page="pagination.meta.current_page"
                    @page-changed="pageChanged">
                </data-table-pagination>
            </div>
        </div>
    </div>
</template>
<script>
import { default as DataTablePagination } from "./DataTablePagination.vue";
import { get } from "utils/network";
import { confirm, promptErrors } from "utils/prompts";

export default {
    name: 'DataTable',

    components: { DataTablePagination },
    
    computed: {
        id() {
            return `data-table-${ this._uid }`
        },

        hasSelected() {
            return this.selectedData.length > 0;
        },

        displayedTitle() {
            return this.searched ? `Search results for "${ this.lastSearch }"` : this.title;
        },

        displayedPages() {
            let pages = [];
            for (let page = 1; page <= (pagination.meta.last_page || pagination.meta.last); page++) {
                pages.push(this.formatPageData(page));
            }

            return pages;
        },

        headerCount() {
            return (this.$slots.thead ? this.$slots.thead.length : 1);
        },

        recordsEmpty() {
            return this.pagination.data && this.pagination.data.length == 0;
        },

        confirm() {
            return confirm;
        },

        

    },

    data: () => ({
        link: '',
        fetching: true,
        currentPage: 0,
        pagination: { data: [], meta: {} },
        self: {},
        search: "",
        searched: false,
        lastSearch: "",
        filters: {},
        selectedData: [],
        sortField: {},
    }),

    methods: {
        getPageClass(isActive) {
            return {
                'active': isActive,
                'page-item': true,
            };
        },
        
        startSearch() {
            this.searched = !!this.search && this.search.length > 0
            this.link = this.fetchLink;
            this.fetchData();
        },

        formatPageData(pageNumber) {
            return {
                active: this.currentPage == pageNumber,
                link: `${ this.pagination.meta.path }?page=${ pageNumber }`,
                name: pageNumber,
            };
        },

        prepareParameters() {
            const params = {
                filters: this.filters,
                sort: this.sortField,
            };
            if (this.search) {
                params.search = this.lastSearch = this.search
            }

            return params;
        },

        fetchData() {
            this.selectedData = [];
            this.fetching = true;
            let params = this.prepareParameters();
            get(this.link, { params })
                .then(response => this.mapData(response))
                .catch(error => this.fetchFailed(error))
                .finally(() => this.fetching = false);
        },

        fetchFailed(error) {
            this.$emit('fetch-failed', error);
            promptErrors(error.response.data.message);
            this.pagination = { data: [], meta: {} };
        },

        mapData(response) {
            this.pagination =this.getPaginationFromPath(response);
            this.currentPage = this.pagination.meta.current_page;
            this.$emit("pageloaded");
        },

        getPaginationFromPath(data) {
            this.paginatePath.split('.')
                .forEach(index => data = data[index]);
            return data;
        },
        
        movePage(event) {
            event.preventDefault();
            this.link = event.target.href
            this.currentPage = parseInt(event.target.dataset.page)
            this.fetchData();
        },

        refreshPage() {
            if (this.pagination.data.length <= 1) {
                this.link = this.getFetchLink(this.pagination.meta.current_page - 1);
            }
            this.fetchData();
        },

        pageChanged(page){
            this.link = this.getFetchLink(page);
            this.fetchData();
        },

        getFetchLink(page = 1) {
            return this.fetchLink 
                + (this.fetchLink.indexOf("?") == -1 ? '?page=' : '&page=')
                + (page <= 0 ? 1 : page);
        },

        reorderSort(sort) {
            this.sortField = JSON.stringify(sort.type != null ? [{...sort}] : []);
            this.refreshPage();
        },

        gotToPage(page) {
            this.link = this.getFetchLink(page);
            this.fetchData();
        }

    },

    props: {
        title: String,
        fetchLink: String,
        paginatePath: String,
        searchable: { type: Boolean, default: true },
        simplified: { type: Boolean, default: false },
        hasCustomRows: { type: Boolean, default: false },
        initialFilters: { type: Array|Object, default: () => ({}) },
    },

    mounted() {
        this.self = this;
        this.link = this.fetchLink;
        this.filters = {...this.initialFilters};
        this.fetchData();
    },
}
</script>