<template>
    <div class="w-100">
        <slot name="title" :title="displayedTitle" :start-search="startSearch" :search="search" :self="self"></slot>
        <div class="forms">
            <slot name="forms" :table="self"></slot>
        </div>
        <div class="row" :style="'min-height: ' + minHeight">
            <template v-if="fetching">
                <slot name="loading"></slot>
            </template>
            <template v-else-if="recordsEmpty">
                <slot name="empty"></slot>
            </template>
            <template v-else v-for="(record) in pagination.data">
                <slot name="body" :record="record" :mixins="self" :refresh-page="refreshPage"></slot>
            </template>
        </div>
        <template scope-slot="footer">
            <div class="float-right">
                <pagination
                    v-if="(pagination.meta.last_page || pagination.meta.last) > 1"
                    :total-pages="pagination.meta.last_page || pagination.meta.last"
                    :current-page="pagination.meta.current_page"
                    @pageChanged="pageChanged"
                ></pagination>
            </div>
        </template>
    </div>
</template>
<script>
const Pagination = () => import('./Pagination.vue');
import { promptErrors } from "utils/prompts";

export default {
    name: 'PaginatedSection',
    components: { Pagination },

    computed: {
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

        recordsEmpty() {
            return this.pagination.data && this.pagination.data.length == 0;
        },

    },

    props: {
        title: String,
        fetchLink: String,
        paginatePath: {
            type: String,
            default: "",
        },
        simplified: { type: Boolean, default: false },
        minHeight: String,
    },

    data: () => ({
        link: '',
        fetching: true,
        currentPage: 0,
        pagination: {
            meta: {}
        },
        self: {},
        search: "",
        searched: false,
        lastSearch: "",
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

        fetchData() {
            this.fetching = true;
            
            let params = this.searched ? { search: this.lastSearch = this.search  } : {};
            axios(this.link, { params })
                .then(response => this.mapData(response))
                .catch(error => this.fetchFailed(error))
                .finally(() => this.fetching = false);
        },

        fetchFailed(error) {
            this.$emit('fetch-failed', error);
            promptErrors(error.response.data.message);
            this.pagination = { data: [] };
        },

        mapData(response) {
            this.pagination =this.getPaginationFromPath(response);
            this.currentPage = this.pagination.meta.current_page;
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
                let page = this.pagination.meta.current_page - 1;
                this.link = this.pagination.meta.path +
                    '?page=' + (page <= 0 ? 1 : page);
            }
            this.fetchData();
        },
        pageChanged(page){
            this.link = this.pagination.meta.path + '?page=' + page;
            this.fetchData();
        },

    },


    mounted() {
        this.self = this;
        this.link = this.fetchLink;
        this.fetchData();
    },
}
</script>
