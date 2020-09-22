<template>
	<div>
		<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center bl-mb-32">
			<div class="table-title">
				<span :class="dataTitle" @click="onClickTitle">{{ title }}</span>
				<span v-if="dataTable === 'hidden'">
					<span class="fa fa-angle-right data-title ml-1 mr-1"></span>
					<span class="data-title-active">{{ subTitle }}</span>
				</span>
			</div>
			<img v-show="show_table_search" class="table-search" src="/img/icons/search.png"
                srcset="/img/icons/search@2x.png 2x, /img/icons/search@3x.png 3x"
				@click="onClickSearch">

			<input :class="dataSearch" type="text" v-model="searchText"
				@keyup="fetchData">
    	</div>
		<div :class="showWorkerCompanyFilter" v-if="modalName == 'Worker'">
			<div class="me-label">
            	Filter Workers by Company
        	</div>
			<div class="me-row">
				<div class="role-col-left">
					<select v-model="worker_company_filter" @change="fetchData">
						<option value="all">all</option>
						<option v-for="(value, index) in companies" v-bind:key="index" :value="value">{{ value }}</option>
					</select>
			</div>
        </div>
		</div>

		<view-user v-if="modalName == 'User'" :class="dataRecord"></view-user>

		<view-worker v-else-if="modalName == 'Worker'" :class="dataRecord"></view-worker>

		<view-job v-else-if="modalName == 'Job'" :class="dataRecord"></view-job>

		<view-company v-else-if="modalName == 'Company'" :class="dataRecord"></view-company>

		<view-ticket v-else-if="modalName == 'Ticket'" :class="dataRecord"></view-ticket>

		<view-job-role v-else-if="modalName == 'JobRole'" :class="dataRecord"></view-job-role>

		<div :class="dataTable">
			<div style="overflow:auto">
				<table class="table table-hover">
					<thead>
						<tr>
							<th v-for="column in columns" :key="column" @click="sortByColumn(column)" class="">
								<span v-if="column === 'created_at' && modalName == 'User'">JOINED AT</span>
								<span v-else-if="column === 'created_at' && modalName == 'Job'">DATE ADDED</span>
								<span v-else-if="column === 'title' && modalName == 'Job'">JOB TITLE</span>
								<span v-else>{{ column | columnHead }}</span>


								<span v-if="column === sortedColumn">
									<i v-if="order === 'asc'" class="fa fa-arrow-up"></i>
									<i v-else class="fa fa-arrow-down"></i>
								</span>
							</th>
						</tr>
					</thead>

					<tbody>
						<tr class="" v-if="tableData.length === 0">
							<td class="lead text-center" :colspan="columns.length + 1">No data found.</td>
						</tr>
						<tr v-for="(data, key1) in tableData" :key="key1" class="m-datatable__row" @click="onClickViewRow(data)" v-else>
							<td v-for="(value, key) in data" :key="key.id">
								<span v-if="key !== 'full_name'">{{ value }}</span>
								<span v-if="key === 'full_name'">
									<img class="table-profile-pic" :src="getProfilePic(value)"/>{{ getProfileName(value) }}
								</span>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<br>

			<nav v-if="pagination && tableData.length > 0">
				<ul class="pagination">
					<a class="page-link-2 mr-3" href="#"
						v-if="modalName == 'Job' || modalName == 'Ticket' || modalName == 'JobRole'"
						@click="onClickViewRow(null)">
							<span class="fa fa-sync"></span>Add
					</a>
					<a class="page-link-2 mr-3" href="#" @click="fetchData"><span class="fa fa-sync"></span>Refresh</a>
					<li class="page-item-2" :class="{'disabled' : currentPage === 1}">
						<a class="page-link-2" href="#" @click.prevent="changePage(currentPage - 1)">Previous</a>
					</li>
					<li v-for="page in pagesNumber" :key="page.id" class="page-item-2" :class="{'active': page == pagination.meta.current_page}">
						<a href="javascript:void(0)" @click.prevent="changePage(page)" class="page-link-2">{{ page }}</a>
					</li>
					<li class="page-item-2" :class="{'disabled': currentPage === pagination.meta.last_page }">
						<a class="page-link-2" href="#" @click.prevent="changePage(currentPage + 1)">Next</a>
					</li>
					<span class="ml-3" style="margin-top: 8px;">Displaying {{ pagination.meta.from }} - {{ pagination.meta.to }} of {{ pagination.meta.total }} entries.</span>
				</ul>
			</nav>

			<nav v-else>
				<ul class="pagination">
					<a class="page-link-2 mr-3" href="#"
						v-if="modalName == 'Job' || modalName == 'Ticket'"
						@click="onClickViewRow(null)">
							<span class="fa fa-sync"></span>Add
					</a>
				</ul>
			</nav>
		</div>
	</div>
</template>

