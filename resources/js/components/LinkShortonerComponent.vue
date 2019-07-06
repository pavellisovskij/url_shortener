<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!--<form action="" method="post" enctype="multipart/form-data">-->
                    <input type="text" name="link" v-model="link">
                    <button v-on:click="shortening">Сократить</button>
                <!--</form>-->
                <div class="card">
                    <div class="card-header">Ваша короткая сссылка</div>

                    <div class="card-body">
                        <a :href="shortURL">shortoner.test/{{shortURL}}</a>
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
                }).catch(error => {
                    // here catch error messages from laravel validator and show them
                });
            }
        }
    }
</script>
