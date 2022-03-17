<template>
    <ul class="pagination">
        <li v-for="(page, key) in pages" :key=key
            :class="'page-item ' + (isPageActive(page.name) ? 'disabled active' : 'font-weight-bold')">
            <a class= "page-link p-2" @click="onClickPage(page.name)" v-text="page.name"></a>
        </li>
    </ul>
</template>

<script>
export default {
	name: "Pagination",
	props: {
        maxVisibleButtons: { type: Number, default: 5 },
        totalPages: { type: Number, required: true, default: 1 },
        currentPage: { type: Number, required: true, default: 1 },
    },
	computed: {
        startPage() {
            if (this.totalPages < this.maxVisibleButtons) {
                return 1;
            }

            if (this.currentPage > Math.floor(this.maxVisibleButtons / 2)){
               if ((this.totalPages - Math.floor(this.maxVisibleButtons / 2)) < this.currentPage){
                  return this.totalPages - this.maxVisibleButtons + 1;
               }
               return this.currentPage - Math.floor(this.maxVisibleButtons / 2);
            }
            else {
               return 1;
            }
        },
        pages() {
            const range = [];
            const lastVisiblePage = Math.min(this.startPage + this.maxVisibleButtons - 1, this.totalPages);
            for (let i = this.startPage; i <= lastVisiblePage; i++) {
                range.push({
                    name: i,
                    isDisabled: i === this.currentPage
                });
            }

            return range;
        },
	},
	methods: {
        onClickPage(page) {
            this.$emit('pageChanged', page);
        },
        isPageActive(page) {
            return this.currentPage === page;
        },
    },
};
</script>
