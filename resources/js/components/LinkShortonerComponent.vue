<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="input-group mb-3">
                    <input type="text" name="link" v-model="link" class="form-control" placeholder="Вставьте ссылку" aria-label="" aria-describedby="basic-addon1">
                    <div class="input-group-prepend">
                        <button v-on:click="shortening" class="btn btn-primary" type="button">Сократить</button>
                    </div>
                </div>

                <div class="card" v-if="shortURL">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="text-right" style="border-top: none">Короткая ссылка:</td>
                                        <td style="border-top: none">
                                            <a :href="shortURL">{{'shortoner.test/' + shortURL}}</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-right">Статистика по ссылке:</td>
                                        <td>
                                            <a :href="dashboard">{{'shortoner.test' + dashboard}}</a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
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
                shortURL: "",
                dashboard: "",
                link: "",
            }
        },
        mounted() {
            console.log('LinkShortonerComponent mounted.')
        },
        methods: {
            shortening: function () {
                axios.post('/shortening', {
                    link: this.link
                }).then((response) => {
                    this.shortURL = response.data.short_url;
                    this.dashboard = '/d/' + response.data.short_url;
                }).catch(error => {
                    // here catch error messages from laravel validator and show them
                });
            }
        }
    }
</script>