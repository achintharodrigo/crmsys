<template>
    <v-app id="inspire">
        <v-card>
            <v-card-title>
                <v-text-field
                    v-model="search"
                    append-icon="mdi-magnify"
                    label="Search"
                    single-line
                    hide-details
                ></v-text-field>
            </v-card-title>
            <v-data-table
                :headers="headers"
                :items="customers"
                :search="search"
                :options.sync="options"
                sort-by="first_name"
                :server-items-length="totalCustomers"
                :loading="loading"
                class="elevation-1"
            >
                <template v-slot:top>
                    <v-toolbar  flat >
                        <v-toolbar-title>Customer Information</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-dialog v-model="dialog" max-width="500px" >
                            <template v-slot:activator="{ on, attrs }">
                                <v-btn color="primary" dark class="mb-2" v-bind="attrs" v-on="on"> New Customer </v-btn>
                            </template>
                            <v-card>
                                <v-card-title>
                                    <span class="text-h5">{{ formTitle }}</span>
                                </v-card-title>

                                <v-form
                                    ref="form"
                                    v-model="valid"
                                    lazy-validation
                                >
                                    <v-card-text>
                                        <v-container>
                                            <v-row>
                                                <v-col cols="12" sm="12" md="12" >
                                                    <v-text-field
                                                        v-model="editedItem.first_name"
                                                        label="First Name"
                                                        :rules="firstNameRules"
                                                        required>
                                                    </v-text-field>
                                                </v-col>
                                                <v-col cols="12" sm="12" md="12" >
                                                    <v-text-field
                                                        v-model="editedItem.last_name"
                                                        label="Last Name"
                                                        :rules="lastNameRules"
                                                        required></v-text-field>
                                                </v-col>
                                                <v-col cols="12" sm="12" md="12" >
                                                    <v-text-field
                                                        v-model="editedItem.email"
                                                        label="Email"
                                                        type="email"
                                                        :rules="emailRules"
                                                        required></v-text-field>
                                                </v-col>
                                                <v-col cols="12" sm="12" md="12" >
                                                    <v-text-field
                                                        v-model="editedItem.mobile_number"
                                                        label="Phone"
                                                        :rules="phoneRules"
                                                        required></v-text-field>
                                                </v-col>
                                            </v-row>
                                        </v-container>
                                    </v-card-text>

                                    <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn color="blue darken-1" text @click="close" >
                                            Cancel
                                        </v-btn>
                                        <v-btn
                                            :disabled="!valid"
                                            text
                                            color="blue darken-1"
                                            class="mr-4"
                                            @click="save">
                                            Save
                                        </v-btn>
                                    </v-card-actions>
                                </v-form>
                            </v-card>
                        </v-dialog>
                        <v-dialog v-model="dialogDelete" max-width="500px">
                            <v-card>
                                <v-card-title class="text-h5">Are you sure you want to delete this Customer?</v-card-title>
                                <v-card-actions>
                                    <v-spacer></v-spacer>
                                    <v-btn color="blue darken-1" text @click="closeDelete">Cancel</v-btn>
                                    <v-btn color="blue darken-1" text @click="deleteItemConfirm">OK</v-btn>
                                    <v-spacer></v-spacer>
                                </v-card-actions>
                            </v-card>
                        </v-dialog>
                    </v-toolbar>
                </template>
                <template v-slot:item.actions="{ item }">
                    <v-icon small class="mr-2" @click="editItem(item)" >
                        mdi-pencil
                    </v-icon>
                    <v-icon small @click="deleteItem(item)" >
                        mdi-delete
                    </v-icon>
                </template>
                <template v-slot:no-data>
                    No Customers found.
                </template>
            </v-data-table>
        </v-card>
    </v-app>
</template>

