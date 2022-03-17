<template>
    <div :class="styleClass.card">
        <div class="row justify-content-center">
            <div class="col-lg-3 order-lg-2">
                <div class="card-profile-image">
                    <div :class="styleClass.imageFrame" frame :style="inlineFrameSize" ref="frame">
                        <img :src="value" :class="styleClass.avatarImage" ref="preview">
                    </div>
                </div>
            </div>
        </div>
        <div v-if="!disabled" :class="styleClass.cardHeader">
            <button v-if="!loading || value == null || value == ''" type="button" @click="$refs.input.click()" class="btn btn-default rounded-circle text-center m-0" style="padding: 5px;">
                <i class="fa fa-pen p-2"></i>
            </button>
            <button v-else type="button" disabled class="btn btn-default rounded-circle text-center disabled m-0" style="padding: 5px;">
                <i class="fa fa-spinner fa-spin p-2"></i>
            </button>
            <input type="file" hidden ref="input" @change="imageUploaded" />
        </div>
        <div v-if="hasError" class="mt--3 mb-3 pl-3 pr-3 pt-4 text-center">
            <span class="text-danger" v-text="errorMessage"></span>
        </div>
    </div>
</template>
<script>
import Error from "models/components/Error.js";

export default {
    name: 'AvatarUploader',
    computed: {

        inlineFrameSize() {
            return {
                width: `${ this.frameSize.width }px`,
                height: `${ this.frameSize.height }px`
            };
        },

        errorMessage() {
            return this.error.getMessage();
        },

        hasError() {
            return !this.error.isEmpty();
        },

        styleClass() {
            return {
                imageFrame: {
                    'rounded-circle': this.rounded,
                    'shadow': !this.cardless,
                    'avatar-frame': true,
                    'frame': true,
                },
                avatarImage: {
                    'avatar-image': true,
                    'blur': this.loading,
                },
                card: {
                    'card': !this.cardless,
                    'card-profile': true,
                    'shadow': !this.cardless,
                },
                cardHeader: {
                    'card-header': !this.cardless,
                    'mt--5': true,
                    'p-0': true,
                    'border-0': true,
                    'shadow': !this.cardless,
                    'text-center': true,
                }
            }
        },

    },
    data: () => ({
        loading: false,
        isRequesting: false,
        frameSize: 180,
        error: new Error,
    }),
    methods: {

        initializeUpdateListener() {
            bus.$on(this.updateEvent, (payload) => {
                if (!this.$refs.preview) {
                    return;
                }
                this.$refs.preview.src = payload.path;
                this.rescaleImage();
            })
        },

        clearError() {
            this.error.clear();
        },

        imageUploaded(event) {
            if (!(event.target.files && event.target.files[0])) {
                return
            }
            this.loading = true;
            let reader = new FileReader();
            reader.onload = (e) => setTimeout(() => this.onImageLoad(e), 500);
            reader.readAsDataURL(event.target.files[0]);
        },

        onImageLoad(e) {
            setTimeout(() => this.rescaleImage(), 300);
            this.$refs.preview.src = e.target.result;
            this.$emit('input', e.target.result);
            this.$emit('change', e.target.result);
        },

        resetImageScale() {
            let image = this.$refs.preview;
            image.style.minWidth = '';
            image.style.minHeight = '';
            image.style.maxWidth = '';
            image.style.maxHeight = '';
            this.$refs.frame.style.width = this.frameSize.width + 'px';
            this.$refs.frame.style.height = this.frameSize.height + 'px';
        },

        rescaleImage() {
            this.resetImageScale();
            let image = this.$refs.preview;
            let property = image.naturalWidth > image.naturalHeight
                ? ['height', 'width', 'minWidth', 'maxHeight', "minHeight"]
                : ['width', 'height', 'minHeight', 'maxWidth', "minWidth"];
            let length = image.naturalWidth > image.naturalHeight 
                ? [image.naturalWidth, image.naturalHeight]
                : [image.naturalHeight, image.naturalWidth];

            // image.style[property[0]] = this.frameSize[property[0]];
            if (image.naturalWidth > image.naturalHeight
            && this.frameSize.width > this.frameSize.height) {
                image.style[property[1]] = this.frameSize[property[1]] + 'px';
            }
            image.style[property[3]] = (length[0] * (this.frameSize[property[1]] / length[0])) + 'px';
            image.style[property[4]] = this.frameSize[property[0]] + 'px';
            this.loading = false;
        }
        
    },
    props: {
        value: {
            default: '/images/avatar.jpg',
            type: String|Object
        },
        updateEvent: {
            default: null,
            type: String
        },
        size: Number,
        height: Number,
        width: Number,
        size: {
            default: 36,
            type: Number|Object,
        },
        rounded: {
            default: true,
            type: Boolean,
        },
        cardless: {
            default: false,
            type: Boolean,
        },
        disabled: {
            default: false,
            type: Boolean,
        },
    },

    watch: {
        value() {
            this.clearError();
        }
    },

    mounted() {
        this.frameSize = isNaN(this.size)
            ? this.size
            : {
                width: this.size,
                height: this.size,
            };
        this.resetImageScale()
        this.loading = true;
        this.$refs.preview.onload = () => {
            setTimeout(() => this.rescaleImage(), 300);
        };
        if (this.updateEvent) {
            this.initializeUpdateListener();
        }
    }
}
</script>