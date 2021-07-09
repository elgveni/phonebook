<template>
    <div>
        <div class="row">
            <div class="col-md-8 mb-4">
                <button class="btn btn-success" data-toggle="modal" data-target="#addNew" @click="openModalWindow">
                    Add New user<i class="fas fa-user-plus fa-fw"></i></button>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 mb-4">
                <input type="text" v-model="keyword" placeholder="Search">
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <table class="table" v-if="Books.length > 0">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Date created</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="book in Books">
                        <td>{{ book.id }}</td>
                        <td>{{ book.name }}</td>
                        <td>{{ (book.created_at).slice(0, 10) }}</td>
                        <td>
                            <a class="btn btn-info" href="#" @click="openModalWindowPhone(book.id, book.name)">Phones</a>
                            <a class="btn btn-danger" href="#" @click="deleteUser(book.id)">Delete</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>


        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNewLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 v-show="!editMode" class="modal-title" id="addNewLabel">Add New User</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="createUser()">
                        <div class="modal-body">
                            <div class="form-group">
                                <input v-model="form.name" type="text" name="name"
                                       placeholder="Name"
                                       class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            <button v-show="!editMode" type="submit" class="btn btn-primary">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addPhone" tabindex="-1" role="dialog" aria-labelledby="addNewPhoneLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 v-show="!editPhoneMode" class="modal-title" id="addNewPhoneLabel">Add New Phone for "{{name}}"</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="createUserPhone()">
                        <div class="modal-body">
                            <div class="form-group" v-for="(phoneItem, index) in phoneItems" :key="phoneItem.index" :index="index">
                                <input type="text"
                                       name="phone[]"
                                       placeholder="+380"
                                       v-model="phoneItem.phone"
                                       v-mask = "'+380 ### ## ### ##'"
                                       class="form-control phone-input" :class="{ 'is-invalid': formPhone.errors.has('phone') }">
                                <button v-if="phoneItems.length - 1 <= index" class="btn btn-success" :class="{ disabled: phoneItem.phone.length == 0 }" @click.prevent="addItem()">
                                    +
                                </button>

                                <button v-if="(phoneItems.length - 1 >= index) && (phoneItems.length -1 != index)" class="btn btn-danger" @click.prevent="removeItem(index);">
                                    -
                                </button>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="id" v-model="id" />
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            <button v-show="!editPhoneMode" type="submit" class="btn btn-primary">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    import {mask} from 'vue-the-mask';

    export default {
        props: [
            'books'
        ],
        directives: {mask},
        data() {
            this.getResults();

            return {
                keyword: null,
                Books: [],
                editMode: false,
                editPhoneMode: false,
                id: 0,
                name: '',
                form: new Form({
                    name : ''
                }),
                formPhone: new Form({
                    phone : ''
                }),
                phoneItems: [
                    {
                        phone: '',
                    }
                ],
                errors: [],
            };
        },
        watch: {
            keyword(after, before) {
                this.getResults();
            }
        },
        methods: {
            getResults() {
                axios.get('/livesearch', { params: { keyword: this.keyword } })
                    .then(res => this.Books = res.data)
                    .catch(error => {});
            },

            deleteUser(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {

                    if (result.value) {
                        window.location = '/remove/'+id;
                    }

                })
            },

            createUser() {
                this.form.post('/add-user')
                    .then(( response ) => {
                        this.getResults();
                        $('#addNew').modal('hide');
                    });
            },

            createUserPhone() {
                const options = {
                    method: 'POST',
                    headers: { 'content-type': 'application/form-data' },
                    data: JSON.stringify({ items: this.phoneItems, id: this.id }),
                    url: '/add-user-phone',
                };

                axios(options)
                    .then( function(response) {
                        $('#addPhone').modal('hide');
                    })
                    .catch(err => console.log(err));
            },

            openModalWindow() {
                this.editMode = false;
                this.form.reset();
                $('#addNew').modal('show');
            },

            async openModalWindowPhone(id, name) {
                this.editPhoneMode = false;
                this.id = id;
                this.name = name;

                this.formPhone.reset();

                await axios.get('/get-user-phones', { params: { id: this.id } })
                    .then( (response) => {
                        this.phoneItems = [
                            {
                                phone: '',
                            }
                        ];

                        if (+(response.data.length) !== 0) {
                            this.phoneItems = response.data;
                        }
                    })
                    .catch(error => {});

                $('#addPhone').modal('show');
            },

            addItem () {
                this.phoneItems.push({
                    phone: ''
                })
            },

            removeItem (index) {
                this.phoneItems.splice(index, 1)
            }
        }
    }
</script>

<style>
    #addPhone input[name="phone[]"] {
        width: 90%;
        display: inline-block;
    }
</style>
