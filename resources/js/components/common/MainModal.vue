<template>
    <div class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content modal-ku">
                <div class="modal-header">
                    <slot name="custom-modal-title"></slot>
                </div>
                <div class="modal-body">
                    <slot name="custom-modal-content"></slot>
                </div>
                <div class="modal-footer">
                    <slot name="custom-modal-footer"></slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
	export default {
        name: "main-modal",
        data() {
			return {

            }
        },
    }
</script>

<style scoped>
    .modal-ku {
        width: 100%;
    }
</style>