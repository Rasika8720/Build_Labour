<template>
	<div class="container">
		<p>Exports</p>
		<small>This page export by csv as default.</small>
		<form action="/all/export" method="GET">
			<div class="form-group">
				<label>Export By</label>
			</div>
			<div class="form-check">
				<label class="form-check-label" for="radio1">
					<input type="radio" class="form-check-input" name="exportBy" value="jobs" checked>Jobs
				</label>
			</div>
			<div class="form-check">
				<label class="form-check-label" for="radio1">
					<input type="radio" class="form-check-input" name="exportBy" value="workers" checked>Workers
				</label>
			</div>
			<div class="form-check">
				<label class="form-check-label" for="radio1">
					<input type="radio" class="form-check-input" name="exportBy" value="companies" checked>Companies
				</label>
			</div>

			<button type="submit" class="btn btn-md btn-primary">Export</button>
		</form>
	</div>
</template>

<script>

	export default {
		name: "export",
		data() {
			return {
			}
		},
		props: {

		},

		created() {

		},
		computed: {

		}
	}
</script>

<style scoped></style>