<script>
	import ViewUser from '../admin/ViewUser';
	import ViewWorker from '../admin/ViewWorker';
	import ViewCompany from '../admin/ViewCompany';
	import ViewJob from '../admin/ViewJob';
	import ViewJobRole from '../admin/ViewJobRole';
	import ViewTicket from '../admin/ViewTicket';

	export default {
		name: "datatable",
		data() {
			return {
				dataSearch: 'hidden',
				show_table_search: true,
				dataTitle: 'data-title',
				dataTable: 'data-table',
				dataRecord: 'data-record',
				showWorkerCompanyFilter:'',
				subTitle: '',
				tableData: [],
				searchText: '',
				url: '',
				pagination: {
					meta: {
						to: 1,
						from: 1,
						last_page: 1,
					}
				},
				offset: 4,
				currentPage: 1,
				perPage: 10,
				sortedColumn: this.columns[0],
				order: 'asc',
				companies:'',
				worker_company_filter:'',
			}
		},
		props: {
			title: {
				type: String,
				required: true
			},

			fetchUrl: {
				type: String,
				required: true
			},

			columns: {
				type: Array,
				required: true
			},

			modalName: {
				type: String,
				required: true
			},
		},
		watch: {
	 		fetchUrl: {
				handler: function(fetchUrl) {
		  			this.url = fetchUrl
				},

				immediate: true
	  		}
		},
		created() {
			let vm = this;
			vm.showWorkerCompanyFilter= '';
			Bus.$on('refreshDatatable', function() {
				vm.fetchData();
			});

			Bus.$on('adminSaveChanges', function(id) {
				vm.onClickTitle();

				if (id == 0) {
					vm.changePage(vm.pagination.meta.last_page);
				}
			});

			this.fetchData();
			this.getCompanies();

			console.log(this.columns);
		},
		computed: {
			pagesNumber() {
				if (!this.pagination.meta.to) {
					return [];
				}

				let from = this.pagination.meta.current_page - this.offset;
				if (from < 1) {
					from = 1;
				}

				let to = from + (this.offset * 2);
				if (to >= this.pagination.meta.last_page) {
					to = this.pagination.meta.last_page;
				}

				let pagesArray = [];
				for (let page = from; page <= to; page++) {
					pagesArray.push(page);
				}

				return pagesArray;
			},
			totalData() {
				return (this.pagination.meta.to - this.pagination.meta.from) + 1;
			},
		},
		methods: {
			fetchData() {
			    console.log(this.url);
				let dataFetchUrl = `${this.url}?page=${this.currentPage}&column=${this.sortedColumn}&order=${this.order}&per_page=${this.perPage}&search_text=${this.searchText}&company_filter=${this.worker_company_filter}`

				axios.get(dataFetchUrl, Utils.getBearerAuth())

					.then(({ data }) => {
					    console.log(data);
							this.pagination = data;

							this.tableData = data.data;

						})
					.catch(error => this.tableData = [])
				this.showWorkerCompanyFilter= '';
				Bus.$emit('alertHide');
			},
			serialNumber(key) {
				return (this.currentPage - 1) * this.perPage + 1 + key;
			},
			changePage(pageNumber) {
				if (pageNumber != this.pagination.meta.last_page + 1 && pageNumber >= 1) {
					this.currentPage = pageNumber;

					this.fetchData();
				}
			},
			sortByColumn(column) {


				if (column === this.sortedColumn) {
					this.order = (this.order === 'asc') ? 'desc' : 'asc';

				} else {
					this.sortedColumn = column;
					this.order = 'asc';
				}

				this.fetchData();
			},
			getProfilePic(value) {
				return value.split(' ')[0];
			},
			getProfileName(value) {
				return value.split(' ').slice(1).join(' ');
			},
			onClickSearch() {
				this.dataSearch = this.dataSearch == 'hidden' ? 'data-search' : 'hidden';
			},
			onClickViewRow(data) {
				let id = 0;

				console.log(data);

				if (data != null) {
					id = data.id;

					if (this.modalName == 'User' || this.modalName == 'Worker') {
						this.subTitle = this.getProfileName(data.full_name);

					} else if (this.modalName == 'Job') {
						this.subTitle = data.title;

					} else if (this.modalName == 'Company') {
						this.subTitle = data.name;

					} else if (this.modalName == 'Ticket') {

						this.subTitle = data.ticket;

					} else if (this.modalName == 'JobRole') {

                    	this.subTitle = data.JobRole;
                	}

				} else {
					this.subTitle = 'Add New';
				}

				this.dataSearch = 'hidden';
				this.dataTitle = 'data-title';
				this.dataTable = 'hidden';
				this.dataRecord = 'data-record';

				this.showWorkerCompanyFilter= 'hidden';
				this.show_table_search = false;
				Bus.$emit('datatableView' + this.modalName, id)
			},
			onClickTitle() {
				this.dataTitle = 'data-title';
				this.dataTable = 'data-table';
				this.dataRecord = 'hidden';

				this.fetchData();
			},
			async getCompanies(){
				let vm = this;
				await axios.get('/api/v1/admin/worker/companies',  Utils.getBearerAuth())

                .then(function(response) {
                    vm.companies = response.data.data;

                }).catch(function(error) {
                    let inputErrors = Utils.handleError(error);

                    if (inputErrors) vm.errors = inputErrors;
                });
			},

		},
		filters: {
			columnHead(value) {
				return value.split('_').join(' ').toUpperCase();
			}
		},
		components: {
			ViewUser,
			ViewWorker,
			ViewCompany,
			ViewJob,
			ViewJobRole,
			ViewTicket,
		},
		destroyed(){
			this.showWorkerCompanyFilter= '';
		}
	}
</script>

<style scoped></style>
