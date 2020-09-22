<template>
    <span class="field-icon">
        <img :src="imgSrc" :srcset="imgSrcSet" ref="toggleImg" @click="toggle">
    </span>
</template>

<script>
    export default {
        name: "password-eye",
        data() {
            return {
                show: false,
                imgSrc: '/img/icons/eyeshow.png',
                imgSrcSet: '/img/icons/eyeshow@2x.png 2x, /img/icons/eyeshow@3x.png 3x',
            }
        },

        props: {
            refName: {
                type: String,
                required: true
            },
        },

        methods: {

            toggle() {
                if (this.show === true) {
                    this.$refs['toggleImg'].src = '/img/icons/eyeshow.png';
                    this.$refs['toggleImg'].srcset = '/img/icons/eyeshow@2x.png 2x, /img/icons/eyeshow@3x.png 3x';
                    
                    Bus.$emit(this.refName, 'password');
                
                } else {
                    this.$refs['toggleImg'].src = '/img/icons/eyehide.png';
                    this.$refs['toggleImg'].srcset = '/img/icons/eyehide@2x.png 2x, /img/icons/eyehide@3x.png 3x';
                    
                    Bus.$emit(this.refName, 'text');
                }

                this.show = ! this.show;
            },
            
        }
    }
</script>