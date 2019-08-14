<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body table-responsive" v-if="list">
                        <!--<div class="table-responsive">-->
                            <table class="table">
                                <thead>
                                    <tr>
                                        <td>№</td>
                                        <td>Ссылка</td>
                                        <td>Короткая ссылка</td>
                                        <td>Дата создания</td>
                                        <td>Действия</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in list">
                                        <td class="align-baseline">{{setIndex(index)}}</td>
                                        <td class="align-baseline">
                                            <a :href="'https://' + item.url">
                                                {{item.url}}
                                            </a>
                                        </td>
                                        <td class="align-baseline">
                                            <a :href="item.short_url">
                                                {{item.short_url}}
                                            </a>
                                        </td>
                                        <td class="align-baseline">{{item.created_at}}</td>
                                        <td class="align-baseline">
                                            <a :href="'/d/' + item.short_url" class="btn btn-primary" style="border-radius: 4px 0px 0px 4px">Статистика</a>
                                            <button v-on:click="deleteLink(item.id, index)" class="btn btn-danger" type="button" style="border-radius: 0px 4px 4px 0px">Удалить</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        <!--</div>-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                list: ''
            }
        },
        mounted() {
            console.log('ListComponent mounted.');
            this.getList();
        },
        methods: {
            getList: function () {
                axios.get('/get-list').then((response) => {
                    if (response.data.list) {
                        this.list = response.data.list
                    }
                }).catch(error => {
                    // here catch error messages from laravel validator and show them
                });
            },
            setIndex: function (index) {
                return index + 1;
            },
            deleteLink: function (id, index) {
                if (confirm("Вы действительно хотите удалить данную ссылку?")) {
                    axios.delete('/delete-url/' + id).then((response) => {
                        this.list.splice(index, 1);
                    }).catch(error => {
                        // here catch error messages from laravel validator and show them
                        alert("Could not delete company");
                    });
                }
            }
        }
    }
</script>