<script>
export default {
    name: "CustomerTableComponent",
    data: () => ({
        search: '',
        dialog: false,
        dialogDelete: false,
        headers: [
            { text: 'First Name', value: 'first_name', align: 'start' },
            { text: 'Last Name', value: 'last_name' },
            { text: 'Email', value: 'email' },
            { text: 'Phone', value: 'mobile_number' },
            { text: 'Actions', value: 'actions', sortable: false },
        ],
        customers: [],
        totalCustomers: 0,
        options: {},
        editedIndex: -1,
        editedItem: {
            first_name: '',
            last_name: '',
            email: '',
            mobile_number: '',
        },
        valid: true,
        first_name:'',
        firstNameRules: [
            v => !!v || 'First Name is Required'
        ],
        lastNameRules: [
            v => !!v || 'Last Name is Required'
        ],
        phoneRules: [
            v => !!v || 'Phone Number is Required',
            v => (v && v.length >= 10) || 'Phone Number needs to at least 10 digits',
            v => /^(?:(?:\+?1\s*(?:[.-]\s*)?)?(?:\(\s*([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9])\s*\)|([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9]))\s*(?:[.-]\s*)?)?([2-9]1[02-9]|[2-9][02-9]1|(?:\+?[0-9]\s*[0-9]*)|[2-9][02-9]{2})\s*(?:[.-]\s*)?([0-9]{4})(?:\s*(?:#|x\.?|ext\.?|extension)\s*(\d+))?$/.test(v) || 'Phone number must be valid',
        ],
        emailRules: [
            v => !!v || 'E-mail is required',
            v => /.+@.+\..+/.test(v) || 'E-mail must be valid',
        ],
        defaultItem: {
            first_name: '',
            last_name: '',
            email: '',
            mobile_number: '',
        },
    }),

    computed: {
        formTitle () {
            return this.editedIndex === -1 ? 'New Customer' : 'Edit Customer'
        },
    },
    mounted () {
        this.initialize()
    },

    watch: {
        dialog (val) {
            val || this.close()
        },
        dialogDelete (val) {
            val || this.closeDelete()
        },
        options: {
            handler () {
                this.initialize()
            },
            deep: true,
        },
        search() {
            this.initialize();
        }
    },

    created () {
        this.initialize()
    },

    methods: {
        initialize: function () {
            this.loading = true
            axios.get('api/customers/data?page=' + this.options.page + '&q=' + this.search)
                .then(function (res) {
                    this.customers = res.data.data;
                    this.totalCustomers = res.data.total
                    this.loading = false
                }.bind(this));
        },

        editItem (item) {
            this.editedIndex = this.customers.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialog = true
        },

        deleteItem (item) {
            this.editedIndex = this.customers.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialogDelete = true
        },

        deleteItemConfirm () {
            console.log(this.editedItem);
            this.customers.splice(this.editedIndex, 1)
            this.closeDelete()

            axios.delete('api/customers/' + this.editedItem.id)
            .then(response => {
                if (response.status == 200) {
                    this.$emit('customerDeleted');
                }
            })
            .catch(error => {
                console.log(error);
            });
        },

        close () {
            this.dialog = false
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            })
        },

        closeDelete () {
            this.dialogDelete = false
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            })
        },

        save () {
            let isFormValid = this.$refs.form.validate();

            if (isFormValid) {
                if (this.editedIndex > -1) {
                    Object.assign(this.customers[this.editedIndex], this.editedItem)
                    axios.put('api/customers/' + this.editedItem.id, {
                        first_name: this.editedItem.first_name,
                        last_name: this.editedItem.last_name,
                        email: this.editedItem.email,
                        mobile_number: this.editedItem.mobile_number,
                    })
                    .then(response => {
                        if (response.status == 200) {
                            this.$emit('customerUpdated');
                            this.close();
                        }
                    })
                    .catch(error => {
                        console.log(error);
                    })
                } else {
                    this.customers.push(this.editedItem)
                    axios.post('api/customers/store', {
                        first_name: this.editedItem.first_name,
                        last_name: this.editedItem.last_name,
                        email: this.editedItem.email,
                        mobile_number: this.editedItem.mobile_number,
                    })
                    .then(response => {
                        console.log(response);
                        if (response.status == 200) {
                            this.$emit('newCustomerAdded')
                            this.close()
                        }
                    })
                    .catch(error => {
                        console.log(error);
                    })
                }
            }
        },
    }
}
</script>

<style scoped>

</style>
