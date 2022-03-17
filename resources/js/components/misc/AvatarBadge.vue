<template>
    <span :class="styleClass.frameWrapper">
        <div :class="styleClass.avatarFrame" ref="frame">
            <img :src="src" :class="styleClass.avatarImage" ref="preview">
        </div>
    </span>
</template>
<script>
import { bus } from 'utils/bus';

export default {
    name: 'AvatarBadge',
    computed: {
        styleClass() {
            return {
                frameWrapper: {
                    'rounded-circle': this.rounded,
                    'avatar-sm': true,
                },
                avatarFrame: {
                    'rounded-circle': this.rounded,
                    'avatar-frame': true,
                    'cardless': true,
                },
                avatarImage: {
                    'avatar-image': true,
                    'blur': this.loading,
                },
            }
        },
    },
    data: () => ({
        loading: false,
        frameSize: 36,
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
        size: {
            default: 36,
            type: Number|Object,
        },
        rounded: {
            default: true,
            type: Boolean,
        },
        src: {
            default: '/user-default.png',
            type: String|Object
        },
        updateEvent: {
            default: null,
            type: String
        },
    },

    mounted() {
        this.frameSize = isNaN(this.size) ? this.size : { width: this.size, height: this.size };
        this.resetImageScale()

        this.loading = true;
        this.$refs.preview.onload = () => setTimeout(() => this.rescaleImage(), 300);

        if (this.updateEvent) {
            this.initializeUpdateListener();
        }
    }
    
}
</script>