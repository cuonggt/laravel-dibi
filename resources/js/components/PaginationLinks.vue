<template>
    <ul class="pagination">
        <li class="paginate_button page-item previous"
            :class="{'disabled': onFirstPage || linksDisabled}">
            <a href="#"
                class="page-link"
                @click.prevent="selectPreviousPage()"
                :disabled="onFirstPage || linksDisabled">
                Prev
            </a>
        </li>

        <template v-for="element in elements">
            <li class="paginate_button page-item disabled" v-if="typeof element == 'string'">
                <a href="#" class="page-link disabled">{{ element }}</a>
            </li>

            <template v-if="Array.isArray(element)" v-for="page in element">
                <li class="paginate_button page-item"
                    :class="{'active': currentPage == page}">
                    <a href="#"
                        class="page-link"
                        @click.prevent="selectPage(page)">{{ page }}</a>
                </li>
            </template>
        </template>

        <li class="paginate_button page-item next"
            :class="{'disabled': ! hasMorePages || linksDisabled}">
            <a href="#"
                class="page-link"
                @click.prevent="selectNextPage()"
                :disabled="! hasMorePages || linksDisabled">
                Next
            </a>
        </li>
    </ul>
</template>

<script>
export default {
    props: {
        total: {
            type: Number,
            required: true
        },
        perPage: {
            default: 25,
            validator(value) {
                return value > 0;
            }
        },
        value: {
            type: Number,
            default: 1
        },
        onEachSide: {
            type: Number,
            default: 3
        },
    },

    data: () => ({ linksDisabled: false }),

    computed: {
        lastPage: function () {
            return Math.max(Math.ceil(this.total / this.perPage), 1);
        },

        currentPage: function () {
            return this.isValidPageNumber(this.value) ? Number.parseInt(this.value) : 1;
        },

        hasMorePages: function () {
            return this.currentPage < this.lastPage;
        },

        hasPages: function () {
            return this.total > 0 && (this.currentPage != 1 || this.hasMorePages);
        },

        onFirstPage: function () {
            return this.currentPage <= 1;
        },

        previousPage: function () {
            if (this.currentPage > 1) {
                return this.currentPage - 1;
            }
        },

        nextPage: function () {
            if (this.lastPage > this.currentPage) {
                return this.currentPage + 1;
            }
        },

        elements: function () {
            let elementWindow = this.makeElementWindow();

            return _.compact([
                elementWindow.first,
                Array.isArray(elementWindow.slider) ? '...' : null,
                elementWindow.slider,
                Array.isArray(elementWindow.last) ? '...' : null,
                elementWindow.last,
            ]);
        }
    },

    mounted() {
        Bus.$on('resources-loaded', () => {
            this.linksDisabled = false;
        });
    },

    methods: {
        isValidPageNumber(page) {
            return page >= 1 && Number.isInteger(page);
        },

        selectPage(page) {
            if (this.currentPage != page) {
                this.linksDisabled = true;
                this.$emit('input', page);
            }
        },

        selectPreviousPage() {
            this.selectPage(this.previousPage);
        },

        selectNextPage() {
            this.selectPage(this.nextPage);
        },

        makeElementWindow() {
            if (this.lastPage < (this.onEachSide * 2) + 6) {
                return this.getSmallSlider();
            }

            return this.getElementSlider(this.onEachSide);
        },

        getSmallSlider() {
            return {
                'first': this.getElementRange(1, this.lastPage),
                'slider': null,
                'last': null,
            };
        },

        getElementSlider(onEachSide) {
            let onBothSide = onEachSide * 2;

            if (! this.hasPages) {
                return {'first': null, 'slider': null, 'last': null};
            }

            // If the current page is very close to the beginning of the page range, we will
            // just render the beginning of the page range, followed by the last 2 of the
            // links in this list, since we will not have room to create a full slider.
            if (this.currentPage <= onBothSide) {
                return this.getSliderTooCloseToBeginning(onBothSide);
            }

            // If the current page is close to the ending of the page range we will just get
            // this first couple pages, followed by a larger window of these ending pages
            // since we're too close to the end of the list to create a full on slider.
            else if (this.currentPage > (this.lastPage - onBothSide)) {
                return this.getSliderTooCloseToEnding(onBothSide);
            }

            // If we have enough room on both sides of the current page to build a slider we
            // will surround it with both the beginning and ending caps, with this window
            // of pages in the middle providing a Google style sliding paginator setup.
            return this.getFullSlider(onEachSide);
        },

        getSliderTooCloseToBeginning(onBothSide) {
            return {
                'first': this.getElementRange(1, onBothSide + 2),
                'slider': null,
                'last': this.getFinish(),
            }
        },

        getSliderTooCloseToEnding(onBothSide) {
            let last = this.getElementRange(
                this.lastPage - (onBothSide + 2),
                this.lastPage
            );

            return {
                'first': this.getStart(),
                'slider': null,
                'last': last,
            };
        },

        getFullSlider(onEachSide) {
            return {
                'first': this.getStart(),
                'slider': this.getAdjacentElementRange(onEachSide),
                'last': this.getFinish(),
            };
        },

        getAdjacentElementRange(onEachSide) {
            return this.getElementRange(
                this.currentPage - onEachSide,
                this.currentPage + onEachSide
            );
        },

        getStart() {
            return this.getElementRange(1, 2);
        },

        getFinish() {
            return this.getElementRange(this.lastPage - 1, this.lastPage);
        },

        getElementRange(start, end) {
            return _.range(start, end + 1);
        }
    },
};
</script>